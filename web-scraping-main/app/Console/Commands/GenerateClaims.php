<?php

namespace App\Console\Commands;

use App\Models\Claim;
use App\Models\Patente;
use App\Models\Inventor;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class GenerateClaims extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-claims {term}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */


    public function handle()
    {
        $term = $this->argument('term');
        $scriptPath = base_path('public/scrapper_claims.js');
        $nodePath = '/home/ign/.nvm/versions/node/v22.6.0/bin/node';

        $process = new Process([$nodePath, $scriptPath, $term]);
        $process->setTimeout(3000);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $patent = json_decode($output, true);

        if (!is_array($patent)) {
            $this->error("Erro ao decodificar JSON");
            $this->line($output);
            return;
        }

        // Tenta encontrar a patente no banco
        $patente = Patente::where('patent_number', $patent['patentNumber'])->first();

        if (!$patente) {
            $this->warn("Patente não encontrada no banco: {$patent['patentNumber']}");
            return;
        }

        $claimsInseridas = 0;

        foreach ($patent['claims'] as $claim) {
            $claimExists = Claim::where('patent_id', $patente->id)
                ->where('claim', $claim)
                ->exists();

            if (!$claimExists) {
                Claim::create([
                    'patent_id' => $patente->id,
                    'claim' => $claim,
                ]);
                $claimsInseridas++;
            }
        }

        $this->info("Claims inseridas para patente {$patent['patentNumber']}: {$claimsInseridas}");
        $this->info("Importação de claims finalizada com sucesso.");
    }
}
