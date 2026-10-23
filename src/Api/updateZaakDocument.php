<?php

namespace Woweb\Zaakdms\Api;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Woweb\Zaakdms\Response\ZaakdmsResponse;
use Woweb\Zaakdms\Rule\FileExists;
use Woweb\Zaakdms\Rule\FileSize;
use Illuminate\Support\Str;
use Woweb\Zaakdms\Exceptions\InvalidZaakdmsArgumentsException;
use Woweb\Zaakdms\Zaakdms;

class updateZaakDocument extends AbstractApi
{
    protected $soapAction = 'http://www.egem.nl/StUF/sector/zkn/0310/updateZaakdocument_Di02';

    protected $xmlTemplateName = 'updateZaakDocument';

    protected $soapFunctionForUrl = 'VerwerkSynchroonVrijBericht';

    protected $messageCode = 'Di02';

    public $confidentialLevels = [
        'openbaar'          => 'OPENBAAR',
        'beperkt_openbaar'  => 'BEPERKT OPENBAAR',
        'intern'            => 'INTERN',
        'zaakvertrouwelijk' => 'ZAAKVERTROUWELIJK',
        'vertrouwelijk'     => 'VERTROUWELIJK',
        'confidentieel'     => 'CONFIDENTIEEL',
        'geheim'            => 'GEHEIM',
        'zeer_geheim'       => 'ZEER GEHEIM',
    ];

    public function handle(array $options)
    {
        $this->validateOptions($options);
        $disk = $options['disk'] ?? null;

        if (isset($options['file']) && $options['file']) {
            $options  = array_merge($options, $this->getFormatteddFile($options['file'], $options['titel'], $disk));
        } else {
            // due to some strange setup we always need to send the "inhoud" parameter even
            // if we only want to update meta data, so we get the inhoud from the api
            $response        = (new Zaakdms())->geefZaakdocumentLezen($options['identificatie']);

            $options['file_base64']  = $response['file'];
            $options['bestandsnaam'] = Str::slug($options['titel'], '-');
        }

        $options['vertrouwelijkAanduiding'] = $this->confidentialLevels[$options['vertrouwelijkAanduiding']];

        $xml      = view($this->getXmlTemplateName(), $options)->render();
        
        $response = Http::timeout(45)->retry(2, 100)->withOptions($this->getOptions())
            ->withHeaders($this->getHeaders())
            ->withBody($xml, 'text/xml;charset=UTF-8')
            ->post($this->getUrl());

        $validated = ZaakdmsResponse::validate($response);

        return $validated;
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
        $disk            = $options['disk'] ?? null;
        $configuredDisks = array_keys(config('filesystems.disks', []));
        // Only look for the file on a disk that exists; an unknown disk is reported by the 'disk' rule.
        $fileRules       = $disk === null || in_array($disk, $configuredDisks, true)
            ? ['nullable', new FileExists($disk)]
            : ['nullable'];

        $validator = Validator::make($options, [
            'identificatie'             => 'required|string',
            'documenttype'              => 'required|string|max:255',
            'creatiedatum'              => 'required|date_format:Ymd',
            'ontvangstdatum'            => 'nullable|date_format:Ymd',
            'verzenddatum'              => 'nullable|date_format:Ymd',
            'status'                    => 'nullable|string|max:255',
            'titel'                     => 'required|string|max:255',
            'beschrijving'              => 'nullable|string',
            'auteur'                    => 'nullable|string|max:255',
            'disk'                      => ['nullable', 'string', Rule::in($configuredDisks)],
            'vertrouwelijkAanduiding'   => 'required|in:' . implode(',', array_keys($this->confidentialLevels)),
            'file'                      => $fileRules,
            'zaak.identificatie'        => 'required|string'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            throw new InvalidZaakdmsArgumentsException('Not all Zaakdms options are set for ' . $this->xmlTemplateName . '. Errors: ' . implode(' ', $errors->all()));
        }
    }

    private function getFormatteddFile(string $filepath, string $title, ?string $disk = null)
    {
        if (Storage::disk($disk)->exists($filepath)) {
            $file      = Storage::disk($disk)->get($filepath);
            $extension = pathinfo(storage_path($filepath), PATHINFO_EXTENSION);
            return [
                'file_base64'   => base64_encode($file),
                'extension'     => $extension,
                'bestandsnaam'  => Str::slug($title, '-')  . '-' . Carbon::now()->timestamp . '.'. $extension
            ];
        } else {
            throw new Exception('The file passed to the Zaakdms does not exist on the disk ' . $disk);
        }
    }
}
