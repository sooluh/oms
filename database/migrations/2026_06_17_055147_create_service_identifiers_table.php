<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_identifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('service_fields')->cascadeOnDelete();
            $table->unsignedTinyInteger('identifier_order')->default(1);
            $table->timestamps();

            $table->unique(['service_id', 'field_id']);
            $table->unique(['service_id', 'identifier_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_identifiers');
    }
};
