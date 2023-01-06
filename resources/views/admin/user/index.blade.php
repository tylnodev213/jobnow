@extends('admin.layouts.master')
@section('content')
    <div class="card">
        <div class="card-header">ユーザー一覧</div>
        <div class="card-body">
            <div class="row">
                <div class="col-6 offset-3">
                    {!! Form::open(['url' => getRoute('users.index'), 'method' => 'get']) !!}
                    <div class="form-group row">
                        <label for="email">{{ __('models.users.attributes.name') }}</label>
                        {!! Form::text('name', request()->get('name'), ['class' => 'form-control', 'id' => 'name']) !!}
                    </div>
                    <div class="form-group row">
                        <label for="email">{{ __('models.users.attributes.email') }}</label>
                        {!! Form::text('email', request()->get('email'), ['class' => 'form-control', 'id' => 'email']) !!}
                    </div>
                    <div class="form-group row">
                        <a href="{{ getRoute('users.index') }}" class="btn btn-default">検索条件をクリア</a>
                        <button type="submit" class="btn btn-success ml-3">検索する</button>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-5">
                    @include('admin.elements._paginator')
                </div>
                <div class="col-7 text-right">
                    <a class="btn btn-success" href="{{ backUrl('admin.users.create') }}">ユーザー登録</a>
                    <a class="btn btn-info" href="{{ backUrl('admin.users.downloadCsv', request()->all()) }}">csvをダウンロード</a>
                </div>
            </div>
            <div class="mt-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>@sortablelink('id', ' ID')</th>
                            <th>@sortablelink('name', ' 名前')</th>
                            <th>@sortablelink('email', ' メールアドレス')</th>
                            <th>@sortablelink('created_at', ' 登録日時')</th>
                            <th>@sortablelink('updated_at', ' 更新日時')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($records) && $records->total())
                            @foreach($records as $record)
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td>{{ $record->name }}</td>
                                    <td>{{ $record->email }}</td>
                                    <td>{{ $record->created_at }}</td>
                                    <td>{{ $record->updated_at }}</td>
                                    <td>
                                        <a class="btn btn-info" href="{{ backUrl('admin.users.show', ['id' => $record->id]) }}">表示</a>
                                        <a class="btn btn-success" href="{{ backUrl('admin.users.edit', ['id' => $record->id]) }}">編集</a>
                                        <a class="btn btn-danger btn-submit-delete" data-modal="#modal-confirm-delete" data-url="{{ backUrl('admin.users.destroy', ['id' => $record->id]) }}">削除</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">{{ __('messages.no_data') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div>
                    @include('admin.elements._paging')
                </div>
            </div>
        </div>
    </div>
@endsection

