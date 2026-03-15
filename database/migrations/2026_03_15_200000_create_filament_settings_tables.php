<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->string('group')->default('general')->index();
            $table->string('key')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->string('value_type')->default('string');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('setting_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('setting_id')->constrained('settings')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->text('value')->nullable();
            $table->unsignedInteger('sort')->default(0);

            // Optional polymorphic scope for future model-specific overrides.
            $table->nullableMorphs('settable');

            $table->timestamps();

            $table->index(['setting_id', 'sort']);
            $table->index(['settable_type', 'settable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_values');
        Schema::dropIfExists('settings');
    }
};
