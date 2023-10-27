<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained('users')->cascadeOnDelete();
            $table->string('type')->nullable();
            $table->integer('amount')->default(0);
            $table->boolean('is_money')->default(false);
            $table->boolean('is_replenish')->default(false);
            // до транзакции
            $table->integer('money')->default(0);
            $table->integer('bonus')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->string('transaction_id')->nullable();
            $table->text('fields_json')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
