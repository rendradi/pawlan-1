<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'author',
        'publisher',
        'year',
    ];
}
