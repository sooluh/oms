<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('identifier_1')->nullable()->index();
            $table->string('identifier_2')->nullable()->index();
            $table->string('identifier_3')->nullable()->index();
            $table->json('data');
            $table->timestamps();

            $table->index(['service_id', 'identifier_1']);
            $table->index(['service_id', 'identifier_2']);
            $table->index(['service_id', 'identifier_3']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};
