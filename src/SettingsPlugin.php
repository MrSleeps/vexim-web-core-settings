<?php

namespace VEximweb\Core\Settings;

use Filament\Contracts\Plugin;
use Filament\Panel;
use VEximweb\Core\Settings\Filament\Resources\SettingResource;

class SettingsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());
        return $plugin;
    }       
    
    public function getId(): string
    {
        return 'settings';
    }

    public function register(Panel $panel): void
    {
        // Register the Group resource
        $panel->resources([
            SettingResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Any boot logic
    }

}
