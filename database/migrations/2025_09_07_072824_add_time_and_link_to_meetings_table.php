<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            // make time nullable to avoid breaking existing rows; you can enforce later
            $table->time('time')->nullable()->after('date');
            $table->string('link')->nullable()->after('time');
        });
    }

    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['time', 'link']);
        });
    }
};
