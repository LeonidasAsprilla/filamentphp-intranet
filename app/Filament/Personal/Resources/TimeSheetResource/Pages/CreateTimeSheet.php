<?php

namespace App\Filament\Personal\Resources\TimeSheetResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Personal\Resources\TimeSheetResource;

class CreateTimeSheet extends CreateRecord
{
    protected static string $resource = TimeSheetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::user()->id;
    
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
