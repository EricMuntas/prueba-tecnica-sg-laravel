<?php

namespace App\Models;

use Carbon\Carbon;
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
        return $this->hasMany(Fee::class)->orderBy('id', 'DESC');
    }

    public function currentFee()
    {
        $currentDate = Carbon::now()->toDateString();

        return $this->hasOne(Fee::class)
            ->where('start_day', '<=', $currentDate)
            ->where('end_day', '>=', $currentDate);
    }
}
