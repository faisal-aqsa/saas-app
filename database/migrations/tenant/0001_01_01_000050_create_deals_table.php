<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('stage_id')->constrained('pipeline_stages')->cascadeOnDelete();
            $table->foreignUuid('account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->decimal('value', 15, 2)->nullable();
            $table->date('expected_close_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
