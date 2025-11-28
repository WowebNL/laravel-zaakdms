<?php

namespace Woweb\Zaakdms\Api;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Woweb\Zaakdms\Response\ZaakdmsResponse;
use Woweb\Zaakdms\Rule\FileExists;
use Woweb\Zaakdms\Rule\FileSize;
use Illuminate\Support\Str;
use Log;
use Woweb\Zaakdms\Exceptions\InvalidZaakdmsArgumentsException;

class voegZaakdocumentToe extends AbstractApi
{
    protected $soapAction = 'http://www.egem.nl/StUF/sector/zkn/0310/voegZaakdocumentToe_Lk01';

    protected $xmlTemplateName = 'voegZaakdocumentToe';

    protected $soapFunctionForUrl = 'OntvangAsynchroon';

    protected $messageCode = 'Lk01';

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
        $logginsEnabled = config('zaakdms.log_file_requests');

        $this->validateOptions($options);
        $disk = $options['disk'] ?? null;

        if ($logginsEnabled) {
            Log::channel('zaakdms')->info('Start ' . $options['file'] . ' ophalen bij S3');
        }

        $options  = array_merge($options, $this->getFormatteddFile($options['file'], $options['titel'], $disk));

        if ($logginsEnabled) {
            Log::channel('zaakdms')->info('Einde ' . $options['file'] . ' ophalen bij S3');
        }

        $options['vertrouwelijkAanduiding'] = $this->confidentialLevels[$options['vertrouwelijkAanduiding']];

        $xml      = view($this->getXmlTemplateName(), $options)->render();

        if ($logginsEnabled) {
            Log::channel('zaakdms')->info('Start ' . $options['file'] . ' sturen naar zaakdms');
        }

        $response = Http::timeout(45)->retry(2, 100)->withOptions($this->getOptions())
            ->withHeaders($this->getHeaders())
            ->withBody($xml, 'text/xml;charset=UTF-8')
            ->post($this->getUrl());

        if ($logginsEnabled) {
            Log::channel('zaakdms')->info('Einde ' . $options['file'] . ' sturen naar zaakdms');
        }

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
        $disk      = $options['disk'] ?? null;
        $validator = Validator::make($options, [
            'identificatie'             => 'required|string',
            'documenttype'              => 'required|string|max:255',
            'creatiedatum'              => 'required|date_format:Ymd',
            'ontvangstdatum'            => 'nullable|date_format:Ymd',
            'verzenddatum'              => 'nullable|date_format:Ymd',
            'status'                    => 'nullable|string|max:255',
            'titel'                     => 'required|string|max:255',
            'beschrijving'              => 'nullable|string',
            'auteur'                    => 'required|string|max:255',
            'disk'                      => 'nullable|in:local,s3-nijmegen,s3-nijmegen-openforms',
            'vertrouwelijkAanduiding'   => 'required|in:' . implode(',', array_keys($this->confidentialLevels)),
            'file'                      => ['required', new FileExists($disk)],
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

            // TODO quick and dirty fix for missing extension in s3-nijmegen, nijmegen needs to fix this
            if (str_contains($filepath, '/pdf/') && !str_ends_with(strtolower($filepath), '.pdf')) {
                $filepath = $filepath . '.pdf';
            }
            $extension = pathinfo(storage_path($filepath), PATHINFO_EXTENSION);
            $extension = substr($extension,0,1) == '.' ? $extension : '.' . $extension;

            return [
                'file_base64'   => base64_encode($file),
                'extension'     => $extension,
                'bestandsnaam'  => Str::slug($title, '-')  . '-' . Carbon::now()->timestamp . '.' . $extension
            ];
        } else {
            throw new Exception('The file passed to the Zaakdms does not exist on the disk ' . $disk);
        }
    }
}
