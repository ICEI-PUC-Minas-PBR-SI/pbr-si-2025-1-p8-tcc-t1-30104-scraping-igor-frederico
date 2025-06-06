<?php

namespace App\Livewire;

use App\Models\Claim;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ClaimsTable extends PowerGridComponent
{
    public string $tableName = 'claims-table';

    public function datasource(): Collection
    {
        return Claim::with('patente')->get();
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('patent_id')
            ->add('claim')
            ->add('farmaco', fn ($claim) => optional($claim->patente)->farmaco)
            ->add('document_id', fn ($claim) => optional($claim->patente)->document_id)
            ->add('date_published_formatted', fn ($claim) => optional($claim->patente)?->date_published
                ? Carbon::parse($claim->patente->date_published)->format('d/m/Y')
                : '')
            ->add('title', fn ($claim) => optional($claim->patente)->title)
            ->add('patent_number', fn ($claim) => optional($claim->patente)->patent_number);
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->searchable(),

            Column::make('Reivindicação', 'claim')
                ->sortable()
                ->searchable(),

            Column::make('Fármaco', 'farmaco')
                ->sortable()
                ->searchable(),

            Column::make('Doc. ID', 'document_id')
                ->sortable()
                ->searchable(),

            Column::make('Data Publicação', 'date_published_formatted'),

            Column::make('Título', 'title')
                ->sortable()
                ->searchable(),

            Column::make('Número da Patente', 'patent_number')
                ->sortable()
                ->searchable(),
        ];
    }
}
