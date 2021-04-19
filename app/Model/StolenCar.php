<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class StolenCar extends Model
{
    protected $table = 'stolen_cars';

    protected $fillable = [
        'id',
        'full_name',
        'car_number',
        'color',
        'vin',
        'make_id',
        'model_id',
        'year',
    ];

    public function car_make()
    {
        return $this->belongsTo(CarMake::class);
    }

    public function car_model()
    {
        return $this->belongsTo(CarModel::class);
    }
}
