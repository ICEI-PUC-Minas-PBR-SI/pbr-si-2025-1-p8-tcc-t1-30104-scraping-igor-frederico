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
        Schema::create('inventors', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('city')->nullable(); 
            $table->string('state')->nullable(); 
            $table->unsignedBigInteger('patent_id');
            $table->foreign('patent_id')->references('id')->on('patents');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invertors');
    }
};
