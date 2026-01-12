<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\User;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
    'title',
    'event_date',
    'quota',
    'location',
    'category_id',
    'created_by',
];

public function category()
{
    return $this->belongsTo(Category::class);
}

public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}

}
