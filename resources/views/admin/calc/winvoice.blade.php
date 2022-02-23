



<x-app-layout>

    <div style="width:300px;margin:0 auto;font-weight:bold;font-size:17px">КВИТАНЦІЯ   №  {{ $data['flat'] }} / {{ $data['month'] }}        {{ $data['fio'] }}</div>
    <div style="width:550px;margin:0 auto;font-weight:bold">на сплату за спожиту теплову енергію за  {{ $data['month_name'] }}      {{ $data['year'] }}p.</div>

    <div style="width:550px;margin:0 auto">
        <table class="table table-striped">
            <tbody>
            <tr>
                <td>Опалювальна площа, кв.м</td>
                <td>{{ $data['square'] }}</td>
            </tr>
            <tr>
                <td>за показниками квартирного лічильника тепла</td>
                <td>{{ $data['payment_main']  }}</td>
            </tr>
            <tr>
                <td>донарахування за розрахунковими показниками загальнобудинкового лічильника спожитої теплової енергії (тариф - {{ $data['tarifAdditional'] }} грн за кв.м)</td>
                <td>{{ $data['payment_additional']  }}</td>
            </tr>
            <tr>
                <td>Всього до сплати</td>
                <td>{{ $data['payment_main'] +  $data['payment_additional'] }}</td>
            </tr>

            </tbody>
        </table>
    </div>



    <div class="text-center pdf-btn">
        <a href="{{ route('pdf.generateWarmManager',['flat' => $data['flat'],'month' => $data['month']]) }}" class="btn btn-primary">Надіслати на ємейл</a>
    </div>
</x-app-layout>


