<?php
/**
 * Created by PhpStorm.
 * User: ebon
 * Date: 7/29/14
 * Time: 4:19 AM
 */

namespace Depotwarehouse\Toolbox\DataManagement\Repositories;



use Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel;
use Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException;

interface BaseRepositoryInterface {

    /**
     * Returns all instances of the model
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all();

    public function filter($filters = array(), Callable $postFilter = null);

    /**
     * Searches all the searchable fields of the direct model (no related models) if they contain any of the array of terms.
     * Terms stack, eg. the function checks if any of the searchable fields match the first term AND any of the searchable fields
     * match the second term, etc.
     * @param array $terms Array of strings to search.
     * @return \Illuminate\Pagination\Paginator
     */
    public function search(array $terms = array());

    /**
     * Finds specific instances a model by ID(s)
     * @param $id string|int Either an integer ID or a comma separated string of IDs.
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection|static
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
     * @throws \Exception
     */
    public function update($id, array $attributes = array());

    /**
     * Deletes a model and it's associated record on the data store
     * @param mixed $id
     * @return mixed
     */
    public function destroy($id);

    public function getFillableFields();

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
     */
    public function getSearchableFields(BaseModel $model = null, $with_related = true, $current_depth = 1, &$searchable_array = array(), array &$previous_objects = array(), $requested_searchable_path = "*");

    /**
     * @return \Illuminate\Pagination\Paginator
     */
    public function paginate();

} 