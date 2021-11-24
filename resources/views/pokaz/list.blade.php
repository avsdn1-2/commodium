<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Показания') }}
        </h2>
    </x-slot>


    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div>История показаний</div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div style="width:320px">
                        <div style="width:75px;float:left;">Год</div>
                        <div style="width:75px;float:left;">Месяц</div>
                        <div style="width:75px;float:left;">Вода</div>
                        <div style="width:75px;float:right;">Тепло</div>
                    </div>
                    <ul class="list-disc">
                    @foreach($pokazs as $pokaz)
                        <div style="width:550px;height:25px;margin-bottom:5px/*border:1px solid green*/">
                            <div style="width:320px;float:left">
                                <div style="width:75px;float:left;">{{ $pokaz->year }}</div>
                                <div style="width:75px;float:left;">{{ $pokaz->month }}</div>
                                <div style="width:75px;float:left;">{{ $pokaz->water }}</div>
                                <div style="width:75px;float:right;">{{ $pokaz->warm }}</div>
                            </div>

                        </div>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
