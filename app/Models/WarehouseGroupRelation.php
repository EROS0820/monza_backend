<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class WarehouseGroupRelation extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'warehouse_group_id', 'warehouse_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $primaryKey = 'id';

    public function getWarehouse() {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }
}
