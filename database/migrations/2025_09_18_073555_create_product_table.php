<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // ✅ User is always required
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // ✅ integration_id should be nullable
            $table->foreignId('integration_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            $table->string('external_product_id')->nullable()->index(); // ID from remote platform
            $table->enum('source', ['manual', 'sync'])->default('manual');
            $table->string('name');
            $table->string('sku')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->string('platform')->nullable();
            $table->json('raw_payload')->nullable();

            $table->timestamps();

            // ✅ Unique constraint only if integration_id + external_product_id both are present
            $table->unique(['integration_id', 'external_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
