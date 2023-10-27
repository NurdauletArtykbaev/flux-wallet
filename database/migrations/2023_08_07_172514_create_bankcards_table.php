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
        Schema::create('bankcards', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->foreignId('user_id')->index()->constrained('users')->cascadeOnDelete();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->string('bank')->nullable();
            $table->string('country')->nullable();
            $table->string('card_id')->nullable();
            $table->string('recurring_profile_id')->nullable();
            $table->string('card_mask')->nullable();
            $table->string('provider');
            $table->string('card_owner')->nullable();
            $table->boolean('has_3ds')->default(true);
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
        Schema::dropIfExists('bankcards');
    }
};
