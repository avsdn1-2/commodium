@extends('admin.layout')
@section('title') Внесение показаний за квартиру @endsection

@section('content')
    @if (isset($error_message) && $error_message !== '')
        <div style="width:300px;border:1px solid red;border-radius:5px;color:red;margin:0 auto"><p style="margin:0 0 0 10px">{{ $error_message }}</p></div>
    @elseif (isset($error_save))
        @if ($error_save == false)
            <div style="width:300px;border:1px solid green;border-radius:5px;color:green;margin:0 auto"><p style="margin:0 0 0 10px">Показания для квартиры {{{ $flat  }}} <br>успешно сохранены</p></div>
        @else
            <div style="width:300px;border:1px solid red;border-radius:5px;color:red;margin:0 auto"><p style="margin:0 0 0 10px">Ошибка сохранения данных!</p></div>
        @endif
    @endif

    <!--  -->


    <form method="POST" style="width:300px;margin:0 auto" action="{{ route('pokaz.adminStore') }}">
    @csrf
        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />
        <div style="width:300px">
            <x-label for="water" :value="__('Номер квартиры')" />
            <input id="flat" class="block mt-1 w-full" style="border-radius:5px;" type="text" name="flat"  value="" required autofocus />

            <x-label for="year" :value="__('Год')" />
            <div class="md-form" style="/*width:200px;margin:0 0 0 20px;float:left;*/">
                <select id="year" name="year" class="browser-default custom-select custom-select mb-3">
                    <option value="{{ $rep_year }}" selected >{{ $rep_year }}</option>
                    <option value="{{ $rep_year - 1 }}" >{{ $rep_year - 1 }}</option>
                </select>
            </div>

            <x-label for="month" :value="__('Месяц')" />
            <div class="md-form" style="/*width:200px;margin:0 0 0 20px;float:left;*/">
                <select id="month" name="month" class="browser-default custom-select custom-select mb-3">
                    <option value="1" {{ $rep_month == 1? 'selected': '' }}>январь</option>
                    <option value="2" {{ $rep_month == 2? 'selected': '' }}>февраль</option>
                    <option value="3" {{ $rep_month == 3? 'selected': '' }}>март</option>
                    <option value="4" {{ $rep_month == 4? 'selected': '' }}>апрель</option>
                    <option value="5" {{ $rep_month == 5? 'selected': '' }}>май</option>
                    <option value="6" {{ $rep_month == 6? 'selected': '' }}>июнь</option>
                    <option value="7" {{ $rep_month == 7? 'selected': '' }}>июль</option>
                    <option value="8" {{ $rep_month == 8? 'selected': '' }}>август</option>
                    <option value="9" {{ $rep_month == 9? 'selected': '' }}>сентябрь</option>
                    <option value="10" {{ $rep_month == 10? 'selected': '' }}>октябрь</option>
                    <option value="11" {{ $rep_month == 11? 'selected': '' }}>ноябрь</option>
                    <option value="12" {{ $rep_month == 12? 'selected': '' }} >декабрь</option>
                </select>

            </div>

            <x-label for="water" :value="__('Вода')" />
            <input id="water" class="block mt-1 w-full" style="border-radius:5px;" type="text" name="water"  value="" required  />

            <x-label for="warm" :value="__('Тепло')" />
            <input id="warm" class="block mt-1 w-full" style="border-radius:5px;" type="text" name="warm" value="" />

        </div>

        <div class="flex items-center justify-end mt-4">

            <x-button class="ml-3">
                {{ __('Сохранить показания') }}
            </x-button>
        </div>
    </form>

@endsection


