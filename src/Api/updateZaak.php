<?php

namespace Woweb\Zaakdms\Api;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Woweb\Zaakdms\Exceptions\InvalidZaakdmsArgumentsException;
use Woweb\Zaakdms\Response\ZaakdmsResponse;

/**
 * IMPORTANT: this class only supports update for setting a result + end date for now!
 * All other fields are ignored!
 */
class updateZaak extends AbstractApi
{
    protected $soapAction = 'http://www.egem.nl/StUF/sector/zkn/0310/updateZaak_Lk01';

    protected $xmlTemplateName = 'updateZaak';

    protected $soapFunctionForUrl = 'OntvangAsynchroon';

    protected $messageCode = 'Lk01';

    public function handle(array $options)
    {
        $this->validateOptions($options);
        $xml      = view($this->getXmlTemplateName(), $options)->render();

        $response = Http::withOptions($this->getOptions())
        ->withHeaders($this->getHeaders())
        ->withBody($xml, 'text/xml;charset=UTF-8')
        ->post($this->getUrl());

        return ZaakdmsResponse::validate($response);
    }

    /**
     * Checks is all the options for this api call are present and  in correct format
     *
     * @param array $options
     * @throws Exception
     * @return null
     */
    private function validateOptions(array $options)
    {
        $validator = Validator::make($options, [
            'identificatie'             => 'required|string',
            'resultaat'                 => 'required|string|max:255',
            'einddatum'                 => 'required|date_format:Ymd'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            throw new InvalidZaakdmsArgumentsException('Not all Zaakdms options are set for ' . $this->xmlTemplateName . '. Errors: ' . implode(' ', $errors->all()));
        }
    }
}
