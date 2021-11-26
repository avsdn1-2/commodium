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
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col" style="width:70px;text-align:center">Год</th>
                        <th scope="col" style="width:180px;text-align:center">Месяц</th>
                        <th scope="col" style="width:60px;text-align:center">Вода</th>
                        <th scope="col" style="width:100px;text-align:center">Тепло</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($pokazs as $pokaz)
                        <tr>
                            <td style="text-align:center">{{ $pokaz->year }}</td>
                            <td style="text-align:center">{{ $pokaz->month }} </td>
                            <td style="text-align:center">{{ $pokaz->water }}</td>
                            <td style="text-align:center">{{ $pokaz->warm }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
