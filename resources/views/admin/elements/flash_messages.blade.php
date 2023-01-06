@if(session()->has('success'))
    <div class="alert alert-success">
        <p class="text-left">{!! session()->get('success') !!}</p>
    </div>
@endif

@if(session()->has('token_expiration'))
    <div class="alert alert-danger">
        <p class="text-left">{!! session()->get('token_expiration') !!}</p>
    </div>
@endif
