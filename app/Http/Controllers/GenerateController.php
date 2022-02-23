<?php

namespace App\Http\Controllers;

use App\Models\Pokaz;
use App\Services\CalcService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Generate;
use PDF;

class GenerateController extends Controller
{
    /**
     * @var CalcService
     */
    private $calcService;

    public function __construct(CalcService $calcService)
    {
        $this->calcService = $calcService;
    }

    // function to display preview
    //для теста
    public function preview()
    {
        return view('pdf.preview');
    }

    public function generatePDF($month)
    {
        $payment = $this->calcService->getServiceData(Auth::user()->flat);
        $html = Pokaz::formatInvoice($payment);

        //создаем pdf-документ для выгрузки в файл
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        //создаем копию pdf-документа для выгрузки в браузер
        $pdf_copy = App::make('dompdf.wrapper');
        $pdf_copy->loadHTML($html);

        //записываем квитанцию в pdf-файл
        $filename = 'invoice_' . Auth()->user()->flat . '.pdf';
        file_put_contents (base_path("storage/app/public/$filename"), $pdf->output());

        //отправляем сообщение с прикрепленным pdf-файлом на почту
        Generate::sendEmail(Auth::user()->email,Auth::user()->flat,$month,$filename);
        Generate::sendEmail(Pokaz::admin_email,Auth::user()->flat,$month,$filename);
        Generate::deleteFile($filename);

        return $pdf_copy->stream();
        //return $pdf->download('invoice.pdf'); //скачивание pdf-файла

        //return PDF::loadFile(public_path().'/myfile.html')->save('/path-to/my_stored_file.pdf')->stream('download.pdf');
    }
    public function generatePDFmanager($flat,$month)
    {
        $payment = $this->calcService->getServiceData($flat);
        $html = Pokaz::formatInvoice($payment);

        //создаем pdf-документ для выгрузки в файл
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        //создаем копию pdf-документа для выгрузки в браузер
        $pdf_copy = App::make('dompdf.wrapper');
        $pdf_copy->loadHTML($html);

        $filename = 'manager_invoice_' . $flat . '.pdf';
        //записываем квитанцию в pdf-файл
        file_put_contents (base_path("storage/app/public/$filename"), $pdf->output());

        //отправляем сообщение с прикрепленным pdf-файлом на почту
        Generate::sendEmail(Pokaz::admin_email,$flat,$month,$filename);
        Generate::sendEmail(Pokaz::manager_email,$flat,$month,$filename);
        Generate::deleteFile($filename);

        return $pdf_copy->stream();
        //return $pdf->download('invoice.pdf'); //скачивание pdf-файла

        //return PDF::loadFile(public_path().'/myfile.html')->save('/path-to/my_stored_file.pdf')->stream('download.pdf');
    }

    public function generatePdfWarmManager($flat,$month)
    {
        $data = $this->calcService->getWarmData($flat);

        $html = Pokaz::formatInvoiceWarm($data);

        //создаем pdf-документ для выгрузки в файл
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        //создаем копию pdf-документа для выгрузки в браузер
        $pdf_copy = App::make('dompdf.wrapper');
        $pdf_copy->loadHTML($html);

        $filename = 'winvoice_' . $flat . '.pdf';
        //записываем квитанцию в pdf-файл
        file_put_contents (base_path("storage/app/public/$filename"), $pdf->output());

        Generate::sendEmail($data['email'],$flat,$month,$filename);
        Generate::deleteFile($filename);

        return $pdf_copy->stream();

    }
}
