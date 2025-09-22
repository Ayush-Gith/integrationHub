<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkToUserIntegrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_integrations', function (Blueprint $table) {
            // only add FK if not exists - some DB engines will throw otherwise
            $table->foreign('integration_id')
                ->references('id')
                ->on('integrations')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_integrations', function (Blueprint $table) {
            $table->dropForeign(['integration_id']);
        });
    }
}
