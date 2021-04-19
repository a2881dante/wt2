<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;

class StolenCarExport implements FromCollection
{
    protected $collectionCar;

    public function __construct($collectionCar)
    {
        $this->collectionCar = $collectionCar;
    }

    public function collection()
    {
        return $this->collectionCar;
    }
}
