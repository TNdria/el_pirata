<?php

namespace App\Console\Commands;

use App\Services\ImageCompressionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize {--directory=upload : Directory to optimize} {--format=webp : Output format} {--quality=85 : Compression quality}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize images in the specified directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = $this->option('directory');
        $format = $this->option('format');
        $quality = (int) $this->option('quality');

        $this->info("🖼️  Optimisation des images dans le répertoire: {$directory}");
        $this->info("📁 Format de sortie: {$format}");
        $this->info("🎯 Qualité: {$quality}%");

        $compressionService = new ImageCompressionService();
        
        try {
            $result = $compressionService->optimizeDirectory($directory);
            
            $this->info("✅ Optimisation terminée !");
            $this->table(
                ['Métrique', 'Valeur'],
                [
                    ['Images optimisées', $result['optimized']],
                    ['Erreurs', $result['errors']],
                    ['Total de fichiers', $result['total']],
                ]
            );

            if ($result['errors'] > 0) {
                $this->warn("⚠️  {$result['errors']} erreurs détectées. Vérifiez les logs pour plus de détails.");
            }

        } catch (\Throwable $th) {
            $this->error("❌ Erreur lors de l'optimisation: " . $th->getMessage());
            return 1;
        }

        return 0;
    }
}

