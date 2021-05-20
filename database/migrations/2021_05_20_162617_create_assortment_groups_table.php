<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssortmentGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assortment_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_main_group');
            $table->integer('main_group');
            $table->string('code');
            $table->string('description');
            $table->string('service_demand');
            $table->string('refill_cycle_time');
            $table->string('cycle_time_deviations');
            $table->string('inventory_cost_factor');
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
        Schema::dropIfExists('assortment_groups');
    }
}
