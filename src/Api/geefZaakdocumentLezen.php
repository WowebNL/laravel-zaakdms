<?php

namespace Woweb\Zaakdms\Api;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Woweb\Zaakdms\Exceptions\InvalidZaakdmsArgumentsException;
use Woweb\Zaakdms\Response\ZaakdmsResponse;

class geefZaakdocumentLezen extends AbstractApi
{
    protected $soapAction = 'http://www.egem.nl/StUF/sector/zkn/0310/geefZaakdocumentLezen_Lv01';

    protected $xmlTemplateName = 'geefZaakdocumentLezen';

    protected $soapFunctionForUrl = 'BeantwoordVraag';

    protected $messageCode = 'La01';

    public function handle(string $id, string $documentType = 'native')
    {
        $options = ['identificatie' => $id];
        $url = $this->getUrl() . '?documentType=' . $documentType;

        $this->validateOptions($options);
        $xml = view($this->getXmlTemplateName(), $options)->render();
        //dd($xml);
        $response = Http::withOptions($this->getOptions())
        ->withHeaders($this->getHeaders())
        ->withBody($xml, 'text/xml;charset=UTF-8')
        ->post($url);

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
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            throw new InvalidZaakdmsArgumentsException('Not all Zaakdms options are set for ' . $this->xmlTemplateName . '. Errors: ' . implode(' ', $errors->all()));
        }
    }
}
