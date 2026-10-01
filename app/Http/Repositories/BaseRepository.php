<?php

namespace App\Http\Repositories;

class BaseRepository
{
    protected $model;

    protected $model_name = '';

    public function __construct()
    {
        $this->model = new $this->model_name;
    }

    public function create(array $inputs)
    {
        return $this->model->create($inputs);
    }

    public function insert(array $inputs)
    {
        return $this->model->insert($inputs);
    }

    public function getById($id, $column = ['*'])
    {
        return $this->model->select($column)->find($id);
    }

    public function update($id, array $inputs)
    {
        return tap($this->model->find($id))->update($inputs)->fresh();
    }

    public function delete($id)
    {
        return $this->getById($id, ['*'])->delete();
    }

    public function all()
    {
        return $this->model->all();
    }
}
