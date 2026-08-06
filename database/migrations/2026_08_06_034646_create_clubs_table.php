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
       Schema::create('clubs', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Tên CLB
        $table->text('description')->nullable(); 
        $table->foreignId('manager_id')->constrained('users')->onDelete('cascade'); // Ai là chủ nhiệm
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
