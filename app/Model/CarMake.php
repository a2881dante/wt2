<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class CarMake extends Model
{
    protected $table = 'car_makes';

    protected $fillable = [
        'id',
        'name',
    ];

    public $timestamps = false;

    public function models()
    {
        return $this->hasMany(CarModel::class, 'make_id', 'id');
    }
}
