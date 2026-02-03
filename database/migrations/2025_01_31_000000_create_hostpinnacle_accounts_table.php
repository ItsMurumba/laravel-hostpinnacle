<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = config('hostpinnacle.saas.table', 'hostpinnacle_accounts');
        $ownerKey = config('hostpinnacle.saas.owner_key', 'user_id');

        Schema::create($tableName, function (Blueprint $table) use ($ownerKey) {
            $table->id();
            $table->unsignedBigInteger($ownerKey)->nullable()->index();
            $table->string('api_key');
            $table->string('sender_id');
            $table->string('username');
            $table->text('password');
            $table->string('base_url')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = config('hostpinnacle.saas.table', 'hostpinnacle_accounts');
        Schema::dropIfExists($tableName);
    }
};
