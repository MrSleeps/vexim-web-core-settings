<?php

namespace VEximweb\Core\Settings\Filament\Resources\Pages;

use VEximweb\Core\Settings\Filament\Resources\SettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
