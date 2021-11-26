<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Generate;
use PDF;

class GenerateController extends Controller
{
    // function to display preview
    //для теста
    public function preview()
    {
        return view('pdf.preview');
    }

    public function generatePDF($month)
    {
        //если в кеше содержится информацию о клиенте за данный отчетный год, то достаем ее из кеша
        if (Cache::has(Auth::user()->id))
        {
            $html = Cache::get(Auth::user()->id);
        }

        //создаем pdf-документ для выгрузки в файл
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        //создаем копию pdf-документа для выгрузки в браузер
        $pdf_copy = App::make('dompdf.wrapper');
        $pdf_copy->loadHTML($html);

        //записываем квитанцию в pdf-файл
        file_put_contents (base_path('storage/app/public/invoice.pdf'), $pdf->output());

        //отправляем сообщение с прикрепленным pdf-файлом на почту
        Generate::sendEmail(Auth::user()->email,Auth::user()->flat,$month);

        return $pdf_copy->stream();
        //return $pdf->download('invoice.pdf'); //скачивание pdf-файла

        //return PDF::loadFile(public_path().'/myfile.html')->save('/path-to/my_stored_file.pdf')->stream('download.pdf');
    }
}
