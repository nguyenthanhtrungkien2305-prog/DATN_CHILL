<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $primaryKey = 'banner_id';

    protected $fillable = [
        'title',
        'badge',
        'description',
        'button_text',
        'button_link',
        'button_secondary_text',
        'button_secondary_link',
        'image_url',
        'bg_gradient',
        'position',
        'product_id',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
