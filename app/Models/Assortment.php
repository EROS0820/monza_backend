<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Assortment extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'index', 'gtin', 'unit', 'measure_unit', 'active', 'to_order', 'purchase_price', 'sale_price', 'assortment_group', 'assortment_type', 'service_demand', 'refill_cycle_time', 'cycle_time_deviations', 'inventory_cost_factor',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $primaryKey = 'id';

    public function assortment_group_item() {
        return $this->hasOne(AssortmentGroup::class, 'id', 'assortment_group');
    }

}
