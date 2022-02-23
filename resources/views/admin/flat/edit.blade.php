@extends('admin.layout')

@section('title') Редактирование квартиры @endsection

@section('content')
    @include('admin.flat.form', compact('flat'))
@endsection
