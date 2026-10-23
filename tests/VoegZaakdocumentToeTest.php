<?php

namespace Woweb\Zaakdms\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Woweb\Zaakdms\Api\voegZaakdocumentToe;
use Woweb\Zaakdms\Exceptions\InvalidZaakdmsArgumentsException;

class VoegZaakdocumentToeTest extends TestCase
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
            'auteur'                  => 'Example author',
            'vertrouwelijkAanduiding' => 'openbaar',
            'file'                    => 'tmp/document.pdf',
            'zaak'                    => ['identificatie' => 'ZAAK-0001'],
        ], $overrides);
    }

    public function test_it_reads_the_file_from_any_configured_disk(): void
    {
        Http::fake(['*' => Http::response($this->soapSuccessBody())]);
        Storage::disk('documents')->put('tmp/document.pdf', 'content on the documents disk');

        (new voegZaakdocumentToe())->handle($this->documentOptions(['disk' => 'documents']));

        Http::assertSent(fn (Request $request) => str_contains(
            $request->body(),
            base64_encode('content on the documents disk')
        ));
    }

    public function test_it_reads_the_file_from_the_default_disk_without_a_disk_option(): void
    {
        Http::fake(['*' => Http::response($this->soapSuccessBody())]);
        Storage::disk('local')->put('tmp/document.pdf', 'content on the default disk');

        (new voegZaakdocumentToe())->handle($this->documentOptions());

        Http::assertSent(fn (Request $request) => str_contains(
            $request->body(),
            base64_encode('content on the default disk')
        ));
    }

    public function test_it_rejects_a_disk_that_is_not_configured(): void
    {
        Http::fake();
        Storage::disk('local')->put('tmp/document.pdf', 'only on the default disk');

        $this->expectException(InvalidZaakdmsArgumentsException::class);

        try {
            (new voegZaakdocumentToe())->handle($this->documentOptions(['disk' => 'not-configured']));
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_it_rejects_a_file_that_is_missing_on_the_given_disk(): void
    {
        Http::fake();
        Storage::disk('local')->put('tmp/document.pdf', 'only on the default disk');

        $this->expectException(InvalidZaakdmsArgumentsException::class);

        try {
            (new voegZaakdocumentToe())->handle($this->documentOptions(['disk' => 'documents']));
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_the_file_is_still_required(): void
    {
        Http::fake();

        $this->expectException(InvalidZaakdmsArgumentsException::class);

        try {
            (new voegZaakdocumentToe())->handle($this->documentOptions(['file' => null]));
        } finally {
            Http::assertNothingSent();
        }
    }
}
