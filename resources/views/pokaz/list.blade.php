<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Показания') }}
        </h2>
    </x-slot>


    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h3>История показаний</h3>
            <form method="POST" style="width:700px;margin:0 auto" action="{{ route('pokaz.info') }}">
                @csrf
                <x-label for="year" :value="__('Год')" style="width:60px;float:left" />
                <div class="md-form" style="width:100px;margin:0 0 0 0;float:left">
                    <select id="year" name="year" class="browser-default custom-select custom-select mb-3">
                        <option value="{{ $rep_year }}" selected>{{ $rep_year }}</option>
                        <option value="{{ $rep_year - 1 }}" >{{ $rep_year - 1 }}</option>
                    </select>
                </div>

                <x-label for="month" :value="__('Месяц')" style="width:60px;margin:0 0 0 30px;float:left" />
                <div class="md-form" style="width:100px;margin:0 0 0 0;float:left">
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

                <x-label for="volume" :value="__('Объем')" style="width:60px;margin:0 0 0 30px;float:left" />
                <div class="md-form" style="width:100px;margin:0 0 0 0;float:left">
                    <select id="volume" name="volume" class="browser-default custom-select custom-select mb-3">
                        <option value="my" {{ $volume == 'my'? 'selected': '' }}>Мои</option>
                        <option value="all" {{ $volume == 'all'? 'selected': '' }} >Все</option>
                    </select>
                </div>


                <div class="flex items-center justify-end mt-4">

                    <x-button class="ml-3">
                        {{ __('Выбрать') }}
                    </x-button>
                </div>

            </form>
            <div style="clear:both"></div>

            @if (isset($counter) && $counter !== null)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                          <!--  <th scope="col" style="width:70px;text-align:center"></th>
                            <th scope="col" style="width:180px;text-align:center"></th> -->
                            <th scope="col" style="width:180px;text-align:center">Месячный небаланс, кВт</th>
                            <th scope="col" style="width:100px;text-align:center">Потребление тепла по <br>общему счетчику за месяц, кВт</th>
                            <th scope="col" style="width:60px;text-align:center">Общий счетчик, кВт<br>тек / пред</th>
                            <th scope="col" style="width:100px;text-align:center">Общее потребление тепла всеми квартирами за месяц, кВт</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                        <!--    <td style="text-align:center"></td>
                            <td style="text-align:center"></td> -->
                            <td style="text-align:center">{{ number_format($counter - $counter_prev - $total,0,'.',' ') }}</td>
                            <td style="text-align:center">{{ number_format($counter - $counter_prev,0,'.',' ') }}</td>
                            <td style="text-align:center">{{ number_format($counter,0,'.',' ') }}  /  {{ number_format($counter_prev,0,'.',' ') }}</td>
                            <td style="text-align:center">{{ number_format($total,0,'.',' ') }}</td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            @endif



            @if (isset($pokazs))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                         <!--   <th scope="col" style="width:70px;text-align:center">Год</th>
                            <th scope="col" style="width:180px;text-align:center">Месяц</th> -->
                            <th scope="col" style="width:180px;text-align:center">Квартира</th>
                            <th scope="col" style="width:60px;text-align:center">Вода, куб.м</th>
                            <th scope="col" style="width:100px;text-align:center">Тепло, кВт<br>тек / пред</th>
                            <th scope="col" style="width:100px;text-align:center">Потребление тепла за месяц, кВт</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($pokazs as $pokaz)
                            <tr>
                            <!--    <td style="text-align:center">{{ $pokaz->year }}</td>
                                <td style="text-align:center">{{ $pokaz->month }} </td> -->
                                <td style="text-align:center">{{ number_format($pokaz->flat,0,'.',' ') }} </td>
                                <td style="text-align:center">{{ number_format($pokaz->water,0,'.',' ') }}</td>
                                <td style="text-align:center">{{ number_format($pokaz->warm,0,'.',' ') }}  /  {{ number_format($prev[$pokaz->flat],0,'.',' ') }}</td>
                                <td style="text-align:center">{{ number_format($pokaz->warm - $prev[$pokaz->flat],0,'.',' ') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
