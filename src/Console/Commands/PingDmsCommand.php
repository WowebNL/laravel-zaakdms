<?php

namespace Woweb\Zaakdms\Console\Commands;

use App;
use Cache;
use Exception;
use Illuminate\Console\Command;
use Notification;
use Woweb\Zaakdms\Notifications\ZaakdmsOffline;
use Woweb\Zaakdms\Zaakdms;

class PingDmsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zaakdms:ping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pings the zaakdms service and if the service is offline / online updated the zaakdms_offline param in the cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $testId = config('zaakdms.ping_id');
        $api = App::make(Zaakdms::class);

        try {
            $rep = $api->geefZaakDetails($testId);
        } catch (Exception $e) {
            Cache::put('zaakdms_offline', true, now()->addDays(1));
            if (config('zaakdms.ping_failure_notification') && $webhook = config('app.settings.slack_webhook')) {
                $emailAdresses = explode(',', config('app.settings.error_mail_recipients'));
                $route =  Notification::route('slack', $webhook);
                if ($emailAdresses) {
                    $route->route('mail', $emailAdresses);
                }
                $route->notify(new ZaakdmsOffline($e));
            }
            $this->error('Zaakdms offline');
            return 0;
        }

        $this->info('Zaakdms online');
        Cache::put('zaakdms_offline', false, now()->addDays(1));
        return 0;
    }
}
