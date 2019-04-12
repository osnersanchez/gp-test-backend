<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;

class shoppingCar extends Model
{
    protected $fillable = [
        'quantity'
    ];
}
