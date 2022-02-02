<!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="{{ route('home') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Домашняя страница</p>
            </a>
        </li>
        <h2 style="color:white">Функции менеджера</h2>
        @if (auth()->user()->is_manager || auth()->user()->is_admin)
        <li class="nav-item">
            <a href="{{ route('pokaz.adminCreate') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Занести показания<br> за квартиру</p>
            </a>
        </li>
            <li class="nav-item">
                <a href="{{ route('admin.create') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Сформировать квитанцию<br> за обслуживание за квартиру</p>
                </a>
            </li>
        <li class="nav-item">
            <a href="{{ route('admin.warm') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Квитанции по отоплению</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('counter.create') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Занести показания<br> общедомового счетчика</p>
            </a>
        </li>
        @endif


        @if (auth()->user()->is_admin)
            <p>Функции админа</p>
            <li class="nav-item">
                <a href="{{ route('email.create') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Добавить Email</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('flat.create') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Добавить квартиру</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tarif.edit') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Изменить тариф</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('pull.create') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Добавить квартиру в пулл</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('pull.delete') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Удалить квартиру из пулла</p>
                </a>
            </li>


        @endif
{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('admin.orders.index') }}" class="nav-link">--}}
{{--                <i class="far fa-circle nav-icon"></i>--}}
{{--                <p>Orders</p>--}}
{{--            </a>--}}
{{--        </li>--}}
    </ul>
</nav>
<!-- /.sidebar-menu -->
