<?php

namespace Woweb\Zaakdms\Api;

use Exception;

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
        // $verify = config('zaakdms.verify');
        // $options = [];

        // if($verify) {
        //     $options['verify'] = $verify;
        //     'verify'    => config('nijmegen-esb.verify'),
        //     'cert'  => config('nijmegen-esb.cert'),
        //     'ssl_key'   => config('nijmegen-esb.cert_key')
        // }

        // return $options;

        return [
            'verify'    => config('zaakdms.verify'),
            'cert'      => config('zaakdms.cert'),
            'ssl_key'   => config('zaakdms.cert_key'),
            'connect_timeout'   => 5 // 5 seconds
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
