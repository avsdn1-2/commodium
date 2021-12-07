<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/favico.js-slevomat@0.3.11/favico.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js" integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E=" crossorigin="anonymous"></script>

    <!-- Styles -->
    <style>
        /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}}
    </style>

    <style>
        body {
            font-family: 'Nunito';
        }


    </style>
</head>
<body class="">
<div class="p-3 px-md-4 mb-3 bg-dark text-white align-items-center"> <!-- d-flex flex-column flex-md-row align-items-center -->


    <nav x-data="{ open: false }" class="bg-dark border-gray-100" style="/*width:100%*/">



        <!-- Primary Navigation Menu -->
        <div class="flex_width max-w-7xl  px-4 sm:px-6 lg:px-8" style="display:block;margin:0 auto;/*width:1200px;border:1px solid green*/"> <!--mx-auto-->
            <div class="" style="/*width:100%*/">
                <div class="flex_height" style="height:85px;/*width:100%;*/">



                    <div class="" style="width:190px;float:left;/*border:1px solid red*/">
                        <a href="route('home')" class="font-20 height-30 text-white align-items-center text-decoration-none">
                            {{ __('ОСМД Коммодиум') }}
                        </a>
                    </div>

                    @auth
                        <div class="" style="width:420px;position:relative;top:4px;float:left;text-align:center;/*border:1px solid red*/">
                            <a href="/list" :active="request()->routeIs('pokaz.list')" class="height-30 underline">
                                {{ __('История показаний') }}
                            </a>

                            <a href="/create" :active="request()->routeIs('pokaz.create')" class="height-30 underline" style="margin:0 0 0 20px">
                                {{ __('Внести показания') }}
                            </a>

                            <a href="/calc" :active="request()->routeIs('pokaz.calc')" class="height-30 underline" style="margin-left:15px">
                                {{ __('Квитанции') }}
                            </a>
                        </div>
                        <div class="" style="width:350px;position:relative;top:4px;float:left;text-align:center;/*border:1px solid red*/">
                            @if (Auth::user()->is_manager || Auth::user()->is_admin)
                                <a id="admin" href="/admin-panel" :active="request()->routeIs('admin-panel/')" class="height-30 underline">
                                    {{ __('Админ-панель') }}
                                </a>
                            <!--
                                <a id="tarif" href="/editTarif" :active="request()->routeIs('tarif.edit')" class="height-30 underline">
                                    {{ __('Тарифы') }}
                                </a>
                                <a id="email" href="/createEmail" :active="request()->routeIs('email.create')" class="height-30 underline" style="margin:0 0 0 30px;">
                                    {{ __('Емейлы') }}
                                </a>
                                <a id="flat" href="/createFlat" :active="request()->routeIs('flat.create')" class="height-30 underline" style="margin:0 0 0 30px;">
                                    {{ __('Квартиры') }}
                                </a>
                                -->
                                <div style="clear:both"></div>
                            @endif
                            @if (in_array(Auth::user()->role,['admin','superadmin']))
                                 <a id="adminPokaz" href="/adminCreate" :active="request()->routeIs('pokaz.adminCreate')" class="height-30 underline" style="margin:0 0 0 30px;">
                                    {{ __('Внести показания как Админ') }}
                                 </a>

                                <a id="counter" href="/createCounter" :active="request()->routeIs('counter.create')" class="height-30 underline" style="margin:0 0 0 30px;">
                                    {{ __('Внести показания общедомового счетчика тепла') }}
                                </a>

                            @endif
                        </div>
                    @endauth
                    <div class="" style="width:150px;position:relative;top:4px;float:right">
                    @auth
                        <!--<div class="height-30" style="width:40%;float:left">{{ Auth::user()->name }}</div>-->
                            <div style="width:100%;height:30px;text-align:center;/*border:1px solid yellow*/">
                                <div id="nam" :class="{ slide_down: request()->routeIs('tarif.edit') }" style="width:45%;float:left;/*border:1px solid green;*/">
                                    <nav-link style=""href="" class="height-30">{{ Auth::user()->name }}</nav-link>
                                </div>
                                @if (Route::has('logout'))
                                    <div style="width:45%;float:right;/*border:1px solid magenta;*/">
                                        <a href="/logout" class="height-30 underline">Logout</a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div style="width:100%;text-align:center;/*border:1px solid yellow*/">
                                @if (Route::has('login'))
                                    <a href="/login" class="height-30 underline">Вход</a>
                                @endif

                                @if (Route::has('register'))
                                    <a href="/register" class="height-30 underline">Регистрация</a>
                                @endif
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

    </nav>

</div>

@yield('main_content')

</body>
</html>

<style>
    .height-30
    {
        height:32px;
    }
    .border-1
    {
        border:1px solid green;
    }
    .font-20
    {
        font-size:20px;
    }
    .slide_down {
        position:relative;
        top:4px;
    }


    @media all and (max-width: 1200px) {

        .flex_width {
            width: 80%;
            border:1px solid green;
            /*display: inline-block;*/
            /* vertical-align: middle;*/
        }
        .flex_height {
            height:70px;
        }

    }
    @media all and (max-width: 480px) {

        .flex_width {
            width: 100%;
            border:1px solid green;
            /*display: inline-block;*/

        }
        .flex_height {
            height:100px;
        }

    }
</style>


<script>
    /*
    $(function(){

    });
    */
    //alert (screen.width);
</script>

