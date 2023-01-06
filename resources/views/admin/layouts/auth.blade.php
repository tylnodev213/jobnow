<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.layouts.structures.head')
</head>
<body class="{{ getBodyClass() }} auth-layout">
<div id="wrapper" class="wrapper">
    <div id="page-wrapper" class="wrap-content">
        <div class="content-auth">
            <div class="wrap-auth">
                @yield('content')
            </div>
        </div>
    </div>
    @include('admin.layouts.structures.footer')
</div>
@stack('scripts')
</body>
</html>
