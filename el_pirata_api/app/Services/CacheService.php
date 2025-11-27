<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheService
{
    /**
     * Cache les données des chasses avec TTL intelligent
     */
    public function cacheHuntings($key = 'huntings.all', $ttl = 3600)
    {
        return Cache::remember($key, $ttl, function () {
            return DB::table('huntings')
                ->where('is_archived', 0)
                ->where('is_published', 1)
                ->orderBy('start_date', 'desc')
                ->get();
        });
    }

    /**
     * Cache les statistiques avec TTL court
     */
    public function cacheStats($key = 'stats.global', $ttl = 300)
    {
        return Cache::remember($key, $ttl, function () {
            return [
                'users_count' => DB::table('users')->where('is_archived', 0)->count(),
                'huntings_count' => DB::table('huntings')->where('is_archived', 0)->count(),
                'enigmas_count' => DB::table('enigmas')->where('is_archived', 0)->count(),
                'transactions_count' => DB::table('transactions')->where('status', 'validated')->count(),
                'tickets_open' => DB::table('tickets')->where('status', 'open')->count(),
                'refunds_pending' => DB::table('refund_requests')->where('status', 'pending')->count(),
            ];
        });
    }

    /**
     * Cache les classements avec TTL moyen
     */
    public function cacheLeaderboard($huntId, $ttl = 1800)
    {
        $key = "leaderboard.hunt.{$huntId}";
        
        return Cache::remember($key, $ttl, function () use ($huntId) {
            $hunting = DB::table('huntings')->find($huntId);
            if (!$hunting) return [];

            $totalEnigmas = DB::table('enigmas')
                ->where('hunting_id', $huntId)
                ->where('is_archived', 0)
                ->count();

            return DB::table('enigma_user')
                ->join('enigmas', 'enigma_user.enigma_id', '=', 'enigmas.id')
                ->join('users', 'enigma_user.user_id', '=', 'users.id')
                ->where('enigmas.hunting_id', $huntId)
                ->whereNotNull('enigma_user.completed_at')
                ->select(
                    'enigma_user.user_id',
                    'users.name',
                    DB::raw('COUNT(enigma_user.id) as completed_count'),
                    DB::raw("MAX(enigma_user.completed_at) as last_resolved"),
                    DB::raw($totalEnigmas . ' as total_enigmas'),
                    DB::raw('ROUND((COUNT(enigma_user.id) / ' . $totalEnigmas . ') * 100, 0) as completion_percent')
                )
                ->groupBy('enigma_user.user_id', 'users.name')
                ->orderByDesc('completed_count')
                ->orderBy('last_resolved', 'asc')
                ->limit(50)
                ->get()
                ->map(function ($item, $index) {
                    $item->rank = $index + 1;
                    return $item;
                });
        });
    }

    /**
     * Cache les énigmes avec TTL long
     */
    public function cacheEnigmas($huntId = null, $ttl = 7200)
    {
        $key = $huntId ? "enigmas.hunt.{$huntId}" : 'enigmas.free';
        
        return Cache::remember($key, $ttl, function () use ($huntId) {
            $query = DB::table('enigmas')
                ->where('is_archived', 0)
                ->where('is_active', 1);

            if ($huntId) {
                $query->where('hunting_id', $huntId);
            } else {
                $query->whereNull('hunting_id');
            }

            return $query->orderBy('level', 'asc')->get();
        });
    }

    /**
     * Cache les codes VIP d'un utilisateur
     */
    public function cacheUserVipCodes($userId, $ttl = 3600)
    {
        $key = "vip_codes.user.{$userId}";
        
        return Cache::remember($key, $ttl, function () use ($userId) {
            return DB::table('promos')
                ->where('user_id', $userId)
                ->where('type', 'vip_code')
                ->where('is_used', false)
                ->where(function ($query) {
                    $query->whereNull('valid_until')
                        ->orWhere('valid_until', '>', now());
                })
                ->get();
        });
    }

    /**
     * Invalide le cache d'un utilisateur
     */
    public function invalidateUserCache($userId)
    {
        $keys = [
            "vip_codes.user.{$userId}",
            "user.profile.{$userId}",
            "user.transactions.{$userId}",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Invalide le cache d'une chasse
     */
    public function invalidateHuntCache($huntId)
    {
        $keys = [
            "leaderboard.hunt.{$huntId}",
            "enigmas.hunt.{$huntId}",
            "huntings.all",
            "stats.global",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Cache intelligent avec tags
     */
    public function cacheWithTags($key, $callback, $ttl = 3600, $tags = [])
    {
        if (empty($tags)) {
            return Cache::remember($key, $ttl, $callback);
        }

        // Implémentation basique des tags (Redis supporte les tags)
        $taggedKey = implode(':', $tags) . ':' . $key;
        return Cache::remember($taggedKey, $ttl, $callback);
    }

    /**
     * Invalide le cache par tags
     */
    public function invalidateByTags($tags)
    {
        if (empty($tags)) return;

        $pattern = implode(':', $tags) . ':*';
        
        // Pour Redis, on peut utiliser SCAN pour trouver les clés
        if (config('cache.default') === 'redis') {
            $redis = Cache::getStore()->getRedis();
            $keys = $redis->keys($pattern);
            
            if (!empty($keys)) {
                $redis->del($keys);
            }
        }
    }

    /**
     * Cache les résultats de recherche avec TTL court
     */
    public function cacheSearchResults($query, $type, $results, $ttl = 600)
    {
        $key = "search.{$type}." . md5($query);
        Cache::put($key, $results, $ttl);
        return $results;
    }

    /**
     * Récupère les résultats de recherche depuis le cache
     */
    public function getCachedSearchResults($query, $type)
    {
        $key = "search.{$type}." . md5($query);
        return Cache::get($key);
    }

    /**
     * Cache les métriques de performance
     */
    public function cachePerformanceMetrics($ttl = 60)
    {
        return Cache::remember('performance.metrics', $ttl, function () {
            return [
                'memory_usage' => memory_get_usage(true),
                'memory_peak' => memory_get_peak_usage(true),
                'execution_time' => microtime(true) - LARAVEL_START,
                'db_queries' => DB::getQueryLog(),
                'cache_hits' => Cache::getStore()->getRedis()->info()['keyspace_hits'] ?? 0,
                'cache_misses' => Cache::getStore()->getRedis()->info()['keyspace_misses'] ?? 0,
            ];
        });
    }
}

