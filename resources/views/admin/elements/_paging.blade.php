@if(!empty($records) && $records->total())
    <?php $queryString = app('request')->all(); ?>
    <ul class="pagination justify-content-end">
        @if(count($records) > 0 && $records->total() > $records->perPage())
            @if($records->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link"><< 前へ</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{$records->appends($queryString)->url($records->currentPage() - 1)}}"><<&nbsp;前へ</a>
                </li>
            @endif

            @if($records->currentPage() <= 5)
                @if($records->lastPage() >= 9)
                    @for($i = 1; $i <= 9; $i++)
                        <li class="page-item {{ ($records->currentPage() == $i) ? 'active' : '' }}">
                            <a class="page-link" href="{{ $records->appends($queryString)->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                @else
                    @for($i = 1; $i <= $records->lastPage(); $i++)
                        <li class="page-item {{ ($records->currentPage() == $i) ? 'active' : '' }}">
                            <a class="page-link" href="{{ $records->appends($queryString)->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                @endif
            @elseif($records->currentPage() >= $records->lastPage() - 4)
                @for($i = $records->lastPage() - 8; $i <= $records->lastPage(); $i++)
                    @php if ($i <= 0) continue; @endphp
                    <li class="page-item {{ ($records->currentPage() == $i) ? 'active' : '' }}">
                        <a class="page-link" href="{{ $records->appends($queryString)->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor
            @else
                @for($i = $records->currentPage() - 4; $i <= $records->currentPage() + 4; $i++)
                    <li class="page-item {{ ($records->currentPage() == $i) ? 'active' : '' }}">
                        <a class="page-link" href="{{ $records->appends($queryString)->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor
            @endif

            @if($records->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $records->nextPageUrl() }}">次へ&nbsp;>></a>
                </li>
            @else
                <li class="page-item {{ $records->currentPage() == $records->lastPage() ? 'disabled' : '' }}">
                    <span class="page-link">次へ >></span>
                </li>
            @endif
        @endif
    </ul>
@endif
