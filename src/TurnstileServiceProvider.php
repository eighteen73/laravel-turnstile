<?php

namespace Eighteen73\Turnstile;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class TurnstileServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/turnstile.php', 'turnstile');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePublishing();
        $this->configureDirectives();
    }

    /**
     * Configure the publishable resources offered by the package.
     *
     * @return void
     */
    protected function configurePublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../stubs/turnstile.php' => config_path('turnstile.php'),
            ], 'turnstile-config');
        }
    }

    /**
     * Configure custom blade directives.
     *
     * @return void
     */
    protected function configureDirectives()
    {
        Blade::directive('turnstile_script', function () {
            return '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
        });
        Blade::directive('turnstile', function () {
            return '<div class="cf-turnstile" data-sitekey="'.config('turnstile.key').'" data-callback="javascriptCallback"></div>';
        });
    }
}
