<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $table = 'fees';

    protected $fillable = [
        'product_id',
        'start_day',
        'end_day',
        'price',
    ];
}
