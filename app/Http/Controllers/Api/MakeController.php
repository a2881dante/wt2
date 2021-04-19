<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Repositories\CarMakeRepository;

class MakeController extends Controller
{
    protected $carMakeRepo;

    public function __construct(
        CarMakeRepository $carMakeRepo
    ) {
        $this->carMakeRepo = $carMakeRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/makes/{partname}",
     *     summary="Получить список марок авто и моделей по частичному названию",
     *     description="Получить список марок авто и моделей по частичному названию",
     *     operationId="getMakesWithModels",
     *     tags={"Car Makes"},
     *     @OA\Response(response="200", description="OK"),
     *     @OA\Parameter( ref="#/components/parameters/partname", ),
     * )
     */
    public function getWithModels($partname)
    {
        return response()->json(
            $this->carMakeRepo->getWithModels($partname)
        );
    }

    /**
     * @OA\Parameter(
     *      parameter="partname",
     *      in="path",
     *      name="partname",
     *      description="Частичное название марки авто",
     *      @OA\Schema(
     *          type="string",
     *      )
     * )
     */
}
