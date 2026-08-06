<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
      Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->foreignId('club_id')->constrained()->onDelete('cascade'); // Sự kiện của CLB nào
        $table->foreignId('category_id')->nullable()->constrained('event_categories')->onDelete('set null'); // Danh mục sự kiện
        $table->string('name');
        $table->text('description')->nullable();
        $table->string('location')->nullable(); 
        $table->integer('capacity')->default(100); // Sức chứa tối đa (Ràng buộc quan trọng)
        $table->dateTime('start_time')->nullable();
        $table->dateTime('end_time')->nullable();
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Cán bộ duyệt
        $table->timestamps();
        $table->softDeletes();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
