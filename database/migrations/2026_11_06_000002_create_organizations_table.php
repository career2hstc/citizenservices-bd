<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('organization_type', ['private', 'government', 'multinational', 'ngo', 'other'])->default('private')->index();
            $table->string('registration_number')->nullable()->index();
            $table->unsignedSmallInteger('established_year')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('website')->nullable();
            $table->string('public_email')->nullable();
            $table->string('public_phone', 30)->nullable();
            $table->string('verification_status', 30)->default('unverified')->index();
            $table->string('publication_status', 30)->default('draft')->index();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
