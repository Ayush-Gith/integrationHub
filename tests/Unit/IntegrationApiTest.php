<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Integration;

class IntegrationApiTest extends TestCase
{
    use RefreshDatabase;

    protected $adminToken;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role'  => 'admin',
            'password' => bcrypt('password')
        ]);
        $this->adminToken = auth('api')->login($admin);

        // Create Normal User
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'role'  => 'user',
            'password' => bcrypt('password')
        ]);
        $this->userToken = auth('api')->login($user);
    }

    /** @test */
    public function admin_can_list_integrations()
    {
        Integration::factory()->count(2)->create();

        $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/integrations')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    /** @test */
    public function admin_can_create_integration()
    {
        $payload = [
            'platform' => 'Shopify',
            'api_key' => 'test_key'
        ];

        $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/integrations', $payload)
            ->assertStatus(201)
            ->assertJsonFragment(['platform' => 'Shopify']);
    }

    /** @test */
    public function user_cannot_create_integration()
    {
        $payload = [
            'platform' => 'Wix',
            'api_key' => 'test_key'
        ];

        $this->withHeader('Authorization', 'Bearer ' . $this->userToken)
            ->postJson('/api/integrations', $payload)
            ->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_integrations()
    {
        $this->getJson('/api/integrations')
            ->assertStatus(401);
    }

    /** @test */
    public function admin_can_update_integration()
    {
        $integration = Integration::factory()->create();

        $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->putJson("/api/integrations/{$integration->id}", ['platform' => 'UpdatedPlatform'])
            ->assertStatus(200)
            ->assertJsonFragment(['platform' => 'UpdatedPlatform']);
    }

    /** @test */
    public function admin_can_delete_integration()
    {
        $integration = Integration::factory()->create();

        $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->deleteJson("/api/integrations/{$integration->id}")
            ->assertStatus(204);
    }
}