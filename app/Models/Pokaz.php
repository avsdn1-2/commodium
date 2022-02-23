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
    const REFRESH_TIME = 1800;
    const admin_email = 'avsdn1@gmail.com';
    const manager_email = 'kommodium@gmail.com';



    protected $fillable = [
        'water',
        'warm',
        'savedBy'
    ];
    public function getFlat()
    {
        return $this->belongsTo(Flat::class,'flat','number');
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
        function getMonth_m($month)
        {
            switch ($month){
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                case 6:
                case 7:
                case 8:
                case 9:
                    return '0' . $month;
                    break;
                default:
                    return $month;
            }
        }

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

        $date_next = Carbon::createFromFormat('Y-m-d', $res['rep_year'] . '-' . getMonth_m($res['rep_month']) . '-01')->addMonth()->format('Y-m-d');

        $result['day'] = $day;
        $result['rep_month'] = $res['rep_month'];
        $result['rep_month_m'] = getMonth_m($res['rep_month']);
        $result['rep_year'] = $res['rep_year'];
        $result['rep_month_prev'] = (int)$res_prev['rep_month'];
        $result['rep_year_prev'] = (int) $res_prev['rep_year'];
        $result['rep_month_next'] = date('n',strtotime($date_next)); //для отображения в квитанци (следующий месяц за отчетным)
        $result['rep_year_next'] = date('Y',strtotime($date_next));  //для отображения в квитанци (следующий месяц за отчетным)

        return $result;
    }

    public static function getRepPeriodAdmin()
    {
        $year = (int)date('Y',time());
        $month = (int)date('n',time());
        $month_m = (int)date('m',time());
        $day = (int)date('j',time());

        $date = date('Y-m-d',time());
        $date_prev = Carbon::createFromFormat('Y-m-d', $date)->subMonth()->format('Y-m-d');


        if ($day >= self::START_POKAZ_PERIOD){
            $rep_month = $month;
            $rep_month_m = $month_m;
            $rep_year = $year;
        } else {
            $rep_month = (int)date('n',strtotime($date_prev));
            $rep_month_m = (int)date('m',strtotime($date_prev));
            $rep_year = (int)date('Y',strtotime($date_prev));
        }
        $date_next = Carbon::createFromFormat('Y-m-d', $rep_year . '-' . $rep_month_m . '-01')->addMonth()->format('Y-m-d');
        return [
            'day' => $day,
            'rep_month' => $rep_month,
            'rep_month_m' => $rep_month_m,
            'rep_year' => $rep_year,
            'rep_month_prev' => self::getPrevMonthYear($rep_year,$rep_month)['month'],
            'rep_year_prev' => self::getPrevMonthYear($rep_year,$rep_month)['year'],
            'rep_month_next' => date('n',strtotime($date_next)), //для отображения в квитанци (следующий месяц за отчетным)
            'rep_year_next' => date('Y',strtotime($date_next)),  //для отображения в квитанци (следующий месяц за отчетным)

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
    //перевод в гигакаллории
    public static function toGcal($pokaz,$counterType)
    {
        switch ($counterType){
            case 1:
                return $pokaz * 0.000861;
            case 2:
                return $pokaz * 0.861;
            case 3:
                return $pokaz;
            case 4:
                return  $pokaz * 0.239;
        }
    }
    //определение единицы измерения
    public static function getUnits($counterType)
    {
        switch ($counterType){
            case 1:
                return 'кВт';
            case 2:
                return 'МВт';
            case 3:
                return 'Гкал';
            case 4:
                return  'Гдж';
        }
    }
    public static function getDataInGcal($volume,$year,$month):array
    {
        if ($volume == 'all'){
            $pokazs = Pokaz::with('getFlat')->where('year',$year)->where('month',$month)->get();
            $pokazs_prev = Pokaz::with('getFlat')->where('year',self::getPrevMonthYear($year,$month)['year'])->where('month',self::getPrevMonthYear($year,$month)['month'])->get();
        } else {
            $pokazs = Pokaz::with('getFlat')->where('year',$year)->where('month',$month)->where('flat',Auth::user()->flat)->get();
            $pokazs_prev = Pokaz::with('getFlat')->where('year',self::getPrevMonthYear($year,$month)['year'])->where('month',self::getPrevMonthYear($year,$month)['month'])->where('flat',Auth::user()->flat)->get();
        }
        if ($pokazs == null){
            echo 'Ошибка! Не найдены показания за текущий период!';
            exit();
        }
        if ($pokazs_prev == null){
            echo 'Ошибка! Не найдены показания за предыдущий период!';
            exit();
        }

        $prev = [];
        //dd($pokazs_prev);
        foreach ($pokazs_prev as $pokaz)
        {
            $prev[$pokaz->flat] = self::toGcal($pokaz->warm,$pokaz->getFlat->counterType);
        }
        $total = 0;
        foreach ($pokazs as $pokaz)
        {
            $total += self::toGcal($pokaz->warm,$pokaz->getFlat->counterType) - $prev[$pokaz->flat];
            $pokaz->warm = self::toGcal($pokaz->warm,$pokaz->getFlat->counterType);
        }

        $counter = Counter::where('year',$year)->where('month',$month)->first();
        $counter_prev = Counter::where('year',self::getPrevMonthYear($year,$month)['year'])->where('month',self::getPrevMonthYear($year,$month)['month'])->first();
        /*
        if ($counter == null){
            echo 'Ошибка! Не найдены показания общедомового счетчика за текущий период!';
            exit();
        }
        */
        return [
            'pokazs' => $pokazs,
            'prev' => $prev,
            'total' => $total,
            'counter' => is_null($counter)? null: $counter->warm,
            'counter_prev' => is_null($counter_prev)? null: $counter_prev->warm,

        ];
    }

    public static function getDataInRaw($volume,$year,$month):array
    {
        if ($volume == 'all'){
            $pokazs = Pokaz::with('getFlat')->where('year',$year)->where('month',$month)->get();
            $pokazs_prev = Pokaz::with('getFlat')->where('year',self::getPrevMonthYear($year,$month)['year'])->where('month',self::getPrevMonthYear($year,$month)['month'])->get();
        } else {
            $pokazs = Pokaz::with('getFlat')->where('year',$year)->where('month',$month)->where('flat',Auth::user()->flat)->get();
            $pokazs_prev = Pokaz::with('getFlat')->where('year',self::getPrevMonthYear($year,$month)['year'])->where('month',self::getPrevMonthYear($year,$month)['month'])->where('flat',Auth::user()->flat)->get();
        }
        if ($pokazs == null){
            echo 'Ошибка! Не найдены показания за текущий период!';
            exit();
        }
        if ($pokazs_prev == null){
            echo 'Ошибка! Не найдены показания за предыдущий период!';
            exit();
        }
        //формируем массив единиц измерения тепла
        $units = [];
        foreach ($pokazs as $pokaz)
        {
            $units[$pokaz->flat] = self::getUnits($pokaz->getFlat->counterType);
        }
        //формируем массив предыдущих показаний
        $prev = [];
        foreach ($pokazs_prev as $pokaz)
        {
            $prev[$pokaz->flat] = $pokaz->warm;
        }

        return [
            'pokazs' => $pokazs,
            'prev' => $prev,
            'units' => $units,
        ];
    }


    public static function getPayment($pokaz,$pokaz_prev,$tarif,$flat,$periodParams)
    {
        $flats = Flat::all();
        $residents_all = 0;
        foreach ($flats as $one){
            $residents_all += $one->residents;
        }

        $payment = [
            'day' => $periodParams['day'],
            'month' => $periodParams['rep_month'],
            'month_m' =>  $periodParams['rep_month_m'],
            'flat' => $flat->number,
            'fio' => $flat->name . ' ' . mb_substr($flat->first_name,0,1) . '.' . mb_substr($flat->mid_name,0,1) . '.',
            'month_name' => Pokaz::getMonthName($periodParams['rep_month']),
            'month_name_next' => Pokaz::getMonthName($periodParams['rep_month_next']),
            'month_next_m' => Pokaz::formatMonth($periodParams['rep_month_next']),
            'water_tarif' => $tarif->water,
            'service_tarif' => $tarif->service,
            'square_total' => $flat->square_total,
            'square_warm' => $flat->square_warm,
            'warmCounter' => $flat->warmCounter,
            'year' => $periodParams['rep_year'],
            'water' => ($pokaz->water - $pokaz_prev->water) * $tarif->water,
            'water_current' => $pokaz->water,
            'water_previous' => $pokaz_prev->water,
            'tarif_water' => $tarif->water,
            'warm_current' => number_format($pokaz->warm,0,'.',' '),
            'warm_previous' => number_format($pokaz_prev->warm,0,'.',' '),
            'service' => round($flat->square_total * $tarif->service,0),
            'tarif_service' => $tarif->service,
            'lift' => (int)$flat->useLift * round($tarif->lift * ($flat->residents / $residents_all),2),
            'rubbish' => round($tarif->rubbish * ($flat->residents / $residents_all),2),
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
                    .f-small{
                    font-size:9px;
                    }

                }</style><body>';
        $html .= '<div>ОСББ "Коммодіум" 49098 м.Дніпро, пров. Любарського, 4а, ЄДРПОУ 35807177, Р/рах. UA63 305299 00000 26007060006136 (26007060006136) в АТ КБ "Приватбанк",
                       МФО 305299</div>';
        $html .= '<div>РАХУНОК № ' . $payment['flat'] . ' / ' . $payment['month_next_m'] . '</div>';
        $html .= '<div>На сплату комунальних послуг       ' . $payment['fio'] . '      ' . $payment['month_name_next'] . ' ' . $payment['year'] . 'p.' . '</div>';
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
                <td>
                Вода (тариф ' . $payment['water_tarif'] .' грн)<br>
                <span class="f-small">Показання ліч-ка, поточні: ' . $payment['water_current'] . ', попередні: ' . $payment['water_previous'] .'</span>
            </td>
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

        $html .= '</div></body></head></html>';

        return $html;
    }

    public static function formatInvoiceWarm($data)
    {
        $total = $data['payment_main'] +  $data['payment_additional'];

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
        $html .= '<div>ОСББ "Коммодіум", ЄДРПОУ 35807177, 49098 м.Дніпро, пров. Любарського, 4а, Р/рах. UA63 305299 00000 26007060006136 в АТ КБ "Приватбанк",
                       МФО 305299</div>';
        $html .= '<div>КВИТАНЦІЯ  № ' . $data['flat'] . ' / ' . $data['month'] . '</div>';
        $html .= '<div>на сплату за спожиту теплову енергію за        ' . $data['month_name'] . '      '  . $data['year'] . 'p.' . '</div>';
        $html .= '<div style="width:500px;margin:0 auto">
        <table class="table table-striped">
            <tbody>
            <tr>
                <td>Опалювальна площа, кв.м</td>
                <td>' . $data['square'] . '</td>
            </tr>
            <tr>
                <td>за показниками квартирного лічильника тепла</td>
                <td>' . $data['payment_main'] . '</td>
            </tr>
            <tr>
                <td>донарахування за розрахунковими показниками загальнобудинкового лічильника спожитої теплової енергії (тариф - ' . $data['tarifAdditional'] . ' грн за кв.м)</td>
                <td>' . $data['payment_additional'] . '</td>
            </tr>
            <tr>
                <td>Всього до сплати</td>
                <td>' . $total . '</td>
            </tr>

            </tbody>
        </table>
        </div>';

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
    /*
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
    */
}
