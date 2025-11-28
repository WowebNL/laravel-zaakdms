<?php

namespace Woweb\Zaakdms\Api;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Woweb\Zaakdms\Exceptions\InvalidZaakdmsArgumentsException;
use Woweb\Zaakdms\Response\ZaakdmsResponse;

class actualiseerZaakstatus extends AbstractApi
{
    protected $soapAction = 'http://www.egem.nl/StUF/sector/zkn/0310/actualiseerZaakstatus_Lk01';

    protected $xmlTemplateName = 'actualiseerZaakstatus';

    protected $soapFunctionForUrl = 'OntvangAsynchroon';

    protected $messageCode = 'Lk01';

    protected $options;

    public function handle(array $options)
    {
        $this->validateOptions($options);

        $xml      = view($this->getXmlTemplateName(), $this->options)->render();
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
            'volgnummer'                => 'required|integer|min:1',
            'omschrijving'              => 'required|string|max:255',
            'gebruikerIdentificatie'    => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            throw new InvalidZaakdmsArgumentsException('Not all Zaakdms options are set for ' . $this->xmlTemplateName . '. Errors: ' . implode(' ', $errors->all()));
        }

        $this->options = $validator->validate();
    }
}
