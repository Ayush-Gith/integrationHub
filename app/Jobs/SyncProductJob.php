<?php

namespace App\Jobs;

use App\Models\Integration;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Integration $integration;
    protected array $products;
    protected int $userId;

    public function __construct(Integration $integration, array $products, int $userId)
    {
        $this->integration = $integration;
        $this->products = $products;
        $this->userId = $userId;
    }

    public function handle()
    {
        try {
            if ($this->integration->user_id !== $this->userId) {
                Log::warning('SyncProductJob aborted: user mismatch', [
                    'expected_user' => $this->integration->user_id,
                    'given_user' => $this->userId,
                ]);
                return;
            }

            if (empty($this->products)) {
                Log::info('SyncProductJob executed: no products to sync yet.', [
                    'integration_id' => $this->integration->id
                ]);
                return;
            }

            $count = 0;
            foreach ($this->products as $productData) {
                Product::updateOrCreate(
                    [
                        'integration_id' => $this->integration->id,
                        'external_product_id' => $productData['external_product_id'] ?? null,
                    ],
                    [
                        'user_id' => $this->integration->user_id,
                        'name' => $productData['name'] ?? 'Unnamed Product',
                        'sku' => $productData['sku'] ?? null,
                        'price' => is_numeric($productData['price'] ?? null) ? $productData['price'] : 0,
                        'stock' => isset($productData['stock']) ? (int) $productData['stock'] : 0,
                        'status' => $productData['status'] ?? 'active',
                        'platform' => $this->integration->platform,
                        'source' => 'sync', // ✅ Mark as synced
                        'raw_payload' => $productData,
                    ]
                );
                $count++;
            }

            Log::info('Products synced successfully', [
                'integration_id' => $this->integration->id,
                'count' => $count,
            ]);

        } catch (\Throwable $e) {
            Log::error('SyncProductJob failed', [
                'error' => $e->getMessage(),
                'integration_id' => $this->integration->id ?? null
            ]);
            throw $e;
        }
    }
}