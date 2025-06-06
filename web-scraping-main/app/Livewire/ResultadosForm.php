<?php

namespace App\Livewire;

use App\Models\Patente;
use Livewire\Component;
use App\Models\Inventor;
use Illuminate\Support\Facades\DB;

class ResultadosForm extends Component
{

    public $data;

    public function mount()
    {
        $this->data = [
            'por_dia' => Patente::select('date_published', DB::raw('COUNT(*) as total'))
                ->groupBy('date_published')
                ->orderBy('date_published')
                ->get(),

            'por_semana' => Patente::select(DB::raw('YEARWEEK(date_published, 1) as semana'), DB::raw('COUNT(*) as total'))
                ->groupBy('semana')
                ->orderBy('semana')
                ->get(),

            'por_categoria' => Patente::select('farmaco', DB::raw('COUNT(*) as total'))
                ->groupBy('farmaco')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),

            // Farmacos filtrados
            'por_farmaco' => Patente::select('farmaco', DB::raw('COUNT(*) as total'))
                ->whereIn('farmaco', [
                    'paclitaxel',
                    'metformin',
                    'lisinopril',
                    'albuterol',
                    'methotrexate',
                ])
                ->groupBy('farmaco')
                ->orderByDesc('total')
                ->get(),

            // Inventores (sem alterações)
            'por_inventor' => Inventor::select('name', DB::raw('COUNT(*) as total'))
                ->groupBy('name')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),


            // Doenças filtradas
            'por_doenca' => Patente::select('farmaco', DB::raw('COUNT(*) as total'))
                ->whereIn('farmaco', [
                    'cancer',
                    'diabetes',
                    'hypertension',
                    'asthma',
                    'arthritis',
                ])
                ->groupBy('farmaco')
                ->orderByDesc('total')
                ->get(),
        ];
    }



    public function render()
    {
        return view('livewire.resultados-form');
    }
}
