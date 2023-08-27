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
        Schema::create('brawler_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brawler_id')->constrained();
            $table->foreignId('map_id')->constrained();
            $table->integer('number_of_picks')->default(0);
            $table->integer('number_of_wins')->default(0);
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
        Schema::dropIfExists('brawler_maps');
    }
};
