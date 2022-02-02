

@component('mail::message')

<div>
    ОСББ "Коммодіум" 49098 м.Дніпро, пров. Любарського, 4а ЄДРПОУ 35807177 Р/рах. UA63 305299 00000 26007060006136 в АТ КБ "Приватбанк",
        МФО 305299
</div>
<div style="font-weight:bold;">КВИТАНЦІЯ №   {{ $data['flat'] }} / {{ $month }}   {{ $data['fio'] }} </div>
<div>на сплату за спожиту теплову енергію за {{ $month_name }}  {{ $year }}p. </div>

<div style="width:600px;border-top:1px solid grey;margin-top:15px;">Опалювальна площа, м.кв.                           <span style="width:100px;float:right">{{ $data['square'] }}</span></div>
<div style="width:600px;border-top:1px solid grey">за показниками квартирного лічильника, грн             <span style="width:100px;float:right">{{ $data['payment_main'] }}</span></div>
<div style="width:600px;border-top:1px solid grey">донарахування за розрахунковими показниками</div>
<div>загальнобудинкового лічильника спожитої теплової</div>
<div style="width:600px">енергії (тариф - {{ $tariff_additional }} грн за м.кв.), грн                                  <span style="width:100px;float:right">{{ $data['payment_additional'] }}</span></div>
<div style="width:600px;font-weight:bold;border-top:1px solid grey;border-bottom:1px solid grey;">Всього за місяць:  <span style="width:100px;float:right">{{ $data['payment_main'] + $data['payment_additional'] }} грн</span></div>

@endcomponent


