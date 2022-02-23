@extends('admin.layout')

@section('title') Квартиры @endsection

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">@yield('title')</h3>

                <div class="card-tools">
                    {{ $flats->links() }}
                </div>

            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Номер</th>
                            <th>Площадь общая</th>
                            <th>Площадь отопит.</th>
                            <th>Жильцы</th>
                            <th>Тип счетчика тепла</th>
                            <th>Использ. лифт?</th>
                            <th>Фамилия</th>
                            <th>Имя</th>
                            <th>Отчество</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($flats as $flat)
                        <tr>
                            <td>{{ $flat->number }}</td>
                            <td>{{ $flat->square_total }}</td>
                            <td>{{ $flat->square_warm }}</td>
                            <td>{{ $flat->residents }}</td>
                            <td>{{ App\Models\Pokaz::getUnits($flat->counterType) }}</td>
                            <td>{{ App\Models\Flat::liftUsage[$flat->useLift] }}</td>
                            <td>{{ $flat->name }}</td>
                            <td>{{ $flat->first_name }}</td>
                            <td>{{ $flat->mid_name }}</td>

                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('flat.edit', ['flat' => $flat]) }}" class="btn btn-warning">Edit</a>
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
