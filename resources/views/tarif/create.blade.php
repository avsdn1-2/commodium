<x-app-layout>
    <x-slot name="header">
        <div style="width:300px;margin:0 auto">Введите данные квартиры</div>
    </x-slot>
    <form method="POST" style="width:300px;margin:0 auto" action="{{ route('flat.store') }}">
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
                <x-label for="number" :value="__('Номер')" />

                <x-input id="number" class="block mt-1 w-full" type="text" name="number" :value="old('number')" required autofocus />

                <x-label for="square" :value="__('Площадь')" />

                <x-input id="square" class="block mt-1 w-full" type="text" name="square" :value="old('square')" required />

                <x-label for="privilege" :value="__('Льгота')" />

                <x-input id="privilege" class="block mt-1 w-full" type="text" name="privilege" :value="old('privilege')" />
            </div>
        </div>

        <div class="flex items-center justify-center mt-4">

            <x-button class="ml-3">
                {{ __('Сохранить тарифы') }}
            </x-button>
        </div>
    </form>
</x-app-layout>
