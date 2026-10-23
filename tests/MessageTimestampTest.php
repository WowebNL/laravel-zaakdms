<?php

namespace Woweb\Zaakdms\Tests;

use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;

class MessageTimestampTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // An afternoon time: a 12-hour format would render 03 instead of 15.
        Carbon::setTestNow(Carbon::create(2026, 3, 14, 15, 30, 0));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public static function templates(): array
    {
        $document = [
            'identificatie'           => 'DOC-0001',
            'documenttype'            => 'Brief',
            'creatiedatum'            => '20260314',
            'titel'                   => 'Example document',
            'auteur'                  => 'Example author',
            'vertrouwelijkAanduiding' => 'OPENBAAR',
            'extension'               => 'pdf',
            'bestandsnaam'            => 'example-document.pdf',
            'file_base64'             => base64_encode('content'),
            'zaak'                    => ['identificatie' => 'ZAAK-0001'],
        ];

        return [
            'actualiseerZaakstatus'   => ['actualiseerZaakstatus', [
                'identificatie'          => 'ZAAK-0001',
                'volgnummer'             => 1,
                'omschrijving'           => 'Ontvangen',
                'gebruikerIdentificatie' => 'tester',
            ]],
            'creeerZaak'              => ['creeerZaak', [
                'identificatie'    => 'ZAAK-0001',
                'omschrijving'     => 'Example case',
                'startdatum'       => '20260314',
                'registratiedatum' => '20260314',
                'zaakniveau'       => 1,
                'zaaktype'         => ['code' => 'ZT-1', 'omschrijving' => 'Example case type'],
                'initiator'        => [
                    'type'                    => 'niet_natuurlijk_persoon',
                    'niet_natuurlijk_persoon' => ['statutaireNaam' => 'Example Organisation'],
                ],
            ]],
            'geefLijstZaakdocumenten' => ['geefLijstZaakdocumenten', ['identificatie' => 'ZAAK-0001']],
            'geefZaakDetails'         => ['geefZaakDetails', ['identificatie' => 'ZAAK-0001']],
            'geefZaakdocumentLezen'   => ['geefZaakdocumentLezen', ['identificatie' => 'DOC-0001']],
            'updateZaak'              => ['updateZaak', [
                'identificatie' => 'ZAAK-0001',
                'resultaat'     => 'Verleend',
                'einddatum'     => '20260314',
            ]],
            'updateZaakDocument'      => ['updateZaakDocument', $document],
            'voegZaakdocumentToe'     => ['voegZaakdocumentToe', $document],
        ];
    }

    #[DataProvider('templates')]
    public function test_the_message_timestamp_uses_a_24_hour_clock(string $template, array $data): void
    {
        $xml = view('zaakdms::' . $template, $data)->render();

        $this->assertStringContainsString('<stuf:tijdstipBericht>20260314153000</stuf:tijdstipBericht>', $xml);
    }

    public function test_the_status_timestamp_uses_a_24_hour_clock(): void
    {
        $xml = view('zaakdms::actualiseerZaakstatus', [
            'identificatie'          => 'ZAAK-0001',
            'volgnummer'             => 1,
            'omschrijving'           => 'Ontvangen',
            'gebruikerIdentificatie' => 'tester',
        ])->render();

        $this->assertStringContainsString('<zkn:datumStatusGezet>20260314153000</zkn:datumStatusGezet>', $xml);
    }

    public function test_no_template_formats_a_time_with_a_12_hour_clock(): void
    {
        $offending = [];
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(__DIR__ . '/../resources/views', \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            preg_match_all("/->format\\(\\s*['\"]([^'\"]*)['\"]/", file_get_contents($file->getPathname()), $matches);

            foreach ($matches[1] as $format) {
                // 'h' and 'g' are the 12-hour hour specifiers in PHP date formats.
                if (preg_match('/(?<!\\\\)[hg]/', $format)) {
                    $offending[] = $file->getFilename() . ': ' . $format;
                }
            }
        }

        $this->assertSame([], $offending);
    }
}
