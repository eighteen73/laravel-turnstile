<?php

namespace Eighteen73\Turnstile;

use Eighteen73\Turnstile\Http\Middleware\Turnstile;
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
        $this->configureMiddleware();
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
        Blade::directive('turnstileScripts', function () {
            return '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
        });
        Blade::directive('turnstile', function () {
            return '<div class="cf-turnstile" data-sitekey="'.config('turnstile.key').'" data-callback="onTurnstileSuccess"></div>';
        });
    }

    /**
     * Configure middleware that checks the submitted code.
     *
     * @return void
     */
    protected function configureMiddleware()
    {
        if (config('turnstile.mode') !== 'middleware') {
            return;
        }
        $this->app->booted(function () {
            $router = app('router');
            $router->pushMiddlewareToGroup('web', Turnstile::class);
        });
    }
}
