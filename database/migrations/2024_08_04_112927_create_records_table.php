<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->string('identifier');
            $table->json('data')->default('{}');
            $table->foreignId('enhancer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['page_id', 'identifier']);
            $table->index(['page_id', 'identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
