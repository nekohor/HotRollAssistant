<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRuleSteelGradeCategosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rule_steel_grade_categos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('steel_grade')->unique();
            $table->string('catego1');
            $table->string('catego2');
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
        Schema::dropIfExists('rule_steel_grade_categos');
    }
}
