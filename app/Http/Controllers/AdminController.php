<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pokaz;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function adminCreate()
    {
        if (!Auth::user()->is_manager || !Auth::user()->is_admin ) {
            abort(403,'Доступ запрещен!');
        }
        $result = Pokaz::getRepPeriodAdmin();

        return view('admin.pokaz.create', [
            'rep_month' => $result['rep_month'],
            'rep_year' => $result['rep_year'],
        ]);
    }

    public function adminStore(Request $request)
    {
        if (!Auth::user()->is_manager || !Auth::user()->is_admin ) {
            abort(403,'Доступ запрещен!');
        }
        $rules = [
            'flat' => 'required|string|max:3|regex:#^[0-9А-Яа-я]+$#',
            'year' => 'required|integer',
            'month' => 'required|integer',
            'water' => 'required|integer',
            'warm' => 'nullable|integer'
        ];
        try {
            $validatedData = $request->validate($rules);
        } catch (\ValidationException $exception) {
            return back()->withErrors(['msg' => $exception->getMessage()])->withInput();
        }

        $pokaz = Pokaz::where('flat',$request->get('flat'))->where('year',$request->get('year'))->where('month',$request->get('month'))->get()->first();
        if ($pokaz == null){
            //сравнение с показаниями за предыдущий месяц
            $pokaz_prev = Pokaz::where('flat',$request->get('flat'))->where('year',Pokaz::getRepPeriodAdmin()['rep_year_prev'])->where('month',Pokaz::getRepPeriodAdmin()['rep_month_prev'])->get()->first();

            if ($pokaz_prev !== null){
                if ($pokaz_prev->water <= $request->get('water') && $pokaz_prev->warm <= $request->get('warm')){
                    $pokaz = new Pokaz();
                    $pokaz->flat = $request->get('flat');
                    $pokaz->year = $request->get('year');
                    $pokaz->month = $request->get('month');
                    $pokaz->water = $request->get('water');
                    $pokaz->warm = $request->get('warm');
                    $error_save = !$pokaz->save();
                    $error_message = '';
                } else {
                    $error_message = 'Вы вводите меньшие показания чем за прошлый месяц!';
                    $error_save = false;
                }
            } else {
                $pokaz = new Pokaz();
                $pokaz->flat = $request->get('flat');
                $pokaz->year = $request->get('year');
                $pokaz->month = $request->get('month');
                $pokaz->water = $request->get('water');
                $pokaz->warm = $request->get('warm');
                $error_save = !$pokaz->save();
                $error_message = '';
            }
        } else {
            $error_message = 'Показания для этой квартиры за указанный период уже переданы!';
            $error_save = false;
        }

        return view('admin.pokaz.create', [
            'error_message' => $error_message,
            'error_save' => $error_save,
            'flat' => $request->get('flat'),
            'rep_month' => $request->get('month'),
            'rep_year' => $request->get('year'),
        ]);
    }
}
