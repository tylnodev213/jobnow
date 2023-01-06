@extends('admin.layouts.master')
@section('content')
    <div class="card">
        <div class="card-header">ユーザー確認</div>
        <div class="card-body">
            <div class="row">
                <div class="col-6 offset-3">
                    <div>
                        {{ __('models.users.attributes.email') }}: {{ data_get($record, 'email') }}
                    </div>
                    <div class="mt-4">
                        {{ __('models.users.attributes.password') }}: ********
                    </div>
                    <div class="mt-4">
                        {{ __('models.users.attributes.name') }}: {{ data_get($record, 'last_name') . ' ' . data_get($record, 'first_name') }}
                    </div>
                    <div class="mt-4">
                        <div>写真:</div>
                        <div>
                            @php
                                $avatar = !empty($record) ? data_get($record, 'avatar') : null;
                                $avatar = !empty($avatar) ? baseStorageUrl($avatar) : public_url('assets/css/admin/img/image_default.png');
                            @endphp
                            {!! Html::image($avatar, '', ['width' => 200]) !!}
                        </div>
                    </div>
                    <div class="mt-4">
                        <a class="btn btn-default" href="{{ getBackUrl() }}">Back</a>
                        <a class="btn btn-success" href="{{ backUrl('admin.users.edit', ['id' => $record['id']]) }}">編集</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

