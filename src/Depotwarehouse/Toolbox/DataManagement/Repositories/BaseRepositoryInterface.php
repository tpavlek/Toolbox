<?php
/**
 * Created by PhpStorm.
 * User: ebon
 * Date: 7/29/14
 * Time: 4:19 AM
 */

namespace Depotwarehouse\Toolbox\DataManagement\Repositories;



interface BaseRepositoryInterface {

    /**
     * Returns all instances of the model
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all();

    public function filter($filters = array());

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

    public function getSearchableFields();

    /**
     * @return \Illuminate\Pagination\Paginator
     */
    public function paginate();

} 