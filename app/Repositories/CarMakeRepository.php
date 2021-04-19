<?php

namespace App\Repositories;


use Illuminate\Database\Eloquent\Model;

use App\Model\CarMake;

class CarMakeRepository extends BaseRepository
{
    public function __construct(CarMake $model)
    {
        parent::__construct($model);
    }

    public function getWithModels($partname)
    {
        return $this->model->with('models')
            ->where('name', 'like', "{$partname}%")
            ->get();
    }
}