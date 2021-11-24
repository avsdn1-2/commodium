<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'email'
    ];

    public static function getAllowedEmails()
    {
        $allowedEmails = [];
        $res = self::all()->toArray();
        foreach ($res as $one)
        {
            $allowedEmails[] = $one['email'];
        }
        return $allowedEmails;
    }
}
