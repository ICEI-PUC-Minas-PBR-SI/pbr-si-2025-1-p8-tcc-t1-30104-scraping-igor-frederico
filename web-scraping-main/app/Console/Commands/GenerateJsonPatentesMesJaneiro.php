<?php

namespace App\Console\Commands;

use App\Models\Patente;
use App\Models\Inventor;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GenerateJsonPatentesMesJaneiro extends Command
{
    protected $signature = 'generate-json-patents-janeiro {term}';
    protected $description = 'Importa patentes do USPTO e salva no banco de dados.';

    public function handle()
    {
        $term = $this->argument('term');
        $scriptPath = base_path('public/new.js');
        $nodePath = '/home/ign/.nvm/versions/node/v22.6.0/bin/node';

        $process = new Process([$nodePath, $scriptPath, $term]);
        $process->setTimeout(20000);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $patents = json_decode($output, true);

        foreach ($patents as $patent) {
            

            $patente = Patente::create([
                'farmaco' => $term,
                'document_id' => $patent['documentId'],
                'date_published' => $patent['datePublished'],
                'title' => $patent['title'],
                'patent_number' => $patent['patentNumber'],
                'page_count' => $patent['pageCount'] ?? null,
                'type' => $patent['type'] ?? null,
            ]);

            if (isset($patent['inventors'])) {
                $inventorsRaw = $patent['inventors'];

                
                if (is_string($inventorsRaw)) {
                   
                    $inventorNames = explode(';', str_replace('et al.', '', $inventorsRaw));
                } else {
                    $inventorNames = [];
                }
                $inventorNames = array_map('trim', $inventorNames);
                foreach ($inventorNames as $inventorName) {
                    if (!empty($inventorName)) {
                        Inventor::create([
                            'patent_id' => $patente->id,
                            'name' => $inventorName,
                        ]);
                    }
                }
            }
        }

        $this->info("Importação finalizada com sucesso.");
    }
}
