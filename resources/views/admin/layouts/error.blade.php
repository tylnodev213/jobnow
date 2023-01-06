<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.layouts.structures.head')
</head>
<body class="{{ getBodyClass() }}">
<div id="wrapper" class="wrapper">
    <div id="page-wrapper" class="wrap-content admin main">
        @include('admin.layouts.structures.navbar')
        <div class="container">
            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>
    @include('admin.layouts.structures.footer')
</div>
@stack('scripts')
</body>
</html>
