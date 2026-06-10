<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = config('audit.drivers.database.connection', config('database.default'));
        $table      = config('audit.drivers.database.table', 'audits');

        Schema::connection($connection)->create($table, function (Blueprint $blueprint): void {
            $morphPrefix = config('audit.user.morph_prefix', 'user');

            $blueprint->bigIncrements('id');
            $blueprint->string($morphPrefix . '_type')->nullable();
            $blueprint->unsignedBigInteger($morphPrefix . '_id')->nullable();
            $blueprint->string('event');
            $blueprint->morphs('auditable');
            $blueprint->text('old_values')->nullable();
            $blueprint->text('new_values')->nullable();
            $blueprint->text('url')->nullable();
            $blueprint->ipAddress('ip_address')->nullable();
            $blueprint->string('user_agent', 1023)->nullable();
            $blueprint->string('tags')->nullable();
            $blueprint->timestamps();

            $blueprint->index([$morphPrefix . '_id', $morphPrefix . '_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = config('audit.drivers.database.connection', config('database.default'));
        $table      = config('audit.drivers.database.table', 'audits');

        Schema::connection($connection)->drop($table);
    }
};
