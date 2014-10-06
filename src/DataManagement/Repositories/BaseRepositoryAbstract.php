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
     * which is to simply set $this->configuration to a Configuration object acceptable to the client.
     *
     * It is recommended that each project implement resolveConfiguration in a single BaseRepository, then have
     * all your repositories extend from that, however you are welcome to implement the function on a per-repository
     * basis
     *
     * @return void
     */
    abstract function resolveConfiguration();

    /**
     * Returns all instances of the model
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * @param array $filters
     * @param callable $postFilter
     * @return \Illuminate\Pagination\Paginator
     * @throws \Depotwarehouse\Toolbox\Exceptions\ArrayEmptyException
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

        // We must make sure configuration is resolved first
        $this->resolveConfiguration();

        return $items->paginate($this->configuration->pagination['per_page']);
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
     * Terms stack, eg. the function checks if any of the searchable fields match the first term AND any of the searchable fields
     * match the second term, etc.
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

    /**
     * @return \Illuminate\Pagination\Paginator
     */
    public function paginate()
    {
        return $this->model->paginate(Config::get('pagination.per_page'));
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

    public function getSearchableFields(BaseModel $model = null, $with_related = true, $current_depth = 1, &$searchable_array = array(), $requested_searchable_path = "*")
    {
        $this->resolveConfiguration();
        $model = ($model === null) ? $this->model : $model;
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
                if ($current_depth > $this->configuration->include["max_depth"]) {
                    continue;
                }

                $related_path_array = explode(Operation::INCLUDE_PATH_KEY, $searchable_field);
                $key = $related_path_array[0];
                if (!array_key_exists($key, $model->relatedModels)) {
                    throw new InvalidArgumentException("The related models array for: " . get_class($model) . " did not contain key: " . $key);
                }

                $new_model_path = $model->relatedModels[$key];
                $reflection = new \ReflectionClass($new_model_path);
                if (!$reflection->isInstantiable()) {
                    throw new InvalidArgumentException("The requested class: " . $new_model_path . " is not instantiable");
                }

                $next_model = new $new_model_path;
                $requested_searchable_path_array = array_merge($requested_searchable_path_array, $related_path_array);

                $this->getSearchableFields($next_model, true, $current_depth++, $searchable_array, implode(Operation::INCLUDE_PATH_KEY, $requested_searchable_path_array));
                continue;
            }


            // If the current searchable field is compatible with the requested searchable field, add it.
            if ($requested_searchable_field == "*" || $requested_searchable_field == $searchable_field) {
                // Twiddly bits, we reconstruct the path using all but the last of the requested path, and the current searchable
                $requested_searchable_path_array[] = $searchable_field;
                $searchable_array[] = implode(Operation::INCLUDE_PATH_KEY, $requested_searchable_path_array);
            }

            // Nothing more to do.

        }
        return $searchable_array;
    }
}