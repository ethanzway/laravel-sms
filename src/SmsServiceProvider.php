<?php

namespace Gw19900524\Sms;

use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defere = true;
    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config
        $configPath = __DIR__ . '/Config/config.php';
        $this->publishes([$configPath => config_path('laravel-sms.php')], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sms', function($app){
            $factory = new \Gw19900524\Sms\SmsFactory;
            $manager = new \Gw19900524\Sms\SmsManager($app, $factory);
            return $manager;
        });
        $this->app->alias('sms', SmsManager::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sms'];
    }
}
