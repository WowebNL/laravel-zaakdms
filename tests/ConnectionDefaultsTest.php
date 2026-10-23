<?php

namespace Woweb\Zaakdms\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Woweb\Zaakdms\Api\geefZaakDetails;

class ConnectionDefaultsTest extends TestCase
{
    public function test_the_tls_settings_have_no_default_paths(): void
    {
        $config = require __DIR__ . '/../config/zaakdms.php';

        $this->assertNull($config['cert']);
        $this->assertNull($config['cert_key']);
        $this->assertNull($config['verify']);
    }

    public function test_a_request_is_sent_when_the_tls_settings_are_not_set(): void
    {
        config([
            'zaakdms.cert'     => null,
            'zaakdms.cert_key' => null,
            'zaakdms.verify'   => null,
        ]);
        Http::fake(['*' => Http::response($this->soapSuccessBody())]);

        (new geefZaakDetails())->handle('ZAAK-0001');

        Http::assertSent(fn (Request $request) => $request->url() === 'https://zaakdms.test/BeantwoordVraag');
    }
}
