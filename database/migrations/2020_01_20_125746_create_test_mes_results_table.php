<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestMesResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_mes_results', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string("ACTCOILID")->unique();
            $table->string("PRODSTART");
            $table->string("PRODEND");
            $table->string("ACTSLABID");
            $table->string("GRADENAME");
            $table->string("HEXIT");
    
            $table->string("SLABLENGTH")->nullable();
            $table->string("SLABWIDTH")->nullable();
            $table->string("SLABTHICKNESS")->nullable();
            
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
        Schema::dropIfExists('test_mes_results');
    }
}
