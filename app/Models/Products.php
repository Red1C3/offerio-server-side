<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'category_id',
        'contact',
        'amount',
        'imgName',
        'timestamp-1',
        'timestamp-2',
        'timestamp-3',
        'timestamp-4',
        'price-1',
        'price-2',
        'price-3',
        'price-4',
        'views'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public function likes()
    {
        return $this->hasMany(Likes::class);
    }
}
