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
        Schema::create('brawlers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('win_rate', 4, 2)->default(0);
            $table->decimal('pick_rate', 4, 2)->default(0);
            $table->integer('win_rate_rank')->default(0);
            $table->integer('pick_rate_rank')->default(0);
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
        Schema::dropIfExists('brawlers');
    }
};
