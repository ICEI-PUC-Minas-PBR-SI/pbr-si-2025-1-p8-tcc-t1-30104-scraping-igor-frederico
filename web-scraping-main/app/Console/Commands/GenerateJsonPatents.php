<?php

namespace App\Console\Commands;

use App\Models\Claim;
use App\Models\Patente;
use App\Models\Inventor;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class GenerateJsonPatents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-json-patents {term}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    /* public function handle()
    {
        $term = $this->ask('Digite o nome da patente (ex: aspirin)');
        $url = "node public/scrapper_att.js \"{$term}\"";
        $output = shell_exec($url);
        $patents = json_decode($output, true);

        foreach ($patents as $patent) {

            $patenteExistente = Patente::where('document_id', $patent['documentId'])->exists();
            if ($patenteExistente) {
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
    } */

    public function handle()
    {
        $term = $this->argument('term'); 
        $term = $this->argument('term');
        $scriptPath = base_path('public/scrapper_att.js');
       $nodePath = '/home/ign/.nvm/versions/node/v22.6.0/bin/node';

        $process = new Process([$nodePath, $scriptPath, $term]);
        $process->setTimeout(3000);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $patents = json_decode($output, true);

       
        foreach ($patents as $patent) {

            $patenteExistente = Patente::where('document_id', $patent['documentId'])->exists();
            if ($patenteExistente) {
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


    