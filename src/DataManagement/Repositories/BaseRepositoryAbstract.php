<?php

namespace Depotwarehouse\Toolbox\DataManagement\Repositories;

use Depotwarehouse\Toolbox\DataManagement\Configuration;
use Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel;
use Depotwarehouse\Toolbox\DataManagement\Validators\BaseValidatorInterface;
use Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException;
use Depotwarehouse\Toolbox\Exceptions\ValidationException;

use Depotwarehouse\Toolbox\Operations\Operation;
use Depotwarehouse\Toolbox\Operations\Operations;
use Depotwarehouse\Toolbox\Strings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Carbon\Carbon;
use Illuminate\Pagination\Factory;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

abstract class BaseRepositoryAbstract implements BaseRepositoryInterface
{

    const OBJECT_CREATED = 201;
    const OBJECT_UPDATED = 202;

    /** @var BaseModel */
    protected $model;

    /** @var \Depotwarehouse\Toolbox\DataManagement\Validators\BaseValidatorInterface */
    protected $validator;

    /** @var  \Depotwarehouse\Toolbox\DataManagement\Configuration */
    protected $configuration;

    public function __construct(BaseModel $model, BaseValidatorInterface $validator)
    {
        $this->model = $model;
        $this->validator = $validator;
    }

    /**
     * Resolves the configuration object of the class.
     *
     * In order to decouple from frameworks, configuration of this class is done through a Configuration object.
     * However, since this class is meant to be overridden, putting Configuration instantiation in the constructor
     * would require significant boilerplate on the part of the user in order to instantiate and explicitly call
     * constructors with a Configuration object.
     *
     * Rather, the user must implement the abstract method to resolve configuration. This method has a single function
     * which is to simply set $this->getConfiguration() to a Configuration object acceptable to the client.
     *
     * It is recommended that each project implement resolveConfiguration in a single BaseRepository, then have
     * all your repositories extend from that, however you are welcome to implement the function on a per-repository
     * basis
     *
     * @return void
     */
    abstract function resolveConfiguration();

    /**
     * Gets the current configuration of the repository.
     *
     * This function will first call the mandatory abstract callback to resolve the configuration. If the configuration
     * object is not set properly by resolveConfiguration, errors and exceptions will be rampant.
     *
     * @return Configuration
     */
    private function getConfiguration() {
        $this->resolveConfiguration();
        return $this->configuration;
    }

    /**
     * Returns all instances of the model
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Returns all items that exist, separated by pages.
     * @return \Illuminate\Pagination\Paginator
     */
    public function paginate()
    {
        return $this->handlePaginate($this->all());
    }

    /**
     * Paginates a collection.
     * @param Collection $items
     * @return Paginator
     */
    private function handlePaginate(Collection $items) {
        $factory = new Factory($this->getConfiguration()->pagination["page_name"]);
        if (isset($_GET[$factory->getPageName()]) and $current_page = $_GET[$factory->getPageName()]) {
            $factory->setCurrentPage($current_page);
        } else {
            $factory->setCurrentPage(1);
        }

        $paginator = $factory->make($items->all(), $items->count(), $this->getConfiguration()->pagination["per_page"]);
        return $paginator;
    }

    /**
     * Filters a list of items based on passed filters.
     *
     * The acceptable filters are anything parseable by the Operation class. The usage of this function should be
     * used primarily when there are specific attributes of a set of items that you are wishing to show eg. All users
     * who live in Edmonton.
     *
     * In constrast, the search function below should be used if you're looking for a *specific* item eg. a user named
     * Bob in Edmonton.
     *
     * For some advanced filtering techniques that aren't easily abstractable for multiple use-cases you may pass
     * an optional $postFilter callback function. The callback function will accept a single Builder argument. When
     * using this callback ensure that you do not call ->get() on the Builder, as that will be called by the filter
     * function.
     *
     * @param array $filters
     * @param callable $postFilter A callback function to be applied to the Builder before it is executed.
     * @return \Illuminate\Pagination\Paginator
     */
    public function filter($filters = array(), Callable $postFilter = null)
    {
        $items = $this->model->newQuery();
        $operations = Operations::getOperationsFromArrayOfFilters($filters);

        foreach ($operations as $operation) {
            if (!$operation->hasIncludes()) {
                $items->where($operation->key, $operation->operation, $operation->value);
                continue;
            }

            $items->whereHas($operation->pullInclude(), $this->buildIncludeFilter($operation, $items));
        }

        if ($postFilter !== null) {
            $postFilter($items);
        }

        return $this->handlePaginate($items->get());
    }

