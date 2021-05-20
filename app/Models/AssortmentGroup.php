<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class AssortmentGroup extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'is_main_group', 'main_group', 'code', 'description', 'service_demand', 'refill_cycle_time', 'cycle_time_deviations', 'inventory_cost_factor'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $primaryKey = 'id';

}
