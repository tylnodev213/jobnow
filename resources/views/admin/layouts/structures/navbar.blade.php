<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Laravel</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item {{ getControllerName() == 'home' ? 'active' : '' }}">
                <a class="nav-link" href="{{ getRoute('home') }}">ホーム <span class="sr-only"></span></a>
            </li>

            <li class="nav-item {{ getControllerName() == 'user' ? 'active' : '' }}">
                <a class="nav-link" href="{{ getRoute('users.index') }}">ユーザー管理 <span class="sr-only"></span></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                    {{ getGuard()->user()->email ?? '' }}
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    {!! Form::open(['url' => getRoute('logout'), 'method' => 'post', 'id' => 'formLogout']) !!}
                    <a class="dropdown-item" href="javascript:void(0);" onclick="$('#formLogout').submit();return false;">ログアウト</a>
                    {!! Form::close() !!}
                </div>
            </li>
        </ul>
    </div>
</nav>
