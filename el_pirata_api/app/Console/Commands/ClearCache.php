<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

class ClearCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-smart {--type=all : Type of cache to clear (all, hunt, user, stats)} {--id= : ID for specific cache clearing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear specific cache types intelligently';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $id = $this->option('id');
        $cacheService = new CacheService();

        switch ($type) {
            case 'all':
                $this->info('🧹 Nettoyage complet du cache...');
                \Artisan::call('cache:clear');
                $this->info('✅ Cache complètement nettoyé');
                break;

            case 'hunt':
                if ($id) {
                    $this->info("🎯 Nettoyage du cache pour la chasse ID: {$id}");
                    $cacheService->invalidateHuntCache($id);
                    $this->info('✅ Cache de la chasse nettoyé');
                } else {
                    $this->error('❌ ID de chasse requis pour le type "hunt"');
                    return 1;
                }
                break;

            case 'user':
                if ($id) {
                    $this->info("👤 Nettoyage du cache pour l'utilisateur ID: {$id}");
                    $cacheService->invalidateUserCache($id);
                    $this->info('✅ Cache utilisateur nettoyé');
                } else {
                    $this->error('❌ ID utilisateur requis pour le type "user"');
                    return 1;
                }
                break;

            case 'stats':
                $this->info('📊 Nettoyage du cache des statistiques...');
                \Cache::forget('stats.global');
                $this->info('✅ Cache des statistiques nettoyé');
                break;

            default:
                $this->error("❌ Type de cache invalide: {$type}");
                $this->info('Types disponibles: all, hunt, user, stats');
                return 1;
        }

        return 0;
    }
}

