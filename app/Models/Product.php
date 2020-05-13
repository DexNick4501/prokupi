<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'category_id','user_id', 'price', 'description', 'phone_number', 'location',
    ];

}
