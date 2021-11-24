<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flat extends Model
{
    use HasFactory;

    protected $fillable = [
            'number',
            'square',
            'warmCounter',
            'privilege'
        ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}