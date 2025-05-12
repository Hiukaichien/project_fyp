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
        Schema::table('kertas_siasatans', function (Blueprint $table) {
            // Add the project_id column after an existing column, e.g., after 'id'
            // If 'id' is the first column, you can place it there.
            // Or choose another logical place.
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kertas_siasatans', function (Blueprint $table) {
            // Ensure the foreign key constraint is dropped before the column
            // The default convention for foreign key name is {table}_{column}_foreign
            $table->dropForeign(['project_id']); // Or $table->dropForeign('kertas_siasatans_project_id_foreign');
            $table->dropColumn('project_id');
        });
    }
};