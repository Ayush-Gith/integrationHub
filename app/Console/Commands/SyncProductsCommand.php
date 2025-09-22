<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class SyncProductsCommand extends Command
{
    protected $signature = 'products:sync';
    protected $description = 'Sync products from external platform';

    public function handle()
    {
        $this->info('Starting product sync...');

        Product::create([
            'name' => 'Sample Product',
            'sku' => 'SKU-123',
            'price' => 99.99,
            'stock' => 10,
            'platform' => 'shopify'
        ]);

        $this->info('Product sync completed successfully.');
    }
}
