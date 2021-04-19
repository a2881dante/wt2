<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $table = 'car_models';

    protected $fillable = [
        'id',
        'make_id',
        'name',
    ];

    public $timestamps = false;

    public function car_model()
    {
        return $this->belongsTo(CarMake::class);
    }
}
