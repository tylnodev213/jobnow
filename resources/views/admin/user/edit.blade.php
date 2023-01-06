@extends('admin.layouts.master')
@section('content')
    @include('admin.user._form', ['isEdit' => true])
@endsection

