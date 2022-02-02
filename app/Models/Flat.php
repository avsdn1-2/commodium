<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flat extends Model
{
    use HasFactory;
    const allowedFlats = ['admin','admin1','1','2','3','4','4а','5','6','7','8','9','10','11','12','12а','14','14а','15','16','17',
        '18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37',
        '38','39','40','41','42','43','44','45','46','47','48','49','50','51','52','53','54','54б'];
    const admin_flats = ['admin','admin1'];

    protected $fillable = [
            'number',
            'square',
            'residents',
            'warmCounter',
            'useLift',
            'privilege',
            'name',
            'first_name',
            'mid_name'
        ];

    public function user()
    {

        return $this->belongsTo(User::class,'number','flat');
    }
    public function pokazs()
    {
        return $this->hasMany(Pokaz::class,'number','flat');
    }
}
