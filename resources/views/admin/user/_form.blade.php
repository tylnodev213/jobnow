<div class="card">
    <div class="card-header">{{ __('messages.page_title.admin.users') }}{{ !empty($isEdit) ? __('messages.page_action.edit') : __('messages.page_action.create') }}</div>
    <div class="card-body">
        <div class="row">
            <div class="col-6 offset-3">
                {!! Form::open(['url' => getRoute('users.valid'), 'id' => 'userForm']) !!}
                    {!! Form::hidden('id', data_get($record, 'id')) !!}

                    <div class="form-group">
                        <label for="email">{{ __('models.users.attributes.last_name') }} <span class="text-danger">※</span></label>
                        {!! Form::text('last_name', data_get($record, 'last_name', old('last_name')) , ['class' => 'form-control', 'id' => 'last_name']) !!}
                    </div>

                    <div class="form-group">
                        <label for="email">{{ __('models.users.attributes.first_name') }} <span class="text-danger">※</span></label>
                        {!! Form::text('first_name', data_get($record, 'first_name', old('first_name')) , ['class' => 'form-control', 'id' => 'first_name']) !!}
                    </div>

                    <div class="form-group">
                        <label for="email">{{ __('models.users.attributes.email') }} <span class="text-danger">※</span></label>
                        {!! Form::text('email', data_get($record, 'email', old('email')) , ['class' => 'form-control', 'id' => 'email', 'aria-describedby' => 'emailHelp']) !!}
                    </div>

                    <div class="form-group">
                        <label for="password">{{ __('models.users.attributes.password') }} <span class="text-danger">※</span></label>
                        {!! Form::password('password', ['class' => 'form-control', 'id' => 'password']) !!}
                    </div>

                    <div class="form-group">
                        <label for="password_confirm">{{ __('models.users.attributes.password_confirm') }} <span class="text-danger">※</span></label>
                        {!! Form::password('password_confirm', ['class' => 'form-control', 'id' => 'password_confirm']) !!}
                    </div>

                    <div class="form-group">
                        <label for="avatar">{{ __('models.users.attributes.avatar') }}</label>
                        @php $avatar = !empty($record) ? data_get($record, 'avatar') : null; @endphp
                        {!! Form::upload('avatar', [
                            'class' => 'form-control input-file',
                            'ext' => getConfig('file.default.image.ext'),
                            'size' => getConfig('file.default.image.size'),
                            'accept' => getConfig('file.default.image.accept'),
                            'data-label' => '写真',
                            'show_error_input' => 1,
                            'show_remove_type' => 'button',
                            'file-uploaded' => $avatar,
                            'preview-image-class' => 'w-50',
                            'default-image' => public_url(getConfig('no_avatar')),
                            'preview-url' => $avatar ? baseStorageUrl($avatar) : public_url(getConfig('no_avatar')),
                        ]) !!}
                    </div>

                    <div class="form-group">
                        <a class="btn btn-default" href="{{ getBackUrl() }}">Back</a>
                        <button type="button"
                                class="btn btn-success btn-submit-form"
                                data-modal="#modal-confirm-{{ !empty($isEdit) ? 'update' : 'create' }}"
                                data-url="{{ !empty($isEdit) ? getRoute('users.update', ['id' => data_get($record, 'id')]) : getRoute('users.store') }}">Submit</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
