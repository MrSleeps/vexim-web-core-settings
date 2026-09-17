<?php

use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\DB;
use VEximweb\Core\Data\Models\Setting;
use VEximweb\Core\Settings\Filament\Resources\Pages\ManageAllSettings;

function settingsDnsField(Setting $setting): mixed
{
    $page = new class extends ManageAllSettings
    {
        public function exposeStringField(Setting $setting): mixed
        {
            return $this->getStringField($setting, 'Domain Admin DNS Access');
        }
    };

    return $page->exposeStringField($setting);
}

it('offers the supported domain admin DNS access policies', function () {
    $setting = new Setting();
    $setting->forceFill([
        'key' => 'domain_admin_dns_access',
        'value' => 'disabled',
        'type' => 'string',
        'description' => 'DNS policy',
    ]);

    $field = settingsDnsField($setting);

    expect($field)->toBeInstanceOf(Select::class)
        ->and($field->getOptions())->toBe([
            'disabled' => 'Disabled',
            'global_only' => 'Use global providers only',
            'global_and_own' => 'Use global providers + create their own',
        ]);
});

it('makes a bulk-updated DNS policy visible immediately after clearing the settings cache', function () {
    DB::table('vw_settings')->insert([
        'key' => 'domain_admin_dns_access',
        'value' => 'disabled',
        'type' => 'string',
        'description' => 'DNS policy',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(Setting::get('domain_admin_dns_access'))->toBe('disabled');

    DB::table('vw_settings')
        ->where('key', 'domain_admin_dns_access')
        ->update(['value' => 'global_only']);

    expect(Setting::get('domain_admin_dns_access'))->toBe('disabled');

    Setting::clearCache();

    expect(Setting::get('domain_admin_dns_access'))->toBe('global_only');
});
