<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Webhook;

class WebhookApiTest extends TestCase
{
    use RefreshDatabase;

    protected $adminToken;
    protected $userToken;

    public function test_webhook_can_be_received()
    {
        $response = $this->postJson('/api/webhooks/shopify', [
            'event' => 'product_created',
            'data' => ['id' => 123, 'title' => 'Test Product']
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('webhooks', [
            'platform' => 'shopify',
            'status' => 'received'
        ]);
    }

    public function test_admin_can_view_webhooks()
    {
        $admin = User::factory()->create(['is_admin' => 1]);

        $token = auth('api')->login($admin);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/webhooks');

        $response->assertStatus(200);
    }
};
