<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class WarehouseOperation extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'assortment', 'warehouse', 'unit', 'measure_unit', 'contractor', 'date', 'receipt_value', 'issue_amount', 'reception_frequency', 'release_frequency', 'inventory', 'order_quantity',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $primaryKey = 'id';

    public function assortment_item() {
        return $this->hasOne(Assortment::class, 'id', 'assortment');
    }

    public function warehouse_item() {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse');
    }

    public function unit_item() {
        return $this->hasOne(Unit::class, 'id', 'unit');
    }

    public function measure_unit_item() {
        return $this->hasOne(MeasurementUnit::class, 'id', 'measure_unit');
    }

    public function contractor_item() {
        return $this->hasOne(Contractor::class, 'id', 'contractor');
    }
}
