<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">@yield('title')</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            @if($errors->count() > 0)
                <p>The following errors have occurred:</p>
                <ul>
                    @foreach($errors->all() as $message)
                        <li>{{$message}}</li>
                    @endforeach
                </ul>
            @endif

            @if (isset($flat))
                {{ Form::model($flat, ['route' => ['flat.update', $flat->id], 'method' => 'put']) }}
            @else
                {{ Form::open(['route' => ['flat.store'], 'method' => 'post']) }}
            @endif
            <div class="form-group">
                {{ Form::label('number', 'Номер') }}
                {{ Form::text('number', isset($flat)? $flat->number: null, ['class' => 'form-control', 'placeholder' => 'Номер']) }}
            </div>
            <div class="form-group">
                {{ Form::label('square_total', 'Площадь общая') }}
                {{ Form::text('square_total', isset($flat)? $flat->square_total: null, ['class' => 'form-control', 'placeholder' => 'Площадь общая']) }}
            </div>
            <div class="form-group">
                {{ Form::label('square_warm', 'Площадь отопительная') }}
                {{ Form::text('square_warm', isset($flat)? $flat->square_warm: null, ['class' => 'form-control', 'placeholder' => 'Площадь отопительная']) }}
            </div>
            <div class="form-group">
                {{ Form::label('residents', 'Жильцы') }}
                {{ Form::text('residents', isset($flat)? $flat->residents: null, ['class' => 'form-control', 'placeholder' => 'Жильцы']) }}
            </div>
            <div class="form-group">
                {{ Form::label('counterType', 'Тип счетчика тепла') }}
                {{ Form::select('counterType',['1' => 'киловатты', '2' => 'мегаватты','3' => 'гигакаллории','4' => 'гигаджоули'],isset($flat)? $flat->counterType: '1') }}
            </div>
            <div class="form-group">
                {{ Form::label('useLift', 'Использует лифт') }}
                {{ Form::checkbox('useLift', true, isset($flat)? $flat->useLift: null) }}
            </div>
            <div class="form-group">
                {{ Form::label('name', 'Фамилия') }}
                {{ Form::text('name', isset($flat)? $flat->name: '', ['class' => 'form-control', 'placeholder' => 'Фамилия']) }}
            </div>
            <div class="form-group">
                {{ Form::label('first_name', 'Имя') }}
                {{ Form::text('first_name', isset($flat)? $flat->first_name: '', ['class' => 'form-control', 'placeholder' => 'Имя']) }}
            </div>
            <div class="form-group">
                {{ Form::label('mid_name', 'Отчество') }}
                {{ Form::text('mid_name', isset($flat)? $flat->mid_name: '', ['class' => 'form-control', 'placeholder' => 'Отчество']) }}
            </div>


        <!-- /.card-body -->
            <div class="card-footer">
                {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
            </div>
            {{ Form::close() }}
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
