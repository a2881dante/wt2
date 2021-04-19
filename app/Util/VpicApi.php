<?php


namespace App\Util;


class VpicApi extends ExternalApi
{
    const BASE_URL = 'https://vpic.nhtsa.dot.gov/api/vehicles';

    const GET_MAKES_URI = '/getallmakes';

    const GET_MODELS_URI = '/getmodelsformakeid/';

    const DECODE_VIN_URI = '/DecodeVinValuesExtended/';

    public function getMakes()
    {
        return $this->endpointRequest(self::BASE_URL . self::GET_MAKES_URI . '?format=json');
    }

    public function getModels($makeId)
    {
        return $this->endpointRequest(self::BASE_URL . self::GET_MODELS_URI . $makeId . '?format=json');
    }

    public function decodeVIN($vin)
    {
        return $this->endpointRequest(self::BASE_URL . self::DECODE_VIN_URI . $vin . '?format=json');
    }
}