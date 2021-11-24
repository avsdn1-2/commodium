<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generate extends Model
{
    use HasFactory;

    public static function sendEmail($to,$flat,$month)
    {
        $subject = "КВИТАНЦІЯ № " . $flat . ' / ' . $month;

        $message = "invoice";
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
        //$multipart .= chunk_split(base64_encode(iconv("utf8", "windows-1251", $message)));
        $multipart .= chunk_split(base64_encode($message));


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
