<?php

namespace App\Services;

use App\Mail\ErrorMessage;
use App\Mail\WarmMessage;
use App\Models\Counter;
use App\Models\Flat;
use App\Models\Pokaz;
use App\Models\Pull;
use App\Models\Tarif;
use App\Models\Tarifw;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CalcService
{
   //определение все ли квартиры пула предоставили показания по теплу; если все, то рассчет дополнительного тарифа по теплу
   public function pull():array
   {
       $flatsOfPull = [];
       foreach (Pull::all() as $info){
           $flatsOfPull[] = $info->flat;
       }
       $result = Pokaz::getRepPeriodAdmin();

       $pokazs = Pokaz::with('getFlat')->where('year',$result['rep_year'])->where('month',$result['rep_month'])->get();
       $flatsWithPokaz = [];
       foreach ($pokazs as $pokaz){
           $flatsWithPokaz[] = $pokaz->flat;
       }
       $flatsWithoutPokaz = array_diff($flatsOfPull,$flatsWithPokaz);


       $counter = Counter::where('year',$result['rep_year'])->where('month',$result['rep_month'])->get()->first();
       $tariff_additional = null;

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
           $counterType = [];
           foreach ($pokazs as $pokaz){
               $current[$pokaz->flat] = $pokaz->warm;
               $counterType[$pokaz->flat] = $pokaz->getFlat->counterType;
           }

           $square = [];
           $fio = [];
           $email = [];
           $total_square = 0; //общая прощадь всех картир
           $total_pokaz = 0;  //суммарные показания по всем квартирам

           foreach ($current as $number => $value){
               if (in_array($number,$flatsOfPull)){


                   $flat = Flat::with('user')->where('number', $number)->get()->first();

                   if ($flat == null) {
                       echo "Ошибка! Не существует данных по квартире № $number";
                       exit();
                   }
                   //оперируем с отопительной площадью $flat->square_warm
                   $square[$number] = $flat->square_warm;
                   $total_square += $flat->square_warm;
                   $total_pokaz += round(Pokaz::toGcal($value - $previous[$number],$counterType[$number]),3);
                   $fio[$number] = $flat->name . ' ' . Str::substr($flat->first_name, 0, 1) . '.' . Str::substr($flat->mid_name, 0, 1) . '.';
                   $email[$number] = $flat->user->email;
               }

           }

           $non_balance = $counter->warm - $counter_prev->warm - $total_pokaz;

           $tariff_additional = round($non_balance * $tariff->warm / $total_square,4);

           /*
           $month_name = Pokaz::getMonthName($result['rep_month']);
           $data = [];
           foreach ($current as $number => $value){
               if (in_array($number,$flatsOfPull)) {
                   $data[$number] = [
                       'flat' => $number,
                       'fio' => $fio[$number],
                       'email' => $email[$number],
                       'square' => $square[$number],
                       'payment_main' => round(round(Pokaz::toGcal($value - $previous[$number],$counterType[$number]),3 ) * $tariff->warm, 2),
                       'payment_additional' => round($square[$number] * $tariff_additional, 2),
                   ];
               }
           }

           foreach ($data as $number => $value){
                    Mail::to($value['email'])->send(new WarmMessage($result['rep_month'],$month_name,$result['rep_year'],$tariff_additional,$value));
           }
            */

       }
       return [
           'flatsWithoutPokaz' => $flatsWithoutPokaz,
           'counter' => $counter,
           'rep_month' => $result['rep_month'],
           'rep_year' => $result['rep_year'],
           'tarifAdditional' => $tariff_additional,
       ];
   }

   //рассчет данных для отображения в квитанции за тепло
   public function getWarmData(string $flat):array
   {
       $periodParams = Pokaz::getRepPeriodAdmin();

       $pokaz = Pokaz::with('getFlat')->where('year',$periodParams['rep_year'])->where('month',$periodParams['rep_month'])->where('flat',$flat)->get()->first();
       if ($pokaz == null){
           echo "Ошибка! Не найдены текущие показания по квартире № $flat";
           exit();
       }
       $pokaz_prev = Pokaz::with('getFlat')->where('year',$periodParams['rep_year_prev'])->where('month',$periodParams['rep_month_prev'])->where('flat',$flat)->get()->first();
       if ($pokaz_prev == null){
           echo "Ошибка! Не найдены предыдущие показания по квартире № $flat";
           exit();
       }
       $tariff = Tarif::find(1);
       if ($tariff == null){
           echo "Ошибка! Не найдены тарифы!";
           exit();
       }
       $tarifw = Tarifw::where('year',$periodParams['rep_year'])->where('month',$periodParams['rep_month'])->get()->first();
       if ($tarifw == null){
           echo 'Не рассчитан дополнительный тариф по отоплению за отчетный период!';
           exit();
       }
       $user = User::where('flat',$flat)->get()->first();
       if ($user == null){
           echo "Ошибка! Не найден пользователь квартиры № $flat";
           exit();
       }

       return [
           'flat' => $flat,
           'fio' => $pokaz->getFlat->name . ' ' . Str::substr($pokaz->getFlat->first_name, 0, 1) . '.' . Str::substr($pokaz->getFlat->mid_name, 0, 1) . '.',
           'email' => $user->email,
           'square' => $pokaz->getFlat->square_warm,
           'payment_main' => round(round(Pokaz::toGcal($pokaz->warm - $pokaz_prev->warm,$pokaz->getFlat->counterType),3 ) * $tariff->warm, 2),
           'payment_additional' => round($pokaz->getFlat->square_warm * $tarifw->tarifAdditional, 2),
           'year' => $periodParams['rep_year'],
           'month' => $periodParams['rep_month'],
           'month_name' => Pokaz::getMonthName($periodParams['rep_month']),
           'tarifAdditional' => $tarifw->tarifAdditional,
       ];
   }

   //рассчет данных для отображения в квитанции за обслуживание
   public function getServiceData(string $flat_number):array
   {
       $periodParams = Pokaz::getRepPeriodAdmin();

       $flat = Flat::where('number',$flat_number)->first();
       if ($flat == null){
           echo "Ошибка! Не найдена квартира пользователя!";
           exit();
       }

       $pokaz = Pokaz::where('flat',$flat->number)->where('year',$periodParams['rep_year'])->where('month',$periodParams['rep_month'])->first();
       if ($pokaz == null){
           return [];
           //echo "Ошибка! Не занесены показания за текущий период!";
           //exit();
       }
       $pokaz_prev = Pokaz::where('flat',$flat->number)->where('year',$periodParams['rep_year_prev'])->where('month',$periodParams['rep_month_prev'])->first();
       if ($pokaz_prev == null){
           echo "Ошибка! Не занесены показания за предыдущий период!";
           exit();
       }
       $tarif = Tarif::find(1);
       if ($tarif == null){
           echo "Ошибка! Не найдены тарифы!";
           exit();
       }

       return Pokaz::getPayment($pokaz,$pokaz_prev,$tarif,$flat,$periodParams);
   }
}
