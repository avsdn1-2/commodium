



<x-app-layout>

    <div style="width:200px;margin:0 auto">Квитанція на {{ $payment['day'] }}.{{ $payment['month_m'] }}.{{ $payment['year'] }}</div>

    <div style="width:150px;font-weight:bold;margin:0 auto">ОБСЛУГОВУВАННЯ</div>
    <div style="width:500px;margin:0 auto">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">Послуга</th>
                <th scope="col">Сума</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Обслуговування дому</td>
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
                <td>Вода</td>
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

    @if ($payment['warm'] !== 0)
    <!--<div style="width:120px;font-weight:bold;margin:0 auto">ОПАЛЕННЯ</div>
        <div style="width:800px;margin:0 auto">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col" style="width:70px;text-align:center">Кв-ра</th>
                    <th scope="col" style="width:180px;text-align:center">ПІБ</th>
                    <th scope="col" style="width:60px;text-align:center">кв. м</th>
                    <th scope="col" style="width:100px;text-align:center">Ліч-ник</th>
                    <th scope="col" style="width:110px;text-align:center">Сума</th>
                    <th scope="col" colspan="2" style="width:60px;text-align:center">Показання лічильника<br>попер/поточні</th>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <td style="text-align:center">{{ $payment['flat'] }}</td>
                    <td style="text-align:center">{{ $payment['fio'] }} </td>
                    <td style="text-align:center">{{ $payment['square'] }}</td>
                    <td style="text-align:center">{{ $payment['warmCounter'] == 1? 'є': 'нема' }}</td>
                    <td style="text-align:center">{{ $payment['warm'] }}</td>
                    <td style="text-align:center">{{ $payment['warm_previous'] }}</td>
                    <td style="text-align:center">{{ $payment['warm_current'] }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    -->
    @endif

    <div class="text-center pdf-btn">
        <a href="{{ route('pdf.generateManager',['flat' => $payment['flat'],'month' => $payment['month_m']]) }}" class="btn btn-primary">Отправить на емейл</a>
    </div>
</x-app-layout>


