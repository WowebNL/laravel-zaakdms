<?php

namespace Woweb\Zaakdms;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Woweb\Zaakdms\Console\Commands\PingDmsCommand;
use Woweb\Zaakdms\View\Components\Ontvanger;
use Woweb\Zaakdms\View\Components\Zender;

class ZaakdmsServiceProvider extends ServiceProvider
{
    public $name = 'zaakdms';
    /**
     * Boot the package
     *
     * @return  void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/zaakdms.php' => config_path('zaakdms.php'),
        ]);
        $this->loadViewsFrom(__DIR__.'/../resources/views', $this->name);
        $this->loadViewComponentsAs($this->name, [
            Zender::class,
            Ontvanger::class
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                PingDmsCommand::class,
            ]);
        }

        // scheduler for the ping
        $this->app->booted(function () {
            $schedule = app(Schedule::class);
            $schedule->command('zaakdms:ping')->everyFourHours();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/zaakdms.php',
            $this->name
        );

        $this->app->bind('zaakdms',function(){
            return new Zaakdms();
        }); 
    }
}
