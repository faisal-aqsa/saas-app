<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Polymorphic: can be logged against a Deal, Contact, Account, or Project
            $table->uuidMorphs('subject');

            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['call', 'email', 'note', 'meeting', 'task']);
            $table->string('subject_line')->nullable();  // e.g. email subject or call title
            $table->longText('body')->nullable();
            $table->timestamp('occurred_at')->nullable(); // when the activity happened
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
