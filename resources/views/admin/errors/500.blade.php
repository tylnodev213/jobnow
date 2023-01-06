@extends('admin.layouts.error')
@section('content')
    <div class="card">
        <div class="card-header"></div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <!-- flash message -->
                        <div>{{ __('messages.system_error') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
