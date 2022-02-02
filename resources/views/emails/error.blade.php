@component('mail::message')


<h3>Ошибка в приложении Commodium</h3>
<div>Пользователь: {{ $user }}</div>
<div>Файл: {{ $file }}</div>
<div>Строка: {{ $line }}</div>
<div>Ошибка: {{ $error }}</div>

@component('mail::button', ['url' => ''])
<!--Button Text-->
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
