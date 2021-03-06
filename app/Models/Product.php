<?php

namespace App\Models;

use App\Commentary;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

    use SoftDeletes;

    protected $table    = 'products';

    protected $with = [
    ];

    protected $withCount = [
        'commentaries',
    ];

    protected $fillable = [
        'name', 'category_id','user_id', 'price', 'description', 'phone_number',  'location', 'img',
    ];

    protected $appends = [
        'img_url',
        'img_path',
        'index'
    ];

    function getIndexAttribute(){
        return $this->id;
    }
    function getImgUrlAttribute(){
        return 'storage/!/thumbs/products/' . $this->id . '.jpg';
    }
    function getImgPathAttribute(){
        return public_path($this->getImgUrlAttribute());
    }

    function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }
    function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    function commentaries() {
        return $this->hasMany(Commentary::class, 'product_id');
    }

}
