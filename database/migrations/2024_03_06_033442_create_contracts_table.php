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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('manufacturer_id');
            $table->unsignedBigInteger('distributor_id')->nullable();
            $table->unsignedBigInteger('term_id');
            $table->string('start_date');
            $table->string('end_date');
            $table->string('renewal_date')->nullable();
            $table->string('location');
            $table->enum('status',['open','close_won','close_lost'])->nullable();
            $table->decimal('contract_price', 20, 2)->default('0');
            $table->decimal('contract_cost', 20, 2)->default('0');
            $table->decimal('contract_revenue', 20, 2)->default('0');
            $table->integer('contract_progress')->default('0');
            $table->timestamps();
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('cascade');
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('set null');
            $table->foreign('term_id')->references('id')->on('terms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
