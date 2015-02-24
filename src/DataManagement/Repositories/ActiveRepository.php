<?php

namespace Depotwarehouse\Toolbox\DataManagement\Repositories;

use Depotwarehouse\Toolbox\DataManagement\Configuration;
use Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel;

interface ActiveRepository
{

    /**
     * Returns all instances of the model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();

    /**
     * Finds specific instances a model by it's primary key.
     *
     * @param  $id string|int
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find($id);

    /**
     * Creates a new instance of the model based on the array of attributes passed in.
     *
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Depotwarehouse\Toolbox\DataManagement\Validators\ValidationException
     */
    public function create(array $attributes);


    /**
     * Updates a model with the given IDs using the array of attributes passed in.
     *
     * If no attributes are passed in the model will be "touched" (updated_at set to now).
     *
     * The model must exist, and the new set of attributes must be valid, otherwise we will throw.
     *
     * @param  string|int  $id           A unique identifier of the model
     * @param  array       $attributes   The properties of the model to update as a key-value array
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Depotwarehouse\Toolbox\DataManagement\Validators\ValidationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update($id, array $attributes = array());

    /**
     * Destroys a particular or set of models.
     *
     * Arguments are either a single ID or an array of IDs
     * @param int[]|string[]|int|string $ids
     * @return int The number of records deleted
     */
    public function destroy($ids);


}
