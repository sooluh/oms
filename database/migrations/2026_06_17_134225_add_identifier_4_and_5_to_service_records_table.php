<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_records', function (Blueprint $table) {
            $table->string('identifier_4')->nullable()->index()->after('identifier_3');
            $table->string('identifier_5')->nullable()->index()->after('identifier_4');

            $table->index(['service_id', 'identifier_4']);
            $table->index(['service_id', 'identifier_5']);
        });
    }

    public function down(): void
    {
        Schema::table('service_records', function (Blueprint $table) {
            $table->dropIndex(['service_id', 'identifier_5']);
            $table->dropIndex(['service_id', 'identifier_4']);
            $table->dropColumn(['identifier_5', 'identifier_4']);
        });
    }
};
