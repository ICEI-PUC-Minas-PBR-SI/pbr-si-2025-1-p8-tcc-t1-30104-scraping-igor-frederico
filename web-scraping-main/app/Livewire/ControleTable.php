<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\Artisan;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use PowerComponents\LivewirePowerGrid\Button;

use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;



final class ControleTable extends PowerGridComponent
{
    public string $tableName = 'controle-table-6s9pmg-table';
    public $mensagem;
    use WithExport;


    public function datasource(): Collection
    {
        $data = [
            '🧪 Fármacos específicos' => [
                'paclitaxel',
                'metformin',
                'lisinopril',
                'albuterol',
                'methotrexate'
            ],
            '🧫 Doenças ou condições' => [
                'cancer',
                'diabetes',
                'hypertension',
                'asthma',
                'arthritis'
            ],
        ];

        $result = [];
        $id = 1;
        foreach ($data as $category => $items) {
            foreach ($items as $item) {
                $result[] = [
                    'id' => $id++,
                    'name' => $item,
                    'category' => $category,
                    'created_at' => now(),
                ];
            }
        }

        return collect($result);
    }


    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable(fileName: 'my-export-file')
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header()
                ->includeViewOnTop('components.header-controle')
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
            ->add('name')
            ->add('category');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Category', 'category')
                ->searchable()
                ->sortable(),

            Column::action('Action'),

        ];
    }


    #[\Livewire\Attributes\On('execute-comand')]
    public function executarComandoArtisan($name)
    {
        Artisan::call('generate-json-patents-janeiro', [
            'term' => $name,
        ]);

        $this->mensagem = "Comando executado com sucesso para o fármaco: $name.";
    }

    #[\Livewire\Attributes\On('execute-comand-v2')]
    public function executarComandoArtisanV2($name)
    {
        Artisan::call('generate-json-patents-static', [
            'term' => $name,
            'pages' => 2,
        ]);

        $this->mensagem = "Comando executado com sucesso para o fármaco: $name.";
    }


    #[\Livewire\Attributes\On('execute-comand-v3')]
    public function executarComandoArtisanV3($name)
    {
        Artisan::call('generate-json-patents', [
            'term' => $name,
        ]);

        $this->mensagem = "Comando executado com sucesso para o fármaco: $name.";
    }


    public function actions($row): array
    {
        return [
            Button::add('get_farmacos')
                ->slot('<span class="mr-2">⚡</span> Gerar PATENTES 01/2025')
                ->class('bg-red-500 text-white font-bold p-2 rounded')
                ->dispatch('execute-comand', ['name' => $row->name]),

            /* Button::add('get_farmacos')
                ->slot('<span class="mr-2">⚡</span> Gerar PATENTES PAGINADOS')
                ->class('bg-blue-500 text-white font-bold p-2 rounded')
                ->dispatch('execute-comand-v2', ['name' => $row->name]),

            Button::add('get_farmacos')
                ->slot('<span class="mr-2">⚡</span> Gerar PATENTES Primeira Page')
                ->class('bg-blue-500 text-white font-bold p-2 rounded')
                ->dispatch('execute-comand-v3', ['name' => $row->name]), */
        ];
    }
}
