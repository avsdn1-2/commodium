<x-app-layout>

    @if ($counter == null)
        <div  style="width:600px;margin:0 auto;border:2px solid red">Отсутствуют показания общедомового счетчика тепла за {{ $rep_month }} {{ $rep_year }} </div>
    @endif
    @if ($flatsWithoutPokaz_str !== '')
       <div style="width:600px;margin:50px auto 0 auto;border:2px solid red">Отсутствуют показания тепла за {{ $rep_month }} {{ $rep_year }} г. по квартирам: {{ $flatsWithoutPokaz_str }}</div>
    @endif
    @if ($counter !== null)
            <div style="width:600px;margin:50px auto 0 auto;border:2px solid green">Есть все данные для расчета квитанций за тепло за {{ $rep_month }} {{ $rep_year }}</div>
    @endif



</x-app-layout>