    private function buildIncludeFilter(Operation $operation, Builder &$items)
    {
        if (!$operation->hasIncludes()) {
            return function ($query) use ($operation) {
                $query->where($operation->key, $operation->operation, $operation->value);
            };
        }
        // We currently have more items left in the include path, so we'll recurse
        return function ($query) use ($operation, $items) {
            $query->whereHas($operation->pullInclude(), $this->buildIncludeFilter($operation, $items));
        };
    }

    /**
     * Searches all the searchable fields of the direct model (no related models) if they contain any of the array of terms.
     *
     * Terms stack, eg. the function checks if any of the searchable fields match the first term AND any of the searchable fields
     * match the second term, etc.
     *
     * These terms are only values eg an array of terms might take the form
     * ```.language-php
     * [
     *      'troy',
     *      'pavlek'
     * ]
     * ```
     * @param array $terms Array of strings to search.
     * @return \Illuminate\Pagination\Paginator
     */
    public function search(array $terms = array())
    {
        if (count($terms) == 0) {
            return $this->paginate();
        }

        $searchable_fields = $this->getSearchableFields(false);

        $items = $this->model->newQuery();

        foreach ($terms as $term) {
            $items->where(function ($query) use ($searchable_fields, $term) {
                foreach ($searchable_fields as $searchable_field) {
                    $query->orWhere($searchable_field, 'LIKE', '%' . $term . '%');
                }
            });
        }
        return $items->paginate(Config::get('pagination.per_page'));
    }

    /**
     * Finds specific instances a model by ID(s)
     * @param $id string|int  Either an integer ID or a comma separated string of IDs.
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection|static
     * @throws ModelNotFoundException
     */
    public function find($id)
    {
        $ids = Strings::parseCommaSeparatedIDs($id);
        if (is_array($ids)) {
            $items = new Collection();
            foreach ($ids as $id) {
                $items->push($this->model->findOrFail($id));
            }
            return $items;
        }

        return $this->model->findOrFail($id);
    }

    /**
     * Creates a new instance of the model based on the array of attributes passed in
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model|static
     * @throws \Depotwarehouse\Toolbox\Exceptions\ValidationException
     */
    public function create(array $attributes)
    {
        // Throws a ValidationException if validation fails
        $this->validator->validate($attributes);

        // We're only going to pass in the explicitly fillable fields - we don't want MassAssignmentExceptions!
        $attributes = array_only($attributes, $this->getFillableFields());
        $model = $this->model->create($attributes);

        return $model;
    }


    /**
     * Updates a model with the given IDs using the array of attributes passed in.
     * If no attributes are passed in the model will be "touched" (updated_at set to now).
     * @param mixed $id unique identifier of the model
     * @param array $attributes the properties of the model to update as a key-value array
     * @return integer The status code of the outcome (either created or updated, as class constants)
     * @throws \Depotwarehouse\Toolbox\Exceptions\ValidationException
     * @throws \Exception
     */
    public function update($id, array $attributes = array())
    {
        try {
            $object = $this->find($id);

            $attributes = array_only($attributes, $this->getUpdateableFields());

            try {
                $this->validator->updateValidate($attributes);
            } catch (ValidationException $ex) {
                throw $ex;
            }

            // todo catch exceptions here?
            $object->update($attributes);
            return self::OBJECT_UPDATED;

        } catch (ModelNotFoundException $ex) {
            $this->create(array_merge(['id' => $id], $attributes));
            return self::OBJECT_CREATED;
        }


    }


