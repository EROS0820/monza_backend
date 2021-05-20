<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'GLN', 'address', 'postal_code', 'city', 'order_fulfillment_time', 'delivery_time_deviation', 'minimum_order_quantity', 'minimum_order_value', 'description', 'active', 'supplier', 'recipient', 'supplier_transport',
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
