<?php

namespace Woweb\Zaakdms\Tests;

use Monolog\Handler\NullHandler;
use Orchestra\Testbench\TestCase as Orchestra;
use Woweb\Zaakdms\ZaakdmsServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [ZaakdmsServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        // Applications often run on UTC; the package converts its own timestamps.
        $app['config']->set('app.timezone', 'UTC');
        $app['config']->set('zaakdms.url', 'https://zaakdms.test/');
        $app['config']->set('zaakdms.verify', false);
        $app['config']->set('zaakdms.cert', null);
        $app['config']->set('zaakdms.cert_key', null);
        $app['config']->set('zaakdms.sender', [
            'application'    => 'TEST',
            'administrative' => null,
            'organization'   => 'TEST',
            'user'           => 'tester',
        ]);
        $app['config']->set('zaakdms.receiver', [
            'application'    => 'DMS',
            'administrative' => null,
            'organization'   => 'TEST',
            'user'           => 'tester',
        ]);

        $app['config']->set('logging.channels.zaakdms', [
            'driver'  => 'monolog',
            'handler' => NullHandler::class,
        ]);
    }

    /**
     * A minimal successful SOAP response that passes ZaakdmsResponse::validate().
     */
    protected function soapSuccessBody(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">'
            . '<soap:Body><Bv02Bericht><stuurgegevens><berichtcode>Bv02</berichtcode></stuurgegevens></Bv02Bericht></soap:Body>'
            . '</soap:Envelope>';
    }
}
