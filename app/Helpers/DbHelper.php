<?php

namespace App\Helpers;


use Illuminate\Support\Facades\DB;

use App\Util\VpicApi;

class DbHelper
{
    public static function updateMakesAndModelsTables()
    {
        $vpicApi = new VpicApi();
        $makesResponse = $vpicApi->getMakes();
        if ($makesResponse['status'] == 'success') {
            $makes = $makesResponse['result'];
            DB::table('car_makes')->upsert(collect($makes['Results'])->map(function($item){
                return [
                    'id'    => $item['Make_ID'],
                    'name'  => $item['Make_Name'],
                ];
            })->toArray(), ['id'], ['name']);

            foreach ($makes['Results'] as $make) {
                self::updateModelsTable($make['Make_ID']);
            }
        }
    }

    protected static function updateModelsTable($makeId)
    {
        $vpicApi = new VpicApi();
        $modelsResponse = $vpicApi->getModels($makeId);
        if($modelsResponse['status'] == 'success') {
            $models = $modelsResponse['result'];
            DB::table('car_models')->upsert(collect($models['Results'])->map(function($item){
                return [
                    'id'        => $item['Model_ID'],
                    'name'      => $item['Model_Name'],
                    'make_id'   => $item['Make_ID'],
                ];
            })->toArray(), ['id'], ['name', 'make_id']);
        }
    }
}