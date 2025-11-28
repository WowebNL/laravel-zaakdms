<?php

namespace Woweb\Zaakdms\Response;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Woweb\Zaakdms\Helper;

class ZaakdmsResponse
{
    public static function validate(Response $originalResponse): SimpleXMLElement
    {
        self::checkResponseCode($originalResponse);

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", '$1$2$3', $originalResponse->body());
        $response = str_replace('stuf:bestandsnaam', 'bestandsnaam', $response);
        $xml      = new SimpleXMLElement($response);
        $body     = $xml->xpath('//soapBody')[0];
        self::validateErrorMessage($body, $originalResponse);
        return $body;
    }

    private static function checkResponseCode(Response $response)
    {
        if ($response->failed()) {
            Log::channel('zaakdms')->error('Error ' . $response->status() . ' response body: ' . PHP_EOL . $response->body());
            $response->throw();
        }
    }

    private static function validateErrorMessage($responseObject, Response $response)
    {
        $found = Helper::findInObject($responseObject, 'Fo02Bericht');
        if ($found) {
            Log::channel('zaakdms')->error('Error with succes response' . ' response body: ' . PHP_EOL . $response->body());
            throw new Exception('Response for Zaakdms ' . $response->headers()['esbbusinessproces'][0] . ' failed. Errors: '
                . Helper::findInObject($responseObject, 'omschrijving') . ' ' . Helper::findInObject($responseObject, 'details'));
        }
    }
}