    public function destroy($id)
    {
        return $this->model->destroy($id);
    }


    public function getFillableFields()
    {
        return $this->model->fillable;
    }

    /**
     * @return array list of updateable fields on the model
     */
    public function getUpdateableFields()
    {
        return $this->model->updateable;
    }

    /**
     * Constructs and returns an array of the fields by which a model can be searched.
     *
     * GetSearchableFields will return all the fields declared searchable on the current model, as well as
     * recursively searching related models and returning their searchable fields.
     *
     * @param BaseModel $model Default null. If provided, we will use that class for getting searchable fields.
     * @param bool $with_related Default true. Do we wish to recurse into related models, or just search on the current level
     * @param int $current_depth Tracks how many recursions we have gone through (to prevent infinite recursion)
     * @param array $searchable_array A reference to everything we've found thus far. This is what we return.
     * @param array $previous_objects A list of class names from objects we've already visited, to prevent cycles
     * @param string $requested_searchable_path The current path that we've included so far, listed as colon-separated keys of relatedModels
     * @return array The available searchable fields.
     * @throws InvalidArgumentException
     */
    public function getSearchableFields(BaseModel $model = null, $with_related = true, $current_depth = 1, &$searchable_array = array(), array &$previous_objects = array(), $requested_searchable_path = "*")
    {
        $model = ($model === null) ? $this->model : $model;
        $previous_objects[] = get_class($model);
        foreach ($model->searchable as $searchable_field) {

            // Gets the current path, and pops the end off it, used for constructing subsequent paths.
            $requested_searchable_path_array = explode(Operation::INCLUDE_PATH_KEY, $requested_searchable_path);
            $requested_searchable_field = array_pop($requested_searchable_path_array);

            // If we don't want related models, exclude everything with a colon
            $is_related = strpos($searchable_field, Operation::INCLUDE_PATH_KEY);


            if ($is_related !== false) {
                if (!$with_related) {
                    continue;
                }

                // If we've reached the max recursion depth, we don't want to recurse into the next model.
                if ($current_depth > $this->getConfiguration()->include["max_depth"]) {
                    continue;
                }

                $related_path_array = explode(Operation::INCLUDE_PATH_KEY, $searchable_field);
                $new_model_path = $related_path_array[0];

                try {
                    $reflection = new \ReflectionClass($new_model_path);
                    if (!$reflection->isInstantiable()) {
                        throw new InvalidArgumentException("The requested class: " . $new_model_path . " is not instantiable");
                    }

                    if (in_array($reflection->getName(), $previous_objects)) {
                        // We're about to recurse onto an object that we've already visited, skip it.
                        continue;
                    }

                    $next_model = $reflection->newInstance();
                    $requested_searchable_path_array = array_merge($requested_searchable_path_array, $related_path_array);

                    $this->getSearchableFields($next_model, true, ++$current_depth, $searchable_array, $previous_objects, implode(Operation::INCLUDE_PATH_KEY, $requested_searchable_path_array));
                    continue;
                } catch (\ReflectionException $exception) {
                    throw new InvalidArgumentException("The requested class: " . $new_model_path . " does not exist");
                }
            }


            // If the current searchable field is compatible with the requested searchable field, add it.
            if ($requested_searchable_field == "*" || $requested_searchable_field == $searchable_field) {
                // Twiddly bits, we reconstruct the path using all but the last of the requested path, and the current searchable
                $requested_searchable_path_array[] = $searchable_field;
                $requested_searchable_path_array = array_unique($requested_searchable_path_array);
                $searchable_array[] = implode(Operation::INCLUDE_PATH_KEY, $requested_searchable_path_array);
            }

            // Nothing more to do.

        }

        return array_unique($searchable_array);
    }

}