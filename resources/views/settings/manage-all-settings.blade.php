<div>
    <x-filament-panels::page>
        <form wire:submit="save" class="space-y-6">
            {{-- Add wire:key with a timestamp or random value --}}
            <div wire:key="settings-form-{{ Str::random(8) }}">
                {{ $this->form }}
            </div>
            
            <div class="mt-6 flex gap-3 justify-end">
                <x-filament::button 
                    type="submit" 
                    color="primary" 
                    icon="heroicon-o-check"
                >
                    Save All Settings
                </x-filament::button>
                
                <x-filament::button 
                    href="{{ \VEximweb\Core\Settings\Filament\Resources\SettingResource::getUrl('index') }}"
                    tag="a"
                    color="gray"
                    icon="heroicon-o-arrow-left"
                >
                    Back to List
                </x-filament::button>
            </div>
        </form>
    </x-filament-panels::page>
</div>