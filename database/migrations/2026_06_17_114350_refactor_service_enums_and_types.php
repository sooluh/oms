<?php

use App\Enums\FieldType;
use App\Enums\SearchMode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE services SET search_mode = CASE search_mode WHEN 'OR' THEN ? WHEN 'AND' THEN ? ELSE ? END", [
            SearchMode::Or->value,
            SearchMode::And->value,
            SearchMode::Or->value,
        ]);

        DB::statement("UPDATE service_fields SET type = CASE type
            WHEN 'text' THEN ?
            WHEN 'date' THEN ?
            WHEN 'link' THEN ?
            WHEN 'attachment' THEN ?
            WHEN 'boolean' THEN ?
            ELSE ? END", [
            FieldType::Text->value,
            FieldType::Date->value,
            FieldType::Link->value,
            FieldType::Attachment->value,
            FieldType::Boolean->value,
            FieldType::Text->value,
        ]);

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE services ALTER COLUMN search_mode TYPE smallint USING (search_mode::smallint)');
            DB::statement('ALTER TABLE service_fields ALTER COLUMN type TYPE smallint USING (type::smallint)');
        }

        Schema::table('services', function (Blueprint $table) {
            $table->tinyInteger('search_mode')->default(SearchMode::Or->value)->change();
        });

        Schema::table('service_fields', function (Blueprint $table) {
            $table->tinyInteger('type')->default(FieldType::Text->value)->change();
            $table->string('true_label', 50)->nullable()->after('type');
            $table->string('false_label', 50)->nullable()->after('true_label');
        });
    }

    public function down(): void
    {
        Schema::table('service_fields', function (Blueprint $table) {
            $table->dropColumn(['true_label', 'false_label']);
        });
    }
};
