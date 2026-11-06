<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('individual_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('professional_title')->nullable();
            $table->string('profession')->nullable()->index();
            $table->text('qualification')->nullable();
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->text('bio')->nullable();
            $table->string('website')->nullable();
            $table->string('verification_status', 30)->default('unverified')->index();
            $table->string('publication_status', 30)->default('draft')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('individual_profiles');
    }
};
