<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Laravel;

use Illuminate\Support\ServiceProvider;
use Tigusigalpa\SocialKit\SocialKitClient;
use Tigusigalpa\SocialKit\SocialKitClientInterface;
use Tigusigalpa\SocialKit\SocialKitConfig;

/**
 * Laravel service provider for the SocialKit SDK.
 *
 * Registers the package configuration and binds a shared {@see SocialKitClient}
 * instance into the container, built from the `config/socialkit.php` values.
 */
final class SocialKitServiceProvider extends ServiceProvider
{
    /**
     * Path to the package's default configuration file.
     */
    private function configPath(): string
    {
        return dirname(__DIR__, 2) . '/config/socialkit.php';
    }

    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        $this->mergeConfigFrom($this->configPath(), 'socialkit');

        $this->app->singleton(SocialKitClientInterface::class, static function ($app): SocialKitClient {
            $config = SocialKitConfig::fromArray((array) $app['config']->get('socialkit', []));

            return new SocialKitClient($config);
        });

        $this->app->alias(SocialKitClientInterface::class, SocialKitClient::class);
        $this->app->alias(SocialKitClientInterface::class, 'socialkit');
    }

    /**
     * Bootstrap package services, publishing the config file for Laravel applications.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->configPath() => $this->app->configPath('socialkit.php'),
            ], 'socialkit-config');
        }
    }

    /**
     * @return list<string>
     */
    public function provides(): array
    {
        return [SocialKitClientInterface::class, SocialKitClient::class, 'socialkit'];
    }
}
