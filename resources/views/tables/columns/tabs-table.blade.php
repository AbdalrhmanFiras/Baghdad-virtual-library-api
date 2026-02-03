<x-filament::page>
    <x-filament::tabs>
        <x-filament::tab name="draft" label="Draft">
            <x-filament-tables::table :records="$draftBooks">
                {{ $this->tableColumns() }}
            </x-filament-tables::table>
        </x-filament::tab>
        <x-filament::tab name="published" label="Published">
            <x-filament-tables::table :records="$publishedBooks">
                {{ $this->tableColumns() }}
            </x-filament-tables::table>
        </x-filament::tab>
        <x-filament::tab name="archived" label="Archived">
            <x-filament-tables::table :records="$archivedBooks">
                {{ $this->tableColumns() }}
            </x-filament-tables::table>
        </x-filament::tab>
    </x-filament::tabs>
</x-filament::page>
