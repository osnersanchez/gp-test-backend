<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Categories;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;

class Product extends Model
{
    protected $fillable = [
        'name', 'price', 'quantity', 'description'
    ];

    public function categories()
    {
        return $this->belongsTo(Categories::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'shopping_cars', 'idUser', 'idProduct');
    }
}
