<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('fileattachments', function (Blueprint $table) {
    $table->id();
    $table->string('path');
    $table->string('type')->nullable();
    $table->foreignId('meeting_id')->nullable()->constrained('meetings')->onDelete('cascade');
    $table->timestamps();
      });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fileattachments');
    }
};
