



<x-app-layout>
    <x-slot name="header">
        <div>Generate PDF Laravel 8 - phpcodingstuff.com</div>
    </x-slot>
    <div class="container">


        <div class="col-md-8 section offset-md-2">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h2>Laravel 8 Generate PDF - phpcodingstuff.com</h2>
                </div>
                <div class="panel-body">
                    <div class="main-div">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                        consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                        proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </div>
                </div>
                <div class="text-center pdf-btn">
                    <a href="{{ route('pdf.generate') }}" class="btn btn-primary">Generate PDF</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style type="text/css">
    h2{
        text-align: center;
        font-size:22px;
        margin-bottom:50px;
    }
    body{
        background:#f2f2f2;
    }
    .section{
        margin-top:30px;
        padding:50px;
        background:#fff;
    }
    .pdf-btn{
        margin-top:30px;
    }
</style>


