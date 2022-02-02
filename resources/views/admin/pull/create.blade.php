<x-app-layout>

       <div style="width:300px;margin:50px auto 0 auto">Занесение квартиры в пулл для расчета стоимости потребленного тепла</div>
        @if (isset($message) && $message == '')
        <div style="width:300px;border:1px solid green;border-radius:5px;color:green;margin:0 auto"><p style="margin:0 0 0 10px">Квартира {{{ $flat  }}} <br>успешно добавлена в пулл</p></div>
        @endif

        <form method="POST" style="width:300px;margin:0 auto" action="{{ route('pull.store') }}">
            @csrf
            <div style="width:300px">
                <x-auth-validation-errors class="mb-4" :errors="$errors" />
                <div style="clear:both"></div>
            </div>
            <div>
                <x-label for="flat" :value="__('Номер квартиры')" />
                <input id="flat" class="block mt-1 w-full" style="border-radius:5px;" type="text" name="flat"  value="" required autofocus />

            </div>

            <div class="flex items-center justify-end mt-4">

                <x-button class="ml-3">
                    {{ __('Сохранить квартиру в пулл') }}
                </x-button>
            </div>
        </form>

</x-app-layout>


