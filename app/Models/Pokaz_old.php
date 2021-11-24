<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon as Carbon;

class Pokaz extends Model
{
    use HasFactory;

    protected $fillable = [
        'water',
        'warm'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPeriodParams()
    {
        $date = date('Y-m-d',time());
        $year = date('Y',strtotime($date));
        $month = date('n',strtotime($date));
        $month_m = date('m',strtotime($date));
        $day = date('j',strtotime($date));


        if ($day >=1 && $day <=5) {
            $end = new Carbon('last day of last month');
            $day = date("d",$end->endOfMonth()->format('Y-m-d'));

            $date_prev = Carbon::createFromFormat('Y-m-d', $end->endOfMonth()->format('Y-m-d'))->subMonth()->format('Y-m-d');
            $year_prev = date('Y',strtotime($date_prev));
            $month_prev = date('n',strtotime($date_prev));
            $month_prev_m = date('m',strtotime($date_prev));
            $day_prev = date('j',strtotime($date_prev));
        } else {
            $date_prev = Carbon::createFromFormat('Y-m-d', $date)->subMonth()->format('Y-m-d');
            $year_prev = date('Y',strtotime($date_prev));
            $month_prev = date('n',strtotime($date_prev));
            $month_prev_m = date('m',strtotime($date_prev));
            $day_prev = date('d',strtotime($date_prev));
        }

        return [
            'year' => $year,
            'month' => $month,
            'month_m' => $month_m,
            'day' => $day,
            'year_prev' => $year_prev,
            'month_prev' => $month_prev,
            'month_prev_m' => $month_prev_m,
            'day_prev' => $day_prev
        ];
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
        </div>
        <div style="width:120px;font-weight:bold;margin:0 auto">ОПАЛЕННЯ</div>
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
                <td>' . ($payment['warmCounter'] == 1? 'є': 'нема') . '</td>
                <td>' . $payment['warm'] . '</td>
                <td>' . $payment['warm_previous'] . '</td>
                <td>' . $payment['warm_current'] . '</td>
            </tr>
            </tbody>
        </table>



        </div>



    </div>';
    $html .= '</body></head></html>';





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
        unlink($filepath);

    }

}
