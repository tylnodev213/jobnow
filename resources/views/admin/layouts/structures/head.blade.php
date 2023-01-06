<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ADMIN{{ !empty($title) ? ' | ' . $title : '' }}</title>
<meta name="description" content="">
<link rel="icon" type="image/png" sizes="16x16" href="{{ public_url('favicon.ico') }}">
<meta name="csrf-token" content="{{ csrf_token() }}" />

{!! loadFiles([
    'vendor/bootstrap.min',
    'vendor/bootstrap-theme.min',
    'vendor/bootstrap-datepicker.min',
    'vendor/font-awesome/font-awesome.min',
    'vendor/md-preloader.min',
    'vendor/toastr.min',
    'vendor/sweetalert2',
    'common',
]) !!}

{!! loadFiles(['style'], 'admin') !!}

{!! loadFiles(['vendor/jquery.min'], '', 'js') !!}

@stack('styles')
