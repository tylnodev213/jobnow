<div class="modal" id="modal-confirm-create" tabindex="-1" role="dialog">
    {!! Form::open(['url' => '', 'method' => 'POST']) !!}
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-center">登録します。よろしいですか？</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-submit-modal">はい</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</div>

<div class="modal" id="modal-confirm-update" tabindex="-1" role="dialog">
    {!! Form::open(['url' => '', 'method' => 'POST']) !!}
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-center">更新します。よろしいですか？</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-submit-modal">はい</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</div>


<div class="modal" id="modal-confirm-delete" tabindex="-1" role="dialog">
    {!! Form::open(['url' => '', 'method' => 'POST']) !!}
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-center">削除します。よろしいですか？</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-submit-modal">はい</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</div>
