<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon as Carbon;
use Illuminate\Support\Facades\Auth;

class Pokaz extends Model
{
    use HasFactory;
    const START_POKAZ_PERIOD = 24;
    const END_POKAZ_PERIOD = 2;
    const WARM_MULTIPLIER = 1.1;
    const REFRESH_TIME = 1800;

    protected $fillable = [
        'water',
        'warm'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getRepPeriod(){
        function getMonthYear($day,$month,$year){
            if ($day <= Pokaz::END_POKAZ_PERIOD){
                if ($month == 1){
                    $rep_month = 12;
                    $rep_year = $year - 1;
                } else {
                    $rep_month = $month - 1;
                    $rep_year = $year;
                }
            } else {
                $rep_month = $month;
                $rep_year = $year;
            }
            $result['rep_month'] = $rep_month;
            $result['rep_year'] = $rep_year;
            return $result;
        }
        /*
        $dt = '2022-02-03';
        $year = date('Y',strtotime($dt));
        $month = date('n',strtotime($dt));
        $day = date('j',strtotime($dt));
        $date = date('Y-m-d',strtotime($dt));
        */

        $year = (int)date('Y',time());
        $month = (int)date('n',time());
        $month_m = (int)date('m',time());
        $day = (int)date('j',time());

        $res = getMonthYear($day,$month,$year);

        $date = date('Y-m-d',time());

        $date_prev = Carbon::createFromFormat('Y-m-d', $date)->subMonth()->format('Y-m-d');
        $day_prev = date('j',strtotime($date_prev));
        $month_prev = date('n',strtotime($date_prev));
        $year_prev = date('Y',strtotime($date_prev));

        $res_prev = getMonthYear($day_prev,$month_prev,$year_prev);

        $result['day'] = $day;
        $result['rep_month'] = $res['rep_month'];
        $result['rep_month_m'] = $month_m;
        $result['rep_year'] = $res['rep_year'];
        $result['rep_month_prev'] = (int)$res_prev['rep_month'];
        $result['rep_year_prev'] = (int) $res_prev['rep_year'];




        //    'month_prev_m' => $month_prev_m,
       //     'day_prev' => $day_prev
        return $result;
    }

    public static function getRepPeriodAdmin()
    {
        $year = (int)date('Y',time());
        $month = (int)date('n',time());
        $day = (int)date('j',time());

        $date = date('Y-m-d',time());
        $date_prev = Carbon::createFromFormat('Y-m-d', $date)->subMonth()->format('Y-m-d');

        if ($day >= self::START_POKAZ_PERIOD){
            $rep_month = $month;
            $rep_year = $year;
        } else {
            $rep_month = (int)date('n',strtotime($date_prev));
            $rep_year = (int)date('Y',strtotime($date_prev));
        }
        return [
            'rep_month' => $rep_month,
            'rep_year' => $rep_year,
            'rep_month_prev' => self::getPrevMonthYear($rep_year,$rep_month)['month'],
            'rep_year_prev' => self::getPrevMonthYear($rep_year,$rep_month)['year']
        ];
    }

    public static function getPrevMonthYear($year,$month)
    {
        if ($month == 1){
            return [
                'year' => $year - 1,
                'month' => 12
            ];
        } else{
            return [
                'year' => $year,
                'month' => $month - 1
            ];
        }
    }

    public static function formatMonth($month)
    {
        if (in_array($month,[10,11,12])){
            return $month;
        } else {
            return '0' . $month;
        }
    }
    public static function getData($volume,$year,$month)
    {
        if ($volume == 'all'){
            $pokazs = Pokaz::where('year',$year)->where('month',$month)->get();
            $pokazs_prev = Pokaz::where('year',self::getPrevMonthYear($year,$month)['year'])->where('month',self::getPrevMonthYear($year,$month)['month'])->get();
        } else {
            $pokazs = Pokaz::where('year',$year)->where('month',$month)->where('flat',Auth::user()->flat)->get();
            $pokazs_prev = Pokaz::where('year',self::getPrevMonthYear($year,$month)['year'])->where('month',self::getPrevMonthYear($year,$month)['month'])->where('flat',Auth::user()->flat)->get();
        }

        $prev = [];
        foreach ($pokazs_prev as $pokaz)
        {
            $prev[$pokaz->flat] = $pokaz->warm;
        }
        $total = 0;
        foreach ($pokazs as $pokaz)
        {
            $total += $pokaz->warm - $prev[$pokaz->flat];
        }
        return [
            'pokazs' => $pokazs,
            'prev' => $prev,
            'total' => $total,
            'counter' => Counter::where('year',$year)->where('month',$month)->first()->warm,
            'counter_prev' => Counter::where('year',self::getPrevMonthYear($year,$month)['year'])->where('month',self::getPrevMonthYear($year,$month)['month'])->first()->warm,

        ];
    }


    public static function getPayment($pokaz,$pokaz_prev,$tarif,$flat,$periodParams)
    {
        $payment = [
            'day' => $periodParams['day'],
            'month' => $periodParams['rep_month'],
            'month_m' =>  $periodParams['rep_month_m'],
            'flat' => $flat->number,
            'fio' => $flat->name . ' ' . mb_substr($flat->first_name,0,1) . '.' . mb_substr($flat->mid_name,0,1) . '.',
            'month_name' => Pokaz::getMonthName($periodParams['rep_month']),
            'water_tarif' => $tarif->water,
            'service_tarif' => $tarif->service,
            'square' => $flat->square,
            'warmCounter' => $flat->warmCounter,
            'year' => $periodParams['rep_year'],
            'water' => ($pokaz->water - $pokaz_prev->water) * $tarif->water,
            'warm' => $flat->warmCounter == true? ($pokaz->warm !== null? number_format(round(($pokaz->warm - $pokaz_prev->warm) / 1163.06 * $tarif->warm * self::WARM_MULTIPLIER,2),2,'.',' '): 0 ) : 3000,
            'warm_current' => number_format($pokaz->warm,0,'.',' '),
            'warm_previous' => number_format($pokaz_prev->warm,0,'.',' '),
            'service' => round($flat->square * $tarif->service,0),
            'lift' => $tarif->lift,
            'rubbish' => $tarif->rubbish,
            'parkingCleaning' => $tarif->parkingCleaning,
            'parkingLightening' => $tarif->parkingLightening,
            'cons' => $tarif->cons
        ];
        $payment['total'] = $payment['service'] + $payment['lift'] + $payment['rubbish'] + $payment['water'] +
            $payment['parkingCleaning'] + $payment['parkingLightening'];
        return $payment;
    }


    public static function formatInvoice($payment)
    {
        /*
        $html = '<table>';
        $html .= '<tr>
                     <td>' . $array['service'] . '</td>
                     <td>' . $array['lift'] . '</td>
                  </tr>';
        $html .= '<table>';
        */
        /*
        "<html><head><style>body { font-family: DejaVu Sans }</style>".
        "<body>А вот и кириллица</body>".
        "</head></html>";
        */
        $html = '<html><head><style>body {
                font-family: DejaVu Sans;
                table {
                    border-spacing: 0 10px;
                    font-weight: bold;
                    }
                    th {
                    padding: 10px 20px;
                    background: #56433D;
                    color: #F9C941;
                    border-right: 2px solid;
                    font-size: 12px;
                    }
                    th:first-child {
                    text-align: left;
                    }
                    th:last-child {

                    }
                    td {
                    vertical-align: middle;
                    padding: 10px;
                    font-size: 12px;
                    text-align: center;
                    border-top: 2px solid #56433D;
                    border-bottom: 2px solid #56433D;
                    border-right: 2px solid #56433D;
                    }
                    td:first-child {
                    border-left: 2px solid #56433D;
                    }
                    .w_40 {
                    width:40px;
                    }
                    .w_70 {
                     width:50px;
                    }
                    .w_140 {
                     width:140px;
                    }
                    .w_170 {
                     width:130px;
                    }



                }</style><body>';
        $html .= '<div>ОСББ "Коммодіум" 49098 м.Дніпро, пров. Любарського, 4а Р/рах. UA63 305299 00000 26007060006136 (26007060006136) в АТ КБ "Приватбанк",
                       МФО 305299, ЄДРПОУ 35807177</div>';
        $html .= '<div>РАХУНОК № ' . $payment['flat'] . ' / ' . $payment['month_m'] . '</div>';
        $html .= '<div>На сплату комунальних послуг       ' . $payment['fio'] . '      ' . $payment['month_name'] . ' ' . $payment['year'] . 'p.' . '</div>';
        $html .= '<div style="width:500px;margin:0 auto">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">Послуга</th>
                <th scope="col">Сума</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Обслуговування дому (тариф ' . $payment['service_tarif'] .' грн)</td>
                <td>' . $payment['service'] . '</td>
            </tr>
            <tr>
                <td>Обслуговування ліфту</td>
                <td>' . $payment['lift'] . '</td>
            </tr>
            <tr>
                <td>Вивіз сміття</td>
                <td>' . $payment['rubbish'] . '</td>
            </tr>
            <tr>
                <td>Вода (тариф ' . $payment['water_tarif'] .' грн)</td>
                <td>' . $payment['water'] . '</td>
            </tr>
            <tr>
                <td>Прибирання парковочних місць</td>
                <td>' . $payment['parkingCleaning'] . '</td>
            </tr>
            <tr>
                <td>Освітлення парковочних місць</td>
                <td>' . $payment['parkingLightening'] . '</td>
            </tr>
            <tr>
                <td>ВСЬОГО ДО СПЛАТИ</td>
                <td>' . $payment['total'] . '</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center;font-weight:bold">КОНС\'ЄРЖІ</td>
            </tr>
            <tr>
                <td>з/плата</td>
                <td>' . $payment['cons'] . '</td>
            </tr>
            </tbody>
        </table>
        </div>';

        if ($payment['warm'] !== 0) {
            $html .=
               '<div style="width:120px;font-weight:bold;margin:0 auto">ОПАЛЕННЯ</div>
                <div style="width:700px;margin:0 auto">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col" class="w_40" style="text-align:center">Кв-ра</th>
                            <th scope="col" class="w_170">ПІБ</th>
                            <th scope="col" class="w_40">кв. м</th>
                            <th scope="col" class="w_70">Лічильник</th>
                            <th scope="col" class="w_70">Сума</th>
                            <th scope="col" class="w_140" colspan="2" style="text-align:center">Показання лічильника<br>попер./поточні</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td>' . $payment['flat'] . '</td>
                            <td>' . $payment['fio'] . '</td>
                            <td>' . $payment['square'] . '</td>
                            <td>' . ($payment['warmCounter'] == 1 ? 'є' : 'нема') . '</td>
                            <td>' . $payment['warm'] . '</td>
                            <td>' . $payment['warm_previous'] . '</td>
                            <td>' . $payment['warm_current'] . '</td>
                        </tr>
                        </tbody>
                    </table>
                </div>';
            }

        $html .= '</div></body></head></html>';

        return $html;
    }

