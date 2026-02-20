<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table name, owner column name and owner column type come from config. Set
     * saas.table, saas.owner_key and saas.owner_key_type before running this
     * migration; do not change them after, or the model will not match the schema.
     *
     * owner_key_type: unsignedBigInteger (default), uuid, or string. Use uuid or
     * string when your owner model uses a UUID or string primary key.
     */
    public function up(): void
    {
        $tableName = config('hostpinnacle.saas.table', 'hostpinnacle_accounts');
        $ownerKey = config('hostpinnacle.saas.owner_key', 'user_id');
        $ownerKeyType = config('hostpinnacle.saas.owner_key_type', 'unsignedBigInteger');

        Schema::create($tableName, function (Blueprint $table) use ($ownerKey, $ownerKeyType) {
            $table->id();
            match ($ownerKeyType) {
                'uuid' => $table->uuid($ownerKey)->nullable()->index(),
                'string' => $table->string($ownerKey)->nullable()->index(),
                default => $table->unsignedBigInteger($ownerKey)->nullable()->index(),
            };
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
