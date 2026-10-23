<?php

namespace Woweb\Zaakdms\Api;

use Exception;
use Illuminate\Validation\Rule;
use Woweb\Zaakdms\Rule\FileExists;

abstract class AbstractApi
{
    /** @var string */
    protected $soapAction;

    /** @var string */
    protected $xmlTemplateName;

    /** @var string */
    protected $soapFunctionForUrl;

    /** @var string */
    protected $messageCode;

    /** @var string */
    protected $function;

    public function __construct()
    {
        if (empty($this->soapAction)) {
            throw new Exception('Soap Action varriable needs to be set on ' . get_class($this));
        }
        if (empty($this->xmlTemplateName)) {
            throw new Exception('Xml template varriable needs to be set on ' . get_class($this));
        }
        if (empty($this->soapFunctionForUrl)) {
            throw new Exception('Soap function varriable needs to be set on ' . get_class($this));
        }
        if (empty($this->messageCode)) {
            throw new Exception('Messagecode varriable needs to be set on ' . get_class($this));
        }
    }

    public function getUrl()
    {
        return config('zaakdms.url') . $this->soapFunctionForUrl;
    }

    /**
     * get the name of the blade template for rendering the xml
     *
     * @return string
     */
    public function getXmlTemplateName(): string
    {
        return 'zaakdms::' . $this->xmlTemplateName;
    }

    /**
     * Get the http options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'verify'    => config('zaakdms.verify'),
            'cert'      => config('zaakdms.cert'),
            'ssl_key'   => config('zaakdms.cert_key'),
            'connect_timeout'   => 5 // 5 seconds
        ];
    }

    /**
     * Validation rules for the optional 'disk' option and the 'file' that is read from it.
     *
     * The disk must be one of the disks configured in filesystems.disks. The file is only
     * looked up on a disk that exists; an unknown disk is reported by the 'disk' rule.
     *
     * @param array $options
     * @param string $fileRequirement 'required' or 'nullable'
     * @return array
     */
    protected function diskAndFileRules(array $options, string $fileRequirement): array
    {
        $disk            = $options['disk'] ?? null;
        // An empty disk means the default disk, as Storage::disk('') does.
        if (is_string($disk) && trim($disk) === '') {
            $disk = null;
        }
        $configuredDisks = array_keys(config('filesystems.disks', []));
        $fileRules       = [$fileRequirement];

        if ($disk === null || in_array($disk, $configuredDisks, true)) {
            $fileRules[] = new FileExists($disk);
        }

        return [
            'disk' => ['nullable', 'string', Rule::in($configuredDisks)],
            'file' => $fileRules,
        ];
    }

    /**
     * get the headers needed for the api call
     *
     * @return array
     */
    public function getHeaders(): array
    {
        $headers = ['SOAPAction' => $this->soapAction];
        $apiKey  = config('zaakdms.api_key');

        if ($apiKey) {
            $headers['gateway-api-key'] = $apiKey;
        }

        return $headers;
    }
}
