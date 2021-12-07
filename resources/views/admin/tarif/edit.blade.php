@extends('admin.layout')
@section('title') Добавление Email @endsection

@section('content')

    <div style="width:300px;margin:0 auto">Введите текущие тарифы</div>

    <?php if (!isset($_POST['water'])): ?>
    <form method="POST" style="width:300px;margin:0 auto" action="{{ route('tarif.update') }}">
        @csrf
        <div style="width:300px">
            @if ($errors->any())
                <div class="row justify-content-center" style="border-radius:4px;background-color: hotpink;">
                    <div class="col-md-11">
                        <div class="alert alert-danger" role="alert">
                            <div style="width:75%;margin:3px 0 0 10px;font-size:13px;float:left">
                                @foreach($errors->all(':message') as $one)
                                    {{ $one }}
                                @endforeach
                            </div>
                            <div style="width:13%;margin-left:87%;">
                                <button style="position:relative;left:15px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div style="clear:both"></div>
            <div>
                <x-label for="water" :value="__('Тариф за воду')" />

                <x-input id="water" class="block mt-1 w-full" type="text" name="water" :value="isset($tarif->water)? $tarif->water: 0" required autofocus />

                <x-label for="warm" :value="__('Тариф за тепло')" />

                <x-input id="warm" class="block mt-1 w-full" type="text" name="warm" :value="isset($tarif->warm)? $tarif->warm: 0" required />

                <x-label for="service" :value="__('Тариф за обслуживание')" />

                <x-input id="service" class="block mt-1 w-full" type="text" name="service" :value="isset($tarif->service)? $tarif->service: 0" />

                <x-label for="lift" :value="__('Тариф за обслуживание лифта')" />

                <x-input id="lift" class="block mt-1 w-full" type="text" name="lift" :value="isset($tarif->lift)? $tarif->lift: 0" />

                <x-label for="rubbish" :value="__('Тариф за вывоз мусора')" />

                <x-input id="rubbish" class="block mt-1 w-full" type="text" name="rubbish" :value="isset($tarif->rubbish)? $tarif->rubbish: 0" />

                <x-label for="parkingCleaning" :value="__('Тариф за уборку парковочных мест')" />

                <x-input id="parkingCleaning" class="block mt-1 w-full" type="text" name="parkingCleaning" :value="isset($tarif->parkingCleaning)? $tarif->parkingCleaning: 0" />

                <x-label for="parkingLightening" :value="__('Тариф за освещение парковочных мест')" />

                <x-input id="parkingLightening" class="block mt-1 w-full" type="text" name="parkingLightening" :value="isset($tarif->parkingLightening)? $tarif->parkingLightening: 0" />

                <x-label for="cons" :value="__('Зарплата консьержей')" />

                <x-input id="cons" class="block mt-1 w-full" type="text" name="cons" :value="isset($tarif->cons)? $tarif->cons: 0" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">

            <x-button class="ml-3">
                {{ __('Сохранить') }}
            </x-button>
        </div>
    </form>
    <?php else: ?>
    <div style="width:120px;border:1px solid limegreen;background-color: limegreen;">Тарифы сохранены!</div>
    <?php endif; ?>

@endsection


