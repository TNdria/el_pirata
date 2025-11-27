<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Endpoint de santé de l'API
     */
    public function health()
    {
        try {
            $startTime = microtime(true);
            
            // Test de connexion à la base de données
            DB::connection()->getPdo();
            $dbStatus = 'ok';
            
            // Test de connexion au cache
            Cache::put('health_check', 'ok', 60);
            $cacheStatus = Cache::get('health_check') === 'ok' ? 'ok' : 'error';
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'response_time_ms' => $responseTime,
                'services' => [
                    'database' => $dbStatus,
                    'cache' => $cacheStatus,
                ],
                'version' => '1.0.0',
                'environment' => app()->environment(),
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'unhealthy',
                'timestamp' => now()->toISOString(),
                'error' => $th->getMessage(),
            ], 503);
        }
    }

    /**
     * Métriques de performance
     */
    public function metrics()
    {
        try {
            $metrics = $this->cacheService->cachePerformanceMetrics(30);

            return response()->json([
                'status' => 'ok',
                'timestamp' => now()->toISOString(),
                'metrics' => [
                    'memory' => [
                        'current_usage_mb' => round($metrics['memory_usage'] / 1024 / 1024, 2),
                        'peak_usage_mb' => round($metrics['memory_peak'] / 1024 / 1024, 2),
                    ],
                    'performance' => [
                        'execution_time_ms' => round($metrics['execution_time'] * 1000, 2),
                        'db_queries_count' => count($metrics['db_queries']),
                    ],
                    'cache' => [
                        'hits' => $metrics['cache_hits'],
                        'misses' => $metrics['cache_misses'],
                        'hit_ratio' => $metrics['cache_hits'] + $metrics['cache_misses'] > 0 
                            ? round($metrics['cache_hits'] / ($metrics['cache_hits'] + $metrics['cache_misses']) * 100, 2)
                            : 0,
                    ],
                ],
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des métriques',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Statistiques de l'application
     */
    public function stats()
    {
        try {
            $stats = $this->cacheService->cacheStats();

            return response()->json([
                'status' => 'ok',
                'timestamp' => now()->toISOString(),
                'stats' => $stats,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Test de charge simple
     */
    public function loadTest()
    {
        $startTime = microtime(true);
        $iterations = 100;
        
        for ($i = 0; $i < $iterations; $i++) {
            // Simulation d'une requête simple
            DB::table('users')->count();
        }
        
        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;
        
        return response()->json([
            'status' => 'ok',
            'test' => 'load_test',
            'iterations' => $iterations,
            'total_time_seconds' => round($totalTime, 4),
            'avg_time_per_iteration_ms' => round(($totalTime / $iterations) * 1000, 2),
            'iterations_per_second' => round($iterations / $totalTime, 2),
        ]);
    }
}

