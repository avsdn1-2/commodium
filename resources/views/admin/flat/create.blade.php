@extends('admin.layout')
@section('title') Добавление Квартиры @endsection

@section('content')

    <div style="width:300px;margin:0 auto">Введите данные квартиры</div>

    <form method="POST" style="width:300px;margin:0 auto" action="{{ route('flat.store') }}">
        @csrf
        <div style="width:300px">

            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <div style="clear:both"></div>
            <div>
                <x-label for="number" :value="__('Номер')" />

                <x-input id="number" class="block mt-1 w-full" type="text" name="number" :value="old('number')" required autofocus />

                <x-label for="square" :value="__('Площадь')" />

                <x-input id="square" class="block mt-1 w-full" type="text" name="square" :value="old('square')" required />

                <div class="form-check" style="margin:20px 0 20px 0">
                    <input class="form-check-input" type="checkbox" value="warmCounter" name="warmCounter" id="flexCheckWarmCounter"/>
                    <label class="form-check-label" for="flexCheckWarmCounter">Установлен счетчик тепла</label>
                </div>

                <div class="form-check" style="margin:20px 0 20px 0">
                    <input class="form-check-input" type="checkbox" value="useLift" name="useLift" id="flexCheckUseLift"/>
                    <label class="form-check-label" for="flexCheckUseLift">Использует лифт</label>
                </div>

                <x-label for="privilege" :value="__('Льгота')" />

                <x-input id="privilege" class="block mt-1 w-full" type="text" name="privilege" :value="old('privilege')" />

                <x-label for="name" :value="__('Фамилия')" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />

                <x-label for="first_name" :value="__('Имя')" />
                <x-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required />

                <x-label for="mid_name" :value="__('Отчество')" />
                <x-input id="mid_name" class="block mt-1 w-full" type="text" name="mid_name" :value="old('mid_name')" required />
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">

            <x-button class="ml-3">
                {{ __('Сохранить параметры квартиры') }}
            </x-button>
        </div>
    </form>

@endsection