    public static function getMonthName($month_number)
    {
        switch ($month_number)
        {
            case 1: $month_name = 'січень';
                break;
            case 2: $month_name = 'лютий';
                break;
            case 3: $month_name = 'березень';
                break;
            case 4: $month_name = 'квітень';
                break;
            case 5: $month_name = 'травень';
                break;
            case 6: $month_name = 'червень';
                break;
            case 7: $month_name = 'липень';
                break;
            case 8: $month_name = 'серпень';
                break;
            case 9: $month_name = 'вересень';
                break;
            case 10: $month_name = 'жовтень';
                break;
            case 11: $month_name = 'листопад';
                break;
            case 12: $month_name = 'грудень';
                break;
        }
        return $month_name;
    }

    public static function sendEmail($to,$subject)
    {
        // $subject = "Квитанция";

        $message = "Текст сообщения";
        // название файла
        $filename = "invoice.pdf";

        // папка + название файла
        $filepath = base_path('storage/app/public/' . $filename);

        //письмо с вложением состоит из нескольких частей, которые разделяются разделителем
        // генерируем разделитель
        $boundary = "--".md5(uniqid(time()));

        // разделитель указывается в заголовке в параметре boundary
        $mailheaders = "MIME-Version: 1.0;\r\n";
        $mailheaders .="Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

        // первая часть само сообщение
        $mailheaders .= "From: Commodium\r\n";
        //$mailheaders .= "Reply-To: $user_email\r\n";

        $multipart = "--$boundary\r\n";
        $multipart .= "Content-Type: text/html; charset=windows-1251\r\n";
        $multipart .= "Content-Transfer-Encoding: base64\r\n";
        $multipart .= "\r\n";
        $multipart .= chunk_split(base64_encode(iconv("utf8", "windows-1251", $message)));


        // Закачиваем файл
        $fp = fopen($filepath,"r");
        if (!$fp) {
            print "Не удается открыть файл22";
            exit();
        }
        // чтение файла
        $file = fread($fp, filesize($filepath));
        fclose($fp);

        // второй частью прикрепляем файл, можно прикрепить два и более файла
        $message_part = "\r\n--$boundary\r\n";
        $message_part .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n";
        $message_part .= "Content-Transfer-Encoding: base64\r\n";
        $message_part .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";
        $message_part .= "\r\n";
        $message_part .= chunk_split(base64_encode($file));
        $message_part .= "\r\n--$boundary--\r\n";

        $multipart .= $message_part;
        // отправляем письмо
        mail($to,$subject,$multipart,$mailheaders);

        // удаление файла
        //unlink($filepath);

    }
}
