<?php

namespace Woweb\Zaakdms;

use SimpleXMLElement;
use Woweb\Zaakdms\Api\actualiseerZaakstatus;
use Woweb\Zaakdms\Api\creeerZaak;
use Woweb\Zaakdms\Api\geefLijstZaakdocumenten;
use Woweb\Zaakdms\Api\geefZaakDetails;
use Woweb\Zaakdms\Api\geefZaakdocumentLezen;
use Woweb\Zaakdms\Api\genereerDocumentidentificatie;
use Woweb\Zaakdms\Api\genereerZaakIdentificatie;
use Woweb\Zaakdms\Api\updateZaak;
use Woweb\Zaakdms\Api\updateZaakDocument;
use Woweb\Zaakdms\Api\voegZaakdocumentToe;

class Zaakdms
{
    public function genereerZaakIdentificatie() : string
    {
        $response = (new genereerZaakIdentificatie())->handle();

        return Helper::findInObject($response, 'zknidentificatie');
    }

    public function creeerZaak(array $options) : string
    {
        $response = (new creeerZaak())->handle($options);

        // is equal to zkn:identificatie, use for cross reference check
        return Helper::findInObject($response, 'stufcrossRefnummer');
    }

    public function updateZaak(array $options) : string 
    {
        $response = (new updateZaak())->handle($options);
        return Helper::findInObject($response, 'stufcrossRefnummer');
    }

    public function geefZaakDetails(string $id) : array
    {
        $resp = (new geefZaakDetails())->handle($id);

        return [
            'dms_id'            => Helper::findExtraElement($resp, 'naam', 'corsa_dossierid'),
            'response'          => $resp
        ];
    }
    
    public function actualiseerZaakstatus(array $options) : string
    {
        $response = (new actualiseerZaakstatus())->handle($options);

        // is equal to zkn:identificatie of the document, use for cross reference check
        return Helper::findInObject($response, 'stufcrossRefnummer');
    }

    public function genereerDocumentIdentificatie() : string
    {
        $response = (new genereerDocumentidentificatie())->handle();

        return Helper::findInObject($response, 'zknidentificatie');
    }

    public function voegZaakdocumentToe(array $options) : string
    {
        $response = (new voegZaakdocumentToe())->handle($options);

        // is equal to zkn:identificatie of the document, use for cross reference check
        return Helper::findInObject($response, 'stufcrossRefnummer');
    }

    public function updateZaakDocument(array $options) : string 
    {
        $response = (new updateZaakDocument())->handle($options);

        return Helper::findInObject($response, 'stufcrossRefnummer'); 
    }

    public function geefLijstZaakdocumenten(string $id) : SimpleXMLElement
    {
        $response = (new geefLijstZaakdocumenten())->handle($id);

        // full zknobject item
        return Helper::findInObject($response, 'zknobject');
    }

    public function geefZaakdocumentLezen(string $id, string $documentType = 'native') : array
    {
        $resp = (new geefZaakdocumentLezen())->handle($id, $documentType);

        return [
            'file'              => Helper::findInObject($resp, 'zkninhoud'),
            'data'              => Helper::findInObject($resp, 'zknobject')
        ];
    }
}
