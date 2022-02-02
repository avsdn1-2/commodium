<?php

namespace App\Services;

use app\Models\Pokaz;
use Illuminate\Database\Eloquent\Model;

class PokazService
{
    public function getPokaz($user,$period):array
    {
        $pokaz = Pokaz::where('flat',$user->flat)->where('year',$period['rep_year'])->where('month',$period['rep_month'])->get()->first();
        if ($pokaz !== null){
            $warm = $pokaz->warm;
            $water = $pokaz->water;
        } else {
            $warm = null;
            $water = null;
        }
        $pokaz_prev = Pokaz::where('flat',$user->flat)->where('year',$period['rep_year_prev'])->where('month',$period['rep_month_prev'])->get()->first();
        if ($pokaz_prev !== null){
            $warm_prev = $pokaz_prev->warm;
            $water_prev = $pokaz_prev->water;
        } else {
            $warm_prev = 0;
            $water_prev = 0;
        }
        return [
            'warm' => $warm,
            'water' => $water,
            'warm_prev' => $warm_prev,
            'water_prev' => $water_prev,
        ];
    }

    public function savePokaz($user,$period,$water_prev,$warm_prev,$water,$warm):string
    {
        $error_message = "";
        //если введены предыдущие показания, то сохраняем их
        if ($water_prev > 0 && $warm_prev > 0){
            $pokaz_prev = new Pokaz();
            $pokaz_prev->flat = $user->flat;
            $pokaz_prev->year = $period['rep_year_prev'];
            $pokaz_prev->month = $period['rep_month_prev'];
            $pokaz_prev->water = $water_prev;
            $pokaz_prev->warm = $warm_prev;
            $pokaz_prev->save();
            //$water_prev = $request->get('water_prev');
           // $warm_prev = $request->get('warm_prev');
        }

        //проверяем, есть ли введенные показания за отчетный период
        $pokaz = Pokaz::where('flat',$user->flat)->where('year',$period['rep_year'])->where('month',$period['rep_month'])->get()->first();
        //если нет введенных показаний за отчетный период, то сохраняем их
        if ($pokaz == null){
            //проверяем, есть ли показания за предыдущий период
            $pokaz_prev = Pokaz::where('flat',$user->flat)->where('year',$period['rep_year_prev'])->where('month',$period['rep_month_prev'])->get()->first();
            if ($pokaz_prev->water <= $water && $pokaz_prev->warm <= $warm){
                $pokaz = new Pokaz();
                $pokaz->flat = $user->flat;
                $pokaz->year = $period['rep_year'];
                $pokaz->month = $period['rep_month'];
                $pokaz->water = $water;
                $pokaz->warm = $warm;
                $pokaz->save();
            } else {
                $error_message = "Вы вводите меньшие показания чем за предыдущий период!";
            }
        } else { //если есть введенные показания за отчетный период, то сохраняем, если введены большие показания; если введены меньшие, то показываем ошибку
            if ($water >= $pokaz->water && $warm >= $pokaz->warm){
                $pokaz->water = $water;
                $pokaz->warm = $warm;
                $pokaz->save();
            } else {
                $error_message = "Вы вводите меньшие показания по сравнению с введенными ранее!";
            }
        }
        return $error_message;
    }
}
