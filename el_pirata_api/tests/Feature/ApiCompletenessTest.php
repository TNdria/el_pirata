<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\hunting;
use App\Models\enigmas;
use App\Models\Ticket;
use App\Models\RefundRequest;
use App\Models\promo;

class ApiCompletenessTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test de l'endpoint de santé
     */
    public function test_health_endpoint()
    {
        $response = $this->get('/api/health');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'timestamp',
                    'response_time_ms',
                    'services' => [
                        'database',
                        'cache'
                    ],
                    'version',
                    'environment'
                ]);
    }

    /**
     * Test du système de tickets
     */
    public function test_ticket_system()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/user/tickets', [
                'subject' => 'Test Ticket',
                'message' => 'Test message',
                'priority' => 'normal'
            ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'state',
                    'message',
                    'ticket' => [
                        'id',
                        'subject',
                        'message',
                        'status',
                        'priority'
                    ]
                ]);
    }

    /**
     * Test du système de remboursement
     */
    public function test_refund_system()
    {
        $user = User::factory()->create();
        
        // Créer une transaction de test
        $transaction = \App\Models\transactions::factory()->create([
            'user_id' => $user->id,
            'status' => 'validated'
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/user/refunds', [
                'transaction_id' => $transaction->id,
                'reason' => 'medical',
                'description' => 'Test refund request'
            ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'state',
                    'message',
                    'refund_request' => [
                        'id',
                        'reason',
                        'description',
                        'status'
                    ]
                ]);
    }

    /**
     * Test du système de codes VIP
     */
    public function test_vip_code_system()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/user/vip-codes');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'state',
                    'vip_codes'
                ]);
    }

    /**
     * Test de la validation intelligente
     */
    public function test_intelligent_validation()
    {
        $user = User::factory()->create();
        $enigma = enigmas::factory()->create([
            'response' => 'pirate'
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/user/enigmas/check/answer', [
                'answer' => 'pirate',
                'id_engima' => $enigma->id
            ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'is_correct',
                    'validation_method',
                    'confidence'
                ]);
    }

    /**
     * Test du système de blocage utilisateur
     */
    public function test_user_blocking_system()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->postJson('/api/users/block', [
                'user_id' => $user->id,
                'reason' => 'Test blocking'
            ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'state',
                    'message',
                    'user' => [
                        'id',
                        'is_blocked'
                    ]
                ]);
    }

    /**
     * Test des métriques de performance
     */
    public function test_performance_metrics()
    {
        $response = $this->get('/api/metrics');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'timestamp',
                    'metrics' => [
                        'memory' => [
                            'current_usage_mb',
                            'peak_usage_mb'
                        ],
                        'performance' => [
                            'execution_time_ms',
                            'db_queries_count'
                        ],
                        'cache' => [
                            'hits',
                            'misses',
                            'hit_ratio'
                        ]
                    ]
                ]);
    }

    /**
     * Test de la sécurité (CORS, rate limiting)
     */
    public function test_security_headers()
    {
        $response = $this->get('/api/health');
        
        $response->assertHeader('Access-Control-Allow-Origin');
        $response->assertHeader('Access-Control-Allow-Methods');
    }

    /**
     * Test de l'authentification
     */
    public function test_authentication()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'state' => 'error'
                ]);
    }

    /**
     * Test de la compression d'images
     */
    public function test_image_compression()
    {
        $user = User::factory()->create();
        
        // Créer une image base64 de test
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/user/avatar/update', [
                'avatar' => $base64Image
            ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'state',
                    'message',
                    'user' => [
                        'avatar'
                    ]
                ]);
    }
}

