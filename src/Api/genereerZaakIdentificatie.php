<?php

namespace Woweb\Zaakdms\Api;

use Illuminate\Support\Facades\Http;
use Woweb\Zaakdms\Response\ZaakdmsResponse;

class genereerZaakIdentificatie extends AbstractApi
{
    protected $soapAction = 'http://www.egem.nl/StUF/sector/zkn/0310/genereerZaakIdentificatie_Di02';

    protected $xmlTemplateName = 'genereerZaakIdentificatie';

    protected $soapFunctionForUrl = 'VerwerkSynchroonVrijBericht';

    protected $messageCode = 'Di02';

    protected $function = 'genereerZaakidentificatie';

    public function handle()
    {
        $response = Http::withOptions($this->getOptions())
        ->withHeaders($this->getHeaders())
        ->withBody(view($this->getXmlTemplateName())->render(), 'text/xml;charset=UTF-8')
        ->post($this->getUrl());

        return ZaakdmsResponse::validate($response);
    }
}