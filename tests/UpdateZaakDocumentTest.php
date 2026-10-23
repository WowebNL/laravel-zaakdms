<?php

namespace Woweb\Zaakdms\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Woweb\Zaakdms\Api\updateZaakDocument;
use Woweb\Zaakdms\Exceptions\InvalidZaakdmsArgumentsException;

class UpdateZaakDocumentTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('filesystems.default', 'local');
        $app['config']->set('filesystems.disks.documents', [
            'driver' => 'local',
            'root'   => sys_get_temp_dir() . '/zaakdms-documents',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('documents');
    }

    private function documentOptions(array $overrides = []): array
    {
        return array_merge([
            'identificatie'           => 'DOC-0001',
            'documenttype'            => 'Brief',
            'creatiedatum'            => '20260314',
            'titel'                   => 'Example document',
            'vertrouwelijkAanduiding' => 'openbaar',
            'file'                    => 'tmp/document.pdf',
            'zaak'                    => ['identificatie' => 'ZAAK-0001'],
        ], $overrides);
    }

    public function test_it_reads_the_file_from_the_given_disk(): void
    {
        Http::fake(['*' => Http::response($this->soapSuccessBody())]);
        Storage::disk('documents')->put('tmp/document.pdf', 'content on the documents disk');

        (new updateZaakDocument())->handle($this->documentOptions(['disk' => 'documents']));

        Http::assertSent(fn (Request $request) => str_contains(
            $request->body(),
            base64_encode('content on the documents disk')
        ));
    }

    public function test_it_reads_the_file_from_the_default_disk_without_a_disk_option(): void
    {
        Http::fake(['*' => Http::response($this->soapSuccessBody())]);
        Storage::disk('local')->put('tmp/document.pdf', 'content on the default disk');

        (new updateZaakDocument())->handle($this->documentOptions());

        Http::assertSent(fn (Request $request) => str_contains(
            $request->body(),
            base64_encode('content on the default disk')
        ));
    }

    public function test_it_rejects_a_file_that_is_missing_on_the_given_disk(): void
    {
        Http::fake();
        Storage::disk('local')->put('tmp/document.pdf', 'only on the default disk');

        $this->expectException(InvalidZaakdmsArgumentsException::class);

        try {
            (new updateZaakDocument())->handle($this->documentOptions(['disk' => 'documents']));
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_it_rejects_a_disk_that_is_not_configured(): void
    {
        Http::fake();
        Storage::disk('local')->put('tmp/document.pdf', 'only on the default disk');

        $this->expectException(InvalidZaakdmsArgumentsException::class);

        try {
            (new updateZaakDocument())->handle($this->documentOptions(['disk' => 'not-configured']));
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_it_sends_the_request_with_the_same_timeout_as_voeg_zaakdocument_toe(): void
    {
        $timeouts = [];
        Http::globalMiddleware(function (callable $handler) use (&$timeouts) {
            return function ($request, array $options) use ($handler, &$timeouts) {
                $timeouts[] = $options['timeout'] ?? null;

                return $handler($request, $options);
            };
        });
        Http::fake(['*' => Http::response($this->soapSuccessBody())]);
        Storage::disk('local')->put('tmp/document.pdf', 'content');

        (new updateZaakDocument())->handle($this->documentOptions());

        $this->assertSame([45], $timeouts);
    }

    public function test_it_retries_a_failed_request(): void
    {
        Http::fakeSequence()
            ->push('temporarily unavailable', 503)
            ->push($this->soapSuccessBody(), 200);
        Storage::disk('local')->put('tmp/document.pdf', 'content');

        (new updateZaakDocument())->handle($this->documentOptions());

        Http::assertSentCount(2);
    }
}
