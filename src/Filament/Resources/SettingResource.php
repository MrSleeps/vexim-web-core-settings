<?php
namespace VEximweb\Core\Settings\Filament\Resources;

use VEximweb\Core\Settings\Filament\Resources\Pages\CreateSetting;
use VEximweb\Core\Settings\Filament\Resources\Pages\EditSetting;
use VEximweb\Core\Settings\Filament\Resources\Pages\ListSettings;
use VEximweb\Core\Settings\Filament\Resources\Pages\ManageAllSettings;
use VEximweb\Core\Settings\Filament\Resources\Schemas\SettingForm;
use VEximweb\Core\Settings\Filament\Resources\Tables\SettingsTable;
use VEximweb\Core\Data\Models\Setting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    
    protected static ?string $slug = 'website-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;

    protected static ?string $recordTitleAttribute = 'System Settings';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Website Management';
    
    protected static ?int $navigationSort = 35;

    public static function form(Schema $schema): Schema
    {
        return SettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSettings::route('/'),
            'create' => CreateSetting::route('/create'),
            'edit' => EditSetting::route('/{record}/edit'),
            'manage' => ManageAllSettings::route('/manage'),
        ];
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['key','value','description'];
    }        
}