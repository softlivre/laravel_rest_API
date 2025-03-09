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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['Open', 'In Progress', 'Completed', 'Rejected'])->default('Open');
            $table->unsignedBigInteger('building_id');
            $table->unsignedBigInteger('assigned_to');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Define foreign keys/ constraints
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
