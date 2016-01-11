<?php

namespace Depotwarehouse\Toolbox\DataManagement\Repositories;

use Depotwarehouse\Toolbox\DataManagement\Validation\Validator;
use Illuminate\Database\Eloquent\Model;

class ActiveRepositoryAbstract implements ActiveRepository
{

    /** @var Model  */
    protected $model;

    /**
     * The validator we use to ensure all data is consistent.
     * @var Validator
     */
    protected $validator;

    public function __construct(Model $model, Validator $validator)
    {
        $this->model = $model;
        $this->validator = $validator;
    }

    /**
     * Returns all instances of the model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
       return $this->model->all();
    }

    /**
     * Paginate the results of the model.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate()
    {
        return $this->model->paginate();
    }

    /**
     * Finds specific instances a model by it's primary key.
     *
     * @param  $id string|int
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Creates a new instance of the model based on the array of attributes passed in.
     *
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Depotwarehouse\Toolbox\DataManagement\Validation\ValidationException
     */
    public function create(array $attributes)
    {
        $this->validator->validate($attributes);

        return $this->model->create($attributes);
    }

    /**
     * Updates a model with the given IDs using the array of attributes passed in.
     *
     * If no attributes are passed in the model will be "touched" (updated_at set to now).
     *
     * The model must exist, and the new set of attributes must be valid, otherwise we will throw.
     *
     * @param  string|int $id A unique identifier of the model
     * @param  array $attributes The properties of the model to update as a key-value array
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Depotwarehouse\Toolbox\DataManagement\Validation\ValidationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update($id, array $attributes = array())
    {
        $model = $this->find($id);

        $this->validator->updateValidate($attributes, $id);

        $model->update($attributes);

        return $model;
    }

    /**
     * Destroys a particular or set of models.
     *
     * Arguments are either a single ID or an array of IDs
     * @param int[]|string[]|int|string $ids
     * @return int The number of records deleted
     */
    public function destroy($ids)
    {
        $this->model->destroy($ids);
    }

    /**
     * Get fillable fields for model
     * @return array
     */
    public function getFillableFields()
    {
        return $this->model->getFillable();
    }

}
