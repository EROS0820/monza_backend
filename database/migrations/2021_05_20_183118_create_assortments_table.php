<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssortmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assortments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('index');
            $table->string('gtin');
            $table->integer('unit');
            $table->integer('measure_unit');
            $table->boolean('active');
            $table->boolean('to_order');
            $table->string('purchase_price');
            $table->string('sale_price');
            $table->integer('assortment_group');
            $table->string('assortment_type');
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
        Schema::dropIfExists('assortments');
    }
}
