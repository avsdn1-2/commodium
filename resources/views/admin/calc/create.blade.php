@extends('admin.layout')
@section('title') Формирование квитанции за обслуживание за квартиру @endsection

@section('content')


    <!--  -->


    <form method="POST" style="width:300px;margin:0 auto" action="{{ route('admin.calc') }}">
    @csrf
        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />
        <div style="width:300px">
            <x-label for="water" :value="__('Номер квартиры')" />
            <input id="flat" class="block mt-1 w-full" style="border-radius:5px;" type="text" name="flat"  value="" required autofocus />
        </div>

        <div class="flex items-center justify-end mt-4">

            <x-button class="ml-3">
                {{ __('Сформировать квитанцию') }}
            </x-button>
        </div>
    </form>

@endsection


