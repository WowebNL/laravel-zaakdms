<?php

namespace Woweb\Zaakdms;

use Carbon\CarbonInterface;
use Exception;
use Illuminate\Support\Carbon;
use SimpleXMLElement;

class Helper
{
    public static function findInObject($obj, string $property)
    {
        if(is_object($obj)){
            $obvar = get_object_vars($obj);
            if(array_key_exists($property,$obvar)){
                return $obvar[$property];
            } else {
                foreach($obvar as $var){
                    $result =  self::findInObject($var,$property);
                    if($result){
                        return $result;
                    }
                }
            }
        }
        return null;
    }

    public static function findExtraElement(SimpleXMLElement $response, string $attributeKey, string $attributeValue)
    {
        $extraElements = self::findInObject($response, 'stufextraElementen');

        if($extraElements) {
            foreach($extraElements as $element) {
                $attributes = $element->attributes();
                foreach($attributes as $key => $value){
                    if($key == $attributeKey && $value == $attributeValue ) {
                        return $element;
                    }
                }
            }
        }

        throw new Exception('Attriute ' . $attributeKey .' with value ' . $attributeValue .' not found on Zaakdms response');
    }

    /**
     * The current time in the timezone configured in zaakdms.timezone, for the timestamps
     * the package generates itself. Without a configured timezone the application
     * timezone is used.
     */
    public static function now(): CarbonInterface
    {
        $now      = Carbon::now();
        $timezone = config('zaakdms.timezone');

        return $timezone ? $now->setTimezone($timezone) : $now;
    }

    public static function getWebviewUrl(string $identificatie) : string 
    {
        // TODO ip checker
        return str_replace('{{identificatie}}', $identificatie, config('zaakdms.webview_url'));
    }
}