<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_fields', function (Blueprint $table) {
            $table->boolean('is_identifier')->default(false)->after('visible');
            $table->unsignedTinyInteger('identifier_order')->nullable()->after('is_identifier');
        });

        $identifiers = DB::table('service_identifiers')->get();

        foreach ($identifiers as $identifier) {
            DB::table('service_fields')
                ->where('id', $identifier->field_id)
                ->update([
                    'is_identifier' => true,
                    'identifier_order' => $identifier->identifier_order,
                ]);
        }

        Schema::dropIfExists('service_identifiers');
    }

    public function down(): void
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

        $fields = DB::table('service_fields')->where('is_identifier', true)->get();

        foreach ($fields as $field) {
            DB::table('service_identifiers')->insert([
                'service_id' => $field->service_id,
                'field_id' => $field->id,
                'identifier_order' => $field->identifier_order ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('service_fields', function (Blueprint $table) {
            $table->dropColumn(['is_identifier', 'identifier_order']);
        });
    }
};
