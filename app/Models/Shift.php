<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $guarded = []; 

    // Đã thêm rõ 'shift_id' và 'user_id' để chống lỗi tự đoán tên cột của Laravel
    public function users() 
    {
        return $this->belongsToMany(User::class, 'shift_user', 'shift_id', 'user_id');
    }
}