<?php

namespace Woweb\Zaakdms\Tests;

use Illuminate\Support\Carbon;

class LocalTimeTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function statusMessage(): string
    {
        return view('zaakdms::actualiseerZaakstatus', [
            'identificatie'          => 'ZAAK-0001',
            'volgnummer'             => 1,
            'omschrijving'           => 'Ontvangen',
            'gebruikerIdentificatie' => 'tester',
        ])->render();
    }

    private function assertTimestamps(string $expected, string $xml): void
    {
        $this->assertStringContainsString('<stuf:tijdstipBericht>' . $expected . '</stuf:tijdstipBericht>', $xml);
        $this->assertStringContainsString('<zkn:datumStatusGezet>' . $expected . '</zkn:datumStatusGezet>', $xml);
    }

    public function test_the_default_timezone_is_europe_amsterdam(): void
    {
        $this->assertSame('Europe/Amsterdam', config('zaakdms.timezone'));
    }

    public function test_it_sends_local_summer_time_when_the_application_runs_on_utc(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 1, 13, 30, 0, 'UTC'));

        $this->assertTimestamps('20260701153000', $this->statusMessage());
    }

    public function test_it_sends_local_winter_time_when_the_application_runs_on_utc(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 15, 13, 30, 0, 'UTC'));

        $this->assertTimestamps('20260115143000', $this->statusMessage());
    }

    public function test_the_date_follows_the_local_time_around_midnight(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 12, 31, 23, 30, 0, 'UTC'));

        $this->assertTimestamps('20270101003000', $this->statusMessage());
    }

    public function test_another_timezone_can_be_configured(): void
    {
        config(['zaakdms.timezone' => 'America/New_York']);
        Carbon::setTestNow(Carbon::create(2026, 7, 1, 13, 30, 0, 'UTC'));

        $this->assertTimestamps('20260701093000', $this->statusMessage());
    }

    public function test_an_empty_timezone_uses_the_application_timezone(): void
    {
        config(['zaakdms.timezone' => null]);
        Carbon::setTestNow(Carbon::create(2026, 7, 1, 13, 30, 0, 'UTC'));

        $this->assertTimestamps('20260701133000', $this->statusMessage());
    }

    public function test_dates_supplied_by_the_caller_are_not_converted(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 12, 31, 23, 30, 0, 'UTC'));

        $xml = view('zaakdms::voegZaakdocumentToe', [
            'identificatie'           => 'DOC-0001',
            'documenttype'            => 'Brief',
            'creatiedatum'            => '20261231',
            'titel'                   => 'Example document',
            'auteur'                  => 'Example author',
            'vertrouwelijkAanduiding' => 'OPENBAAR',
            'extension'               => 'pdf',
            'bestandsnaam'            => 'example-document.pdf',
            'file_base64'             => base64_encode('content'),
            'zaak'                    => ['identificatie' => 'ZAAK-0001'],
        ])->render();

        $this->assertStringContainsString('<stuf:tijdstipBericht>20270101003000</stuf:tijdstipBericht>', $xml);
        $this->assertStringContainsString('<zkn:creatiedatum>20261231</zkn:creatiedatum>', $xml);
    }
}
