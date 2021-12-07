<!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="{{ route('home') }}" class="nav-link"> <!--  route('admin.users.index')  -->
                <i class="far fa-circle nav-icon"></i>
                <p>Домашняя страница</p>
            </a>
        </li>
        <p>Функции менеджера</p>
        @if (auth()->user()->is_manager || auth()->user()->is_admin)
        <li class="nav-item">
            <a href="{{ route('pokaz.adminCreate') }}" class="nav-link"> <!--  route('admin.users.index')  -->
                <i class="far fa-circle nav-icon"></i>
                <p>Занести показания<br> за квартиру</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('counter.create') }}" class="nav-link">  <!--  route('admin.products.index')  -->
                <i class="far fa-circle nav-icon"></i>
                <p>Занести показания<br> общедомового счетчика</p>
            </a>
        </li>
        @endif

        <p>Функции админа</p>
        @if (auth()->user()->is_admin)
            <li class="nav-item">
                <a href="{{ route('email.create') }}" class="nav-link"> <!--  route('admin.users.index')  -->
                    <i class="far fa-circle nav-icon"></i>
                    <p>Добавить Email</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('flat.create') }}" class="nav-link">  <!--  route('admin.products.index')  -->
                    <i class="far fa-circle nav-icon"></i>
                    <p>Добавить квартиру</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tarif.edit') }}" class="nav-link">  <!--  route('admin.products.index')  -->
                    <i class="far fa-circle nav-icon"></i>
                    <p>Изменить тариф</p>
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
