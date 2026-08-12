<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'category_id',
        'name',
        'price',
        'slug',
        'description',
        'status',
        'image_url',
    ];
}
