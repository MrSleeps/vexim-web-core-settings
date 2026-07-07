<?php

namespace VEximweb\Core\Settings\Filament\Resources\Pages;

use VEximweb\Core\Settings\Filament\Resources\SettingResource;
use VEximweb\Core\Data\Models\Setting;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ListSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;
    
    protected static string $resource = SettingResource::class;
    
    protected string $view = 'settings::settings.manage-all-settings';
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $settings = Setting::all();
        $this->form->fill($settings->pluck('value', 'key')->toArray());
    }
    
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema($this->getFormSchema())
            ->statePath('data');
    }
    
    protected function getFormSchema(): array
    {
        $settings = Setting::all();
        
    // Debug: Output what we're working with
    \Log::info('=== Settings Debug ===');
    \Log::info('Total settings: ' . $settings->count());
    \Log::info('Categories found: ' . $settings->pluck('category')->unique()->filter()->values()->implode(', '));        
        
  
        // Group all settings by category
        $groupedByCategory = $settings->groupBy('category')->sortKeys();
        
        $tabs = [];
        
        foreach ($groupedByCategory as $category => $categorySettings) {
            // Get the first setting in this category to retrieve the icon
            $firstSetting = $categorySettings->first();
            
            // Use icon if it exists, otherwise use a default based on category name
            $icon = $firstSetting?->icon;
            if (empty($icon)) {
                // Fallback icons based on category name
                $icon = match($category) {
                    'servers' => 'heroicon-o-server',
                    'accounts' => 'heroicon-o-users',
                    'domain' => 'heroicon-o-globe-alt',
                    'website' => 'heroicon-o-globe-alt',
                    'mailman' => 'heroicon-o-envelope',
                    'spam' => 'heroicon-o-shield-check',
                    'emailmessages' => 'heroicon-o-envelope',
                    default => 'heroicon-o-cog-6-tooth',
                };
            }
            
            // Create a tab for each category
            $tabName = ucwords(str_replace('_', ' ', $category ?: 'General'));
            
            $tabs[] = Tab::make($category ?: 'general')
                ->label($tabName)
                ->schema($this->getCategorySchema($categorySettings))
                ->icon($icon);
        }
        
        // If no settings exist, show a default tab
        if (empty($tabs)) {
            $tabs[] = Tab::make('general')
                ->label('General')
                ->schema([
                    Section::make('No settings available')
                        ->schema([])
                ])
                ->icon('heroicon-o-cog-6-tooth');
        }
        
        return [
            Tabs::make('Settings Tabs')
                ->tabs($tabs)
                ->columnSpanFull(),
        ];
    }
    
    /**
     * Get the schema for a category's settings
     */
    protected function getCategorySchema($categorySettings): array
    {
        if ($categorySettings->isEmpty()) {
            return [
                Section::make('No settings in this category')
                    ->schema([])
            ];
        }
        
        $fields = [];
        
        foreach ($categorySettings as $setting) {
            $fields[] = $this->createFieldForSetting($setting);
        }
        
        return [
            Section::make()
                ->schema($fields)
                ->columns(2),
        ];
    }
    
    protected function createFieldForSetting(Setting $setting)
    {
        $label = ucwords(str_replace('_', ' ', $setting->key));
        
        return match($setting->type) {
            'boolean' => Toggle::make($setting->key)
                ->label($label)
                ->helperText($setting->description)
                ->default((bool) $setting->value),
                
            'integer' => TextInput::make($setting->key)
                ->label($label)
                ->helperText($setting->description)
                ->numeric()
                ->integer()
                ->default((int) $setting->value),
                
            'string' => $this->getStringField($setting, $label),
            
            default => TextInput::make($setting->key)
                ->label($label)
                ->helperText($setting->description)
                ->default($setting->value),
        };
    }
    
    protected function getStringField(Setting $setting, string $label)
    {
        // Special handling for specific keys
        if ($setting->key === 'crypt_scheme') {
            return Select::make($setting->key)
                ->label($label)
                ->helperText($setting->description)
                ->options([
                    'sha512' => 'SHA512',
                    'bcrypt' => 'Bcrypt',
                ])
                ->default($setting->value);
        }
        
        // Use textarea for long values or specific keys
        if (strlen($setting->value) > 100 || str_contains($setting->key, 'welcome')) {
            return Textarea::make($setting->key)
                ->label($label)
                ->helperText($setting->description)
                ->default($setting->value)
                ->rows(5)
                ->columnSpanFull();
        }
        
        return TextInput::make($setting->key)
            ->label($label)
            ->helperText($setting->description)
            ->default($setting->value);
    }
    
    public function save(): void
    {
        try {
            foreach ($this->form->getState() as $key => $value) {
                if (is_bool($value)) {
                    $value = $value ? '1' : '0';
                }
                
                Setting::where('key', $key)->update(['value' => (string) $value]);
            }
            
            Notification::make()
                ->title('All settings saved successfully')
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving settings')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save All Settings')
                ->submit('save')
                ->color('primary'),
                
            Action::make('back')
                ->label('Back to List')
                ->url(SettingResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}