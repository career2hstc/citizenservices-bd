<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('locations')->restrictOnDelete();
            $table->string('level', 30)->index(); // division, district, upazila, union, city_corporation, municipality, ward, area, village
            $table->string('name_en');
            $table->string('name_bn')->nullable();
            $table->string('code', 40)->nullable()->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['parent_id', 'level']);
            $table->index(['name_en', 'name_bn']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
