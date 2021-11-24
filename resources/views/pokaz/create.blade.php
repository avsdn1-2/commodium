<x-app-layout>
    @if (isset($error_message))
        <div style="width:300px;border:1px solid red;border-radius:5px;color:red;margin:0 auto"><p style="margin:0 0 0 10px">{{ $error_message }}</p></div>
    @endif
    <!--  -->
    @if ($day >= $start_pokaz_period || $day <= $end_pokaz_period)
        @if ($day >= $start_pokaz_period)

            <div style="width:300px;margin:50px auto 0 auto">Введите показания на {{ $start_pokaz_period }}.{{ $rep_month }}.{{ $rep_year }}</div>

        @else

            <div style="width:300px;margin:0 auto">Введите показания на {{ $start_pokaz_period }}.{{ $rep_month_prev }}.{{ $rep_year_prev }}</div>

        @endif
        <form method="POST" style="width:300px;margin:0 auto" action="{{ route('pokaz.store') }}">
        @csrf
            <div style="width:300px">
                <x-label for="water" :value="__('Вода: текущие показания')" />
                <input id="water" class="block mt-1 w-full" style="border-radius:5px;" type="text" name="water"  value="{{ $water }}" required autofocus />

                @if (isset($water_prev) && $water_prev == 0)
                    <x-label for="water_prev" :value="__('Вода: предыдущие показания')" />
                    <input id="water_prev" class="block mt-1 w-full" style="border-radius:5px;" type="text" name="water_prev"  value="{{ $water_prev }}" required />
                @endif

                <x-label for="warm" :value="__('Тепло: текущие показания')" />
                <input id="warm" class="block mt-1 w-full" style="border-radius:5px;" type="text" name="warm" value="{{ $warm }}" />

                @if (isset($warm_prev) && $warm_prev == 0)
                    <x-label for="warm_prev" :value="__('Тепло: предыдущие показания')" />
                    <input id="warm_prev" class="block mt-1 w-full" style="border-radius:5px;" type="text" name="warm_prev" value="{{ $warm_prev }}" />
                @endif
            </div>

            <div class="flex items-center justify-end mt-4">

                <x-button class="ml-3">
                    {{ __('Отправить показания') }}
                </x-button>
            </div>
        </form>
    @else
         <div style="width:350px;margin:0 auto">Показания вводятся с {{ $start_pokaz_period }}-го по {{ $end_pokaz_period }}-е число</div>
    @endif
</x-app-layout>


