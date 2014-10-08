<?php
/**
 * Created by PhpStorm.
 * User: ebon
 * Date: 7/29/14
 * Time: 4:19 AM
 */

namespace Depotwarehouse\Toolbox\DataManagement\Repositories;



use Depotwarehouse\Toolbox\DataManagement\Configuration;
use Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel;
use Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException;

interface BaseRepositoryInterface {

    /**
     * Sets the configuration of the repository.
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration);

    /**
     * Returns all instances of the model
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all();

    /**
     * Returns all items that exist, separated by pages.
     * @return \Illuminate\Pagination\Paginator
     */
    public function paginate();

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
     * @throws InvalidArgumentException Thrown if a requested relationship does not exist on the model
     */
    public function filter($filters = array(), Callable $postFilter = null);

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
    public function search(array $terms = array());

    /**
     * Finds specific instances a model by ID(s)
     * @param $id string|int  Either an integer ID or a comma separated string of IDs.
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection|static
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find($id);

    /**
     * Creates a new instance of the model based on the array of attributes passed in
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model|static
     * @throws \Depotwarehouse\Toolbox\Exceptions\ValidationException
     */
    public function create(array $attributes);


    /**
     * Updates a model with the given IDs using the array of attributes passed in.
     * If no attributes are passed in the model will be "touched" (updated_at set to now).
     * @param mixed $id unique identifier of the model
     * @param array $attributes the properties of the model to update as a key-value array
     * @return integer The status code of the outcome (either created or updated, as class constants)
     * @throws \Depotwarehouse\Toolbox\Exceptions\ValidationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update($id, array $attributes = array());

    /**
     * Destroys a particular or set of models.
     *
     * Arguments are either a single integer ID or an array of integer IDs
     * @param int[]|int $ids
     * @return int The number of records deleted
     */
    public function destroy($ids);

    /**
     * Returns the list of fields on the model that can be filled via Mass Assignment
     * @return array The list of column names that can be filled
     */
    public function getFillableFields();

    /**
     * Gets the list of columns that are possible to be updated (eg. IDs may not be updateable, etc.)
     * @return array list of updateable fields on the model
     */
    public function getUpdateableFields();

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
    public function getSearchableFields(BaseModel $model = null, $with_related = true, $current_depth = 1, &$searchable_array = array(), array &$previous_objects = array(), $requested_searchable_path = "*");



} 