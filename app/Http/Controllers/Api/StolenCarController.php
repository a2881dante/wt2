<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Maatwebsite\Excel\Facades\Excel;

use App\Exports\StolenCarExport;
use App\Http\Controllers\Controller;
use App\Repositories\StolenCarRepository;

class StolenCarController extends Controller
{
    const ITEMS_PER_PAGE = 30;

    protected $stolenCarRepo;

    public function __construct(
        StolenCarRepository $stolenCarRepo
    ){
        $this->stolenCarRepo = $stolenCarRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/stolen-cars/",
     *     summary="Получить список угнанных авто",
     *     description="Получить список угнанных авто",
     *     operationId="getStolenCars",
     *     tags={"Stolen Cars"},
     *     @OA\Response(response="200", description="OK"),
     *     @OA\Parameter( ref="#/components/parameters/make", ),
     *     @OA\Parameter( ref="#/components/parameters/model", ),
     *     @OA\Parameter( ref="#/components/parameters/year", ),
     *     @OA\Parameter( ref="#/components/parameters/search", ),
     *     @OA\Parameter( ref="#/components/parameters/sort_by", ),
     *     @OA\Parameter( ref="#/components/parameters/sort_order", ),
     *     @OA\Parameter( ref="#/components/parameters/page", ),
     *     @OA\Parameter( ref="#/components/parameters/pages", ),
     * )
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'make'      => 'integer|exists:car_models,id',
            'model'     => 'integer|exists:car_makes,id',
            'year'      => 'integer|min:1900',
            'search'    => 'string',
            'sort_by'   => ['string', Rule::in(['car_number', 'color', 'full_name', 'make', 'model', 'vin', 'year'])],
            'sort_order'=> ['string', Rule::in(['asc', 'desc'])],
            'page'      => 'integer|min:1',
            'pages'     => 'integer|min:5',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'result' => $validator,
            ]);
        }

        $stolenCarsQuery = $this->stolenCarRepo->getWithFilterQuery($request->toArray());
        $stolenCars = $stolenCarsQuery->paginate($request->pages ?? self::ITEMS_PER_PAGE);
        return response()->json([
            'status' => 'success',
            'result' => $stolenCars,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/stolen-cars",
     *     summary="Добавить запись об угнанном авто",
     *     description="Добавить запись об угнанном авто",
     *     operationId="storeStolenCar",
     *     tags={"Stolen Cars"},
     *     @OA\Response(response="200", description="OK"),
     *     @OA\Parameter( ref="#/components/parameters/full_name", ),
     *     @OA\Parameter( ref="#/components/parameters/car_number", ),
     *     @OA\Parameter( ref="#/components/parameters/color", ),
     *     @OA\Parameter( ref="#/components/parameters/vin", ),
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string',
            'car_number' => 'required|string|max:10',
            'color' => 'required|string',
            'vin' => 'required|string|alpha_num|size:17',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'result' => $validator,
            ]);
        }
        $stollenCar = $this->stolenCarRepo->create($request->toArray());
        return response()->json([
            'status' => 'success',
            'result' => $stollenCar,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/stolen-cars/{idCar}",
     *     summary="Редактировать запись об угнанном авто",
     *     description="Редактировать запись об угнанном авто",
     *     operationId="updateStolenCar",
     *     tags={"Stolen Cars"},
     *     @OA\Response(response="200", description="OK"),
     *     @OA\Parameter( ref="#/components/parameters/idCar", ),
     *     @OA\Parameter( ref="#/components/parameters/full_name", ),
     *     @OA\Parameter( ref="#/components/parameters/car_number", ),
     *     @OA\Parameter( ref="#/components/parameters/color", ),
     *     @OA\Parameter( ref="#/components/parameters/vin", ),
     * )
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'string',
            'car_number' => 'string|max:10',
            'color' => 'string',
            'vin' => 'string|alpha_num|size:17',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'result' => $validator,
            ]);
        }

        $stollenCar = $this->stolenCarRepo->updateById($id, $request->toArray());
        return response()->json([
            'status' => 'success',
            'result' => $stollenCar,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/stolen-cars/{idCar}",
     *     summary="Удалить запись об угнанном авто",
     *     description="Удалить запись об угнанном авто",
     *     operationId="delStolenCars",
     *     tags={"Stolen Cars"},
     *     @OA\Response(response="200", description="OK"),
     *     @OA\Parameter( ref="#/components/parameters/idCar", ),
     * )
     */
    public function destroy($id)
    {
        $this->stolenCarRepo->destroy($id);
        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/stolen-cars/export",
     *     summary="Экспорт списка угнанных авто в xls",
     *     description="Экспорт списка угнанных авто в xls",
     *     operationId="exportStolenCarsToXls",
     *     tags={"Stolen Cars"},
     *     @OA\Response(response="200", description="OK"),
     *     @OA\Parameter( ref="#/components/parameters/make", ),
     *     @OA\Parameter( ref="#/components/parameters/model", ),
     *     @OA\Parameter( ref="#/components/parameters/year", ),
     *     @OA\Parameter( ref="#/components/parameters/search", ),
     *     @OA\Parameter( ref="#/components/parameters/sort_by", ),
     *     @OA\Parameter( ref="#/components/parameters/sort_order", ),
     * )
     */
    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'make'      => 'integer|exists:car_models,id',
            'model'     => 'integer|exists:car_makes,id',
            'year'      => 'integer|min:1900',
            'search'    => 'string',
            'sort_by'   => ['string', Rule::in(['car_number', 'color', 'full_name', 'make', 'model', 'vin', 'year'])],
            'sort_order'=> ['string', Rule::in(['asc', 'desc'])],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'result' => $validator,
            ]);
        }

        $stolenCarsQuery = $this->stolenCarRepo->getWithFilterQuery($request->toArray());
        $stolenCars = $stolenCarsQuery->get();
        return Excel::download(new StolenCarExport($stolenCars), 'stolen_cars.xlsx');
    }

    /**
     * @OA\Parameter(
     *      parameter="make",
     *      in="query",
     *      name="make",
     *      description="ID марки авто",
     *      @OA\Schema(
     *          type="integer",
     *      )
     * )
     *
     * @OA\Parameter(
     *      parameter="model",
     *      in="query",
     *      name="model",
     *      description="ID модели авто",
     *      @OA\Schema(
     *          type="integer",
     *      )
     * )
     *
     * @OA\Parameter(
     *      parameter="year",
     *      in="query",
     *      name="year",
     *      description="Год выпуска авто",
     *      @OA\Schema(
     *          type="integer",
     *      )
     * )
     *
     * @OA\Parameter(
     *      parameter="search",
     *      in="query",
     *      name="year",
     *      description="Поисковая строка по: имени владельца | номеру авто | VIN коду",
     *      @OA\Schema(
     *          type="string",
     *      )
     * )
     *
     * @OA\Parameter(
     *      parameter="sort_by",
     *      in="query",
     *      name="sort_by",
     *      description="Указание по какому полю сортировать",
     *      @OA\Schema(
     *          type="string",
     *      )
     * )
     *
     * @OA\Parameter(
     *      parameter="sort_order",
     *      in="query",
     *      name="sort_order",
     *      description="Указание порядка сортировки",
     *      @OA\Schema(
     *          type="string",
     *      )
     * )
     *
     * @OA\Parameter(
     *      parameter="full_name",
     *      in="query",
     *      name="full_name",
     *      description="Имя владельца авто",
     *      @OA\Schema(
     *          type="string",
     *      )
     * )
     *
     * @OA\Parameter(
     *      parameter="car_number",
     *      in="query",
     *      name="car_number",
     *      description="Гос.номер авто",
     *      @OA\Schema(
     *          type="string",
     *      )
     * )
     *
     * @OA\Parameter(
     *      parameter="color",
     *      in="query",
     *      name="color",
     *      description="Цвет авто",
     *      @OA\Schema(
     *          type="string",
     *      )
     * )
     *
     * @OA\Parameter(
     *      parameter="vin",
     *      in="query",
     *      name="vin",
     *      description="VIN код авто",
     *      @OA\Schema(
     *          type="string",
     *      )
     * )
     *
     * @OA\Parameter(
     *      parameter="idCar",
     *      in="path",
     *      name="idСar",
     *      description="ID записи об уганном авто",
     *      @OA\Schema(
     *          type="string",
     *      )
     * )
     *
     * @OA\Parameter(
     *      parameter="page",
     *      in="query",
     *      name="page",
     *      description="Номер страницы при выводе списка",
     *      @OA\Schema(
     *          type="string",
     *      )
     * )
     *
     * @OA\Parameter(
     *      parameter="pages",
     *      in="query",
     *      name="pages",
     *      description="Количество элементов на странице при выводе",
     *      @OA\Schema(
     *          type="string",
     *      )
     * )
     */
}
