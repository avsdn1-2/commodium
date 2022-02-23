@extends('admin.layout')

@section('title') Редактирование пользователя @endsection

@section('content')
    @include('admin.users.form', compact('user'))
@endsection
