<div>
    <x-filament-panels::page>
        <form wire:submit="save" class="space-y-6">
            <div>
                {{ $this->form }}
            </div>
            
            <div class="mt-6 flex gap-3 justify-end">
                <x-filament::button 
                    type="submit" 
                    color="primary" 
                >
                    Save All Settings
                </x-filament::button>
            </div>
        </form>
    </x-filament-panels::page>
</div>