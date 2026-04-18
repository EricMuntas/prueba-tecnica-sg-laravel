<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table = 'subcategories';

    protected $fillable = [
        'category_id',
        'name',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class)->select(['id', 'name']);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
