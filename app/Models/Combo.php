<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
    protected $primaryKey = 'combo_id';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'original_price',
        'price',
        'image_url',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(ComboItem::class, 'combo_id', 'combo_id');
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'combo_items',
            'combo_id',
            'product_id',
            'combo_id',
            'product_id'
        )->withPivot('quantity', 'id');
    }
}
