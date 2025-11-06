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
        Schema::table('users', function (Blueprint $table) {
            // Drop the default "name" column
            $table->dropColumn('name');

            // Adding the required fields
            $table->string('names', 150)->after('id');
            $table->string('document', 50)->unique()->after('names');
            $table->string('phone', 30)->unique()->after('email');

            // Indexes for fast lookup
            $table->index('document', 'IDX_users_document');
            $table->index('phone', 'IDX_users_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove added fields
            $table->dropIndex('IDX_users_document');
            $table->dropIndex('IDX_users_phone');

            $table->dropColumn(['document', 'names', 'phone']);

            // Restoring the original "name" column
            $table->string('name')->after('id');
        });
    }
};
