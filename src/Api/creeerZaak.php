<?php

namespace Woweb\Zaakdms\Api;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Woweb\Zaakdms\Exceptions\InvalidZaakdmsArgumentsException;
use Woweb\Zaakdms\Response\ZaakdmsResponse;
use Woweb\Zaakdms\Rule\RequiredWithoutId;
use Woweb\Zaakdms\Rule\RequiredWithoutInitiatorIdentificatie;
use Woweb\Zaakdms\Rule\ValidateInitiatorField;

class creeerZaak extends AbstractApi
{
    protected $soapAction = 'http://www.egem.nl/StUF/sector/zkn/0310/creeerZaak_Lk01';

    protected $xmlTemplateName = 'creeerZaak';

    protected $soapFunctionForUrl = 'OntvangAsynchroon';

    protected $messageCode = 'Lk01';

    public function handle(array $options)
    {
        $this->validateOptions($options);
        $xml = view($this->getXmlTemplateName(), $options)->render();
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
     * @return null
     * @throws Exception
     */
    private function validateOptions(array $options)
    {
        $validator = Validator::make($options, [
            'identificatie' => 'required|string',
            'omschrijving' => 'required|string|max:255',
            'startdatum' => 'required|date_format:Ymd',
            'registratiedatum' => 'required|date_format:Ymd',
            'zaakniveau' => 'required|integer',
            'zaaktype.omschrijving' => 'required|string|max:255',
            'zaaktype.code' => 'required|integer',
            'initiator.type' => 'required|in:natuurlijk_persoon,niet_natuurlijk_persoon', 
            'initiator.natuurlijk_persoon.bsn' => [new ValidateInitiatorField('natuurlijk_persoon', 'identificatie', $options), 'nullable','numeric', 'digits:9'],
            'initiator.natuurlijk_persoon.geslachtsnaam' => 'required_if:initiator.type,natuurlijk_persoon|nullable|string|max:255',
            'initiator.natuurlijk_persoon.identificatie' => [new ValidateInitiatorField('natuurlijk_persoon', 'bsn', $options), 'nullable', 'string'],
            'initiator.niet_natuurlijk_persoon.nnpId' => [new ValidateInitiatorField('niet_natuurlijk_persoon', 'identificatie', $options), 'nullable', 'numeric', 'digits:9'],
            'initiator.niet_natuurlijk_persoon.statutaireNaam' => 'required_if:initiator.type,niet_natuurlijk_persoon|nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            throw new InvalidZaakdmsArgumentsException('Not all Zaakdms options are set for ' . $this->xmlTemplateName . '. Errors: ' . implode(' ', $errors->all()));
        }
    }
}
