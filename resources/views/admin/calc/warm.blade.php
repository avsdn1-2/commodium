<x-app-layout>

    @if ($counter == null)
        <div  style="width:600px;margin:0 auto;border:2px solid red">Отсутствуют показания общедомового счетчика тепла за {{ $rep_month }} {{ $rep_year }} </div>
    @endif
    @if ($flatsWithoutPokaz_str !== '')
       <div style="margin-left:30px;color: red">Отсутствуют показания тепла за {{ $rep_month }} {{ $rep_year }} г. по квартирам: {{ $flatsWithoutPokaz_str }}</div>
    @else
       <div style="margin-left:30px;color: green">Есть все данные для расчета квитанций за тепло за {{ $rep_month }} {{ $rep_year }}</div>
    @endif




</x-app-layout>


