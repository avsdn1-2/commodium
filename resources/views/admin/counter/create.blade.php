@extends('admin.layout')
@section('title') Внесение показаний общедомового счетчика @endsection

@section('content')
    <div style="width:300px;margin:0 auto">
        <div class="font-semibold text-gray-800 leading-tight">
            {{ __('Общедомовой счетчик') }}
        </div>
    </div>

    @if (isset($message) && $message !== '')
        <div style="width:300px;border:1px solid green;background-color:green;text-align:center;border-radius:5px;color:white;margin:0 auto"><p style="margin:0 0 0 10px">{{ $message }}</p></div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" style="width:300px;margin:0 auto"/>

            <form method="POST" style="width:300px;margin:0 auto" action="{{ route('counter.store') }}">
                @csrf
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

                <x-label for="warm" :value="__('Показания счетчика тепла')" />
                <input id="warm" class="block mt-1 w-full" style="border-radius:5px;" type="text" name="warm" value="" required/>

                <div class="flex items-center justify-end mt-4">
                    <x-button class="ml-3">
                        {{ __('Сохранить показания') }}
                    </x-button>
                </div>

            </form>

        </div>
    </div>

@endsection


