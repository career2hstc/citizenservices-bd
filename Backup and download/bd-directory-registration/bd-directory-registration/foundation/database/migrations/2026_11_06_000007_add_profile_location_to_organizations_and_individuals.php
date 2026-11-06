<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->foreignId('head_office_location_id')->nullable()->after('public_phone')->constrained('locations')->nullOnDelete();
            $table->text('head_office_address')->nullable()->after('head_office_location_id');
        });

        Schema::table('individual_profiles', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('website')->constrained('locations')->nullOnDelete();
            $table->text('address')->nullable()->after('location_id');
        });
    }

    public function down(): void
    {
        Schema::table('individual_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
            $table->dropColumn('address');
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('head_office_location_id');
            $table->dropColumn('head_office_address');
        });
    }
};
