<?php

namespace App\Console\Commands;

use Log;
use App\Models\Claim;
use App\Models\Patente;
use App\Models\Inventor;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GenerateJsonPatentsStatic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-json-patents-static {term} {pages=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        $term = $this->argument('term');
        $pages = $this->argument('pages');

        $scriptPath = base_path('public/scrapper.js');
        $nodePath = '/home/ign/.nvm/versions/node/v22.6.0/bin/node';

        // Passando os parâmetros para o script node
        $process = new Process([$nodePath, $scriptPath, $term, $pages]);
        $process->setTimeout(3000);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $patents = json_decode($output, true);

        foreach ($patents as $patent) {
            $patenteExistente = Patente::where('document_id', $patent['documentId'])->exists();

            $patente_obj = Patente::where('document_id', $patent['documentId'])->first(); 
            if (($patenteExistente) && ($patente_obj->farmaco == $patent['farmaco'])) {
                $this->warn("Patente já registrada: {$patent['documentId']}");
                continue;
            }

            $patente = Patente::create([
                'farmaco' => $patent['farmaco'],
                'document_id' => $patent['documentId'],
                'date_published' => $patent['datePublished'],
                'title' => $patent['title'],
                'patent_number' => $patent['patentNumber'],
                'page_count' => $patent['pageCount'] ?? null,
                'type' => $patent['type'] ?? null,
            ]);

            foreach ($patent['inventors'] as $invetor) {
                preg_match('/^(.*?); (.*?) (.*?), (\w{2})$/', $invetor, $matches);
                $surname = $matches[1] ?? null;
                $name = $matches[2] ?? null;
                $city = $matches[3] ?? null;
                $state = $matches[4] ?? null;

                Inventor::create([
                    'patent_id' => $patente->id,
                    'name' => trim("{$surname} {$name}"),
                    'city' => $city,
                    'state' => $state,
                ]);
            }

            foreach ($patent['claims'] as $claim) {
                Claim::create([
                    'patent_id' => $patente->id,
                    'claim' => $claim['claim'],
                ]);
            }
        }

        $this->info("Importação finalizada com sucesso.");
    }
}
