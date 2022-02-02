<?php

namespace App\Services;

use App\Mail\ErrorMessage;
use App\Mail\WarmMessage;
use App\Models\Counter;
use App\Models\Flat;
use App\Models\Pokaz;
use App\Models\Pull;
use App\Models\Tarif;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CalcService
{
   public function pull():array
   {
       $flatsOfPull = [];
       foreach (Pull::all() as $info){
           $flatsOfPull[] = $info->flat;
       }
       $result = Pokaz::getRepPeriodAdmin();

       $pokazs = Pokaz::where('year',$result['rep_year'])->where('month',$result['rep_month'])->get();
       $flatsWithPokaz = [];
       foreach ($pokazs as $pokaz){
           $flatsWithPokaz[] = $pokaz->flat;
       }
       $flatsWithoutPokaz = array_diff($flatsOfPull,$flatsWithPokaz);

       $counter = Counter::where('year',$result['rep_year'])->where('month',$result['rep_month'])->get()->first();

       if ($counter !== null && count($flatsWithoutPokaz) == 0){
           //есть все данные для рассчета квитанций за тепло по пулу квартир
           $counter_prev = Counter::where('year',$result['rep_year_prev'])->where('month',$result['rep_month_prev'])->get()->first();
           if ($counter_prev == null){
               echo 'Ошибка! Отсутствуют показания общедомового счетчика тепла за предыдущий отчетный период!';
               exit();
           }

           $pokazs_prev = Pokaz::where('year',$result['rep_year_prev'])->where('month',$result['rep_month_prev'])->get();
           if ($pokazs_prev == null){
               echo 'Ошибка! Отсутствуют показания квартир за предыдущий отчетный период!';
               exit();
           }

           $tariff = Tarif::find(1);
           if ($tariff == null){
               echo "Ошибка! Не найдены тарифы!";
               exit();
           }

           $previous = [];
           foreach ($pokazs_prev as $pokaz){
               $previous[$pokaz->flat] = $pokaz->warm;
           }
           $current = [];
           foreach ($pokazs as $pokaz){
               $current[$pokaz->flat] = $pokaz->warm;
           }
           $square = [];
           $fio = [];
           $email = [];
           $total_square = 0; //общая прощадь всех картир
           $total_pokaz = 0;  //суммарные показания по всем квартирам
           foreach ($current as $number => $value){
               $flat = Flat::with('user')->where('number',$number)->get()->first(); //:with('user')->
               if( $flat == null){
                   echo "Ошибка! Не существует данных по квартире № $number";
                   exit();
               }
               $square[$number] = $flat->square;
               $total_square += $flat->square;
               $total_pokaz += ($value - $previous[$number]);
               $fio[$number] = $flat->name . ' ' . Str::substr($flat->first_name,0,1) . '.' . Str::substr($flat->mid_name,0,1) . '.';
               $email[$number] = $flat->user->email;

           }
           $non_balance = $counter->warm - $counter_prev->warm - $total_pokaz;
           $tariff_additional = round($non_balance / Pokaz::kVtToGkal * $tariff->warm / $total_square,4);
           $month_name = Pokaz::getMonthName($result['rep_month']);
           $data = [];
           foreach ($current as $number => $value){
               $data[$number] = [
                   'flat' => $number,
                   'fio' => $fio[$number],
                   'email' => $email[$number],
                   'square' => $square[$number],
                   'payment_main' => round(($value - $previous[$number]) / Pokaz::kVtToGkal * $tariff->warm,2),
                   'payment_additional' => round($square[$number] * $tariff_additional,2),
               ];
           }

           foreach ($data as $number => $value){
             //  if ($number == 10){ //временно
                    Mail::to($value['email'])->send(new WarmMessage($result['rep_month'],$month_name,$result['rep_year'],$tariff_additional,$value));
             //  }
           }


       }
       return [
           'flatsWithoutPokaz' => $flatsWithoutPokaz,
           'counter' => $counter,
           'rep_month' => $result['rep_month'],
           'rep_year' => $result['rep_year'],
       ];
   }
}
