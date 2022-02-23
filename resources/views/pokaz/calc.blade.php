



<x-app-layout>

    <div style="width:250px;margin:0 auto;font-weight:bold;font-size:17px">КВИТАНЦІЯ   №  {{ $payment['flat'] }} / {{ $payment['month_next_m'] }} </div>
    <div style="width:550px;margin:0 auto;font-weight:bold">На сплату комунальних послуг  {{ $payment['fio'] }}     {{ $payment['month_name_next'] }}   {{ $payment['year'] }}p.</div>

    <div style="width:550px;margin:0 auto">
        <table class="table" style="border:2px solid grey"> <!-- table-striped -->
            <thead>
            <tr>
                <th scope="col">Послуга</th>
                <th scope="col">Сума</th>
            </tr>
            </thead>
            <tbody>
            <tr style="border-bottom: 1px solid black">
                <td>Обслуговування будинку (тариф {{ $payment['tarif_service'] }} грн за кв.м)</td>
                <td>{{ $payment['service'] }}</td>
            </tr>
            <tr>
                <td>Обслуговування ліфту</td>
                <td>{{ $payment['lift']  }}</td>
            </tr>
            <tr>
                <td>Вивіз сміття</td>
                <td>{{ $payment['rubbish']  }}</td>
            </tr>
            <tr>
                <td>
                    Вода (тариф {{ $payment['tarif_water'] }} грн за куб.м)<br>
                    <span class="f-small">Показання лічильника, поточні: {{ $payment['water_current'] }}, попередні:  {{ $payment['water_previous'] }}</span>
                </td>
                <td>{{ $payment['water']  }}</td>
            </tr>
            <tr>
                <td>Прибирання паркувальних місць</td>
                <td>{{ $payment['parkingCleaning']  }}</td>
            </tr>
            <tr>
                <td>Освітлення паркувальних місць</td>
                <td>{{ $payment['parkingLightening']  }}</td>
            </tr>
            <tr>
                <td>РАЗОМ ДО СПЛАТИ</td>
                <td>{{ $payment['total']  }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center;font-weight:bold">КОНС'ЄРЖІ</td>
            </tr>
            <tr>
                <td>з/плата</td>
                <td>{{ $payment['cons']  }}</td>
            </tr>
            </tbody>
        </table>
    </div>



    <div class="text-center pdf-btn">
        <a href="{{ route('pdf.generate',['month'=>$payment['month_m']]) }}" class="btn btn-primary">Отправить на емейл</a>
    </div>
</x-app-layout>

<style>
    table tr{
        border-bottom:1px solid black;
    }
    .f-small{
        font-size:11px;
    }
</style>


