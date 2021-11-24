



<x-app-layout>
    <x-slot name="header">
        <div style="width:300px;margin:0 auto">Введите допустимый емейл</div>
    </x-slot>
    <form method="POST" style="width:300px;margin:0 auto" action="{{ route('email.store') }}">
    @csrf
        <div style="width:300px">

            <x-label for="title" :value="__('Email')" />

            <x-input id="title" class="block mt-1 w-full" type="text" name="email" required />
        </div>

        <div class="flex items-center justify-end mt-4">

            <x-button class="ml-3">
                {{ __('Сохранить') }}
            </x-button>
        </div>
    </form>
</x-app-layout>


