<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'photo_url',
    ];

    protected $casts = [
        'photo_url' => 'array',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    public function subcategories()
    {
        return $this->belongsToMany(Subcategory::class);
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }
}
