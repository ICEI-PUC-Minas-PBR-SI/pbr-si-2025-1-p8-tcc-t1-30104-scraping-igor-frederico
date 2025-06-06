<?php

namespace App\Livewire;

use App\Models\Patente;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PatentesTable extends PowerGridComponent
{
    public string $tableName = 'patentes-table';
    public $mensagem;

    public function datasource(): Collection
    {
        return Patente::all();
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()->showSearchInput()->includeViewOnTop('components.header-controle'),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('farmaco')
            ->add('document_id')
            ->add('date_published_formatted', fn ($patente) => Carbon::parse($patente->date_published)->format('d/m/Y'))
            ->add('title')
            ->add('patent_number')
            ->add('page_count')
            ->add('type');
    }

    public function columns(): array
    {
        return [

            Column::action('Ações'),
            
            Column::make('ID', 'id')
                ->sortable()
                ->searchable(),

            Column::make('Fármaco', 'farmaco')
                ->sortable()
                ->searchable(),

            Column::make('Documento ID', 'document_id')
                ->sortable()
                ->searchable(),

            Column::make('Data de Publicação', 'date_published_formatted')
                ->sortable(),

            Column::make('Título', 'title')
                ->sortable()
                ->searchable(),

            Column::make('Número da Patente', 'patent_number')
                ->sortable()
                ->searchable(),

            Column::make('Número de Páginas', 'page_count')
                ->sortable(),

            Column::make('Tipo', 'type')
                ->sortable()
                ->searchable(),

          
        ];
    }


    #[\Livewire\Attributes\On('execute-comand-claim')]
    public function executarComandoArtisanV3($name)
    {
        Artisan::call('generate-claims', [
            'term' => $name,
        ]);

        $this->mensagem = "Comando executado com sucesso para a PATENTE : $name.";
    }


    public function actions($row): array
    {
        return [
            Button::add('get_farmacos')
                ->slot('<span class="mr-2">⚡</span> Gerar Claims')
                ->class('bg-red-500 text-white font-bold p-2 rounded')
                ->dispatch('execute-comand-claim', ['name' => $row->patent_number]),

        ];
    }
}
