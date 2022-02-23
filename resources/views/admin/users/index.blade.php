@extends('admin.layout')

@section('title') Пользователи @endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">@yield('title')</h3>

                <div class="card-tools">
                    {{ $users->links() }}
                </div>

            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Имя</th>
                            <th>Квартира</th>
                            <th>Емейл</th>
                            <th>Роль</th>
                            <th>Управление</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->flat }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ $user->is_admin ? 'badge-success' : 'badge-danger'}}">Admin</span>
                                <span class="badge {{ $user->is_manager ? 'badge-success' : 'badge-danger'}}">Manager</span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('user.edit', ['user' => $user]) }}" class="btn btn-warning">Edit</a>
                                    <a href="{{ route('user.delete', ['user' => $user]) }}" class="btn btn-danger">Delete</a>

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
@endsection
