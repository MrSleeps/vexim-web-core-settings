<?php
namespace VEximweb\Core\Settings;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use VEximweb\Core\Data\Repositories\Interfaces\SettingRepositoryInterface;
use VEximweb\Core\Data\Repositories\SettingRepository;
use VEximweb\Core\Settings\Services\EmailServerSettingsService;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/settings.php',
            'settings'
        );
        
        // Bind plugin repositories
        $this->bindRepositories();
        
        // Bind plugin Services
        $this->bindServices();        
        
        Panel::configureUsing(function (Panel $panel) {
            $panel->plugin(SettingsPlugin::make());
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'settings');
        //$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->publishes([
            __DIR__ . '/../config/settings.php' => config_path('settings.php'),
        ], 'settings-config');
        if ($this->app->runningInConsole()) {
            $this->commands([

            ]);
        }
    }
    
    /**
     * Bind all repositories to their interfaces.
     */
    protected function bindRepositories(): void
    {
        $this->app->bind(SettingRepositoryInterface::class, SettingRepository::class);
    }      
    
    /**
     * Bind all services to the container.
     */
    protected function bindServices(): void
    {
        $this->app->singleton(EmailServerSettingsService::class, function ($app) {
            return new EmailServerSettingsService();
        });     
    }        
}
