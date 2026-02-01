<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBook extends EditRecord
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Hydrate the cover_image field from the relationship
        if ($this->record->image) {
            $data['cover_image'] = $this->record->image->url;
        }

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $data['is_readable'] = ! empty($data['pdf_read']);
        $data['is_downloadable'] = ! empty($data['pdf_download']);
        $data['has_audio'] = ! empty($data['audio']);

        $record->update($data);

        if (isset($data['categories'])) {
            $record->categories()->sync($data['categories']);
        }

        if (array_key_exists('cover_image', $data)) {
            if (empty($data['cover_image'])) {
                $record->image()?->delete();
            } else {
                $record->image()->updateOrCreate(
                    ['imageable_id' => $record->id, 'imageable_type' => get_class($record)],
                    ['url' => $data['cover_image']]
                );
            }
        }

        return $record;
    }

    protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('index');
    }
}
