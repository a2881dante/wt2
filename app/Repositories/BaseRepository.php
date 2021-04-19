<?php

namespace App\Repositories;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BaseRepository
{

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all($columns = array('*')): Collection
    {
        return $this->model->select($columns)->get();
    }

    public function find($id, $columns = array('*')): ?Model
    {
        return $this->model->select($columns)->find($id);
    }

    public function create(array $attributes): ?Model
    {
        return $this->model->create($attributes);
    }

    public function updateById($id, array $input)
    {
        return $this->model->where('id', $id)->update($input);
    }

    public function destroy($ids)
    {
        return $this->model->destroy($ids);
    }

    public function destroyAll()
    {
        return $this->model->delete();
    }

}