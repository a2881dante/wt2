<?php

namespace App\Util;


use Exception;
use GuzzleHttp\Client;

class ExternalApi
{
    public function endpointRequest($url)
    {
        $client = new Client();
        try {
            $response = $client->request('GET', $url);
        } catch (Exception $e) {
            return [
                'status'    => 'error',
                'message'   => $e->getMessage(),
            ];
        }
        return $this->response_handler($response->getBody()->getContents());
    }

    public function response_handler($response)
    {
        if ($response) {
            return [
                'status' => 'success',
                'result' => json_decode($response, true)
            ];
        }
        return [];
    }
}