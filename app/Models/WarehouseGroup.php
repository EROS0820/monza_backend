<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class WarehouseGroup extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'received', 'releases', 'supply', 'description', 'active'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $primaryKey = 'id';

    public function getWarehouseGroupRelation() {
        return $this->hasMany(WarehouseGroupRelation::class, 'warehouse_group_id', 'id');
    }
}
