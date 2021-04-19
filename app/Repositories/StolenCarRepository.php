<?php


namespace App\Repositories;


use Illuminate\Database\Eloquent\Model;

use App\Model\StolenCar;
use App\Util\VpicApi;

class StolenCarRepository extends BaseRepository
{
    public function __construct(StolenCar $model)
    {
        parent::__construct($model);
    }

    public function create(array $attributes): ?Model
    {
        return parent::create(self::getAttributesWithDecodedVin($attributes));
    }

    public function updateById($id, array $input)
    {
        $attrs = !empty($input['vin']) ?  self::getAttributesWithDecodedVin($input) : $input;
        return parent::updateById($id, $attrs);
    }

    public function getWithFilterQuery($input)
    {
        $query = $this->model->query();
        $query->join('car_models', 'car_models.id', '=', 'stolen_cars.model_id');
        $query->join('car_makes', 'car_makes.id', '=', 'stolen_cars.make_id');
        if(!empty($input['make'])) {
            $query->where('make_id', $input['make']);
        }
        if(!empty($input['model'])) {
            $query->where('model_id', $input['model']);
        }
        if(!empty($input['year'])) {
            $query->where('year', $input['year']);
        }
        if(!empty($input['search'])) {
            $query->whereRaw("MATCH(full_name, car_number, vin_code) AGAINST(? IN BOOLEAN MODE)"
                , [ $input['search_str'] ]);
        }
        if(!empty($input['sort_by'])) {
            $sortOrder = $input['sort_order'] ?? 'asc';

            switch ($input['sort_by']) {
                case 'full_name':
                case 'car_number':
                case 'color':
                case 'vin':
                case 'year':
                    $query->orderBy($input['sort_by'], $sortOrder);
                    break;
                case 'make':
                    $query->orderBy('car_makes.name', $sortOrder);
                    break;
                case 'model':
                    $query->orderBy('car_models.name', $sortOrder);
                    break;
            }
        }
        $query->select([
            'full_name',
            'car_number',
            'vin',
            'car_makes.name as make',
            'car_models.name as model',
            'year',
            'color',
        ]);
        return $query;
    }

    private function getAttributesWithDecodedVin(array $attributes)
    {
        $vpicApi = new VpicApi();
        $carDetailResponse = $vpicApi->decodeVIN($attributes['vin']);
        if($carDetailResponse['status'] == 'success') {
            $carDetail = $carDetailResponse['result'];
            $attributes['make_id'] = $carDetail['Results'][0]['MakeID'];
            $attributes['model_id'] = $carDetail['Results'][0]['ModelID'];
            $attributes['year'] = $carDetail['Results'][0]['ModelYear'];
        } else {
            return $carDetailResponse;
        }
        return $attributes;
    }
}