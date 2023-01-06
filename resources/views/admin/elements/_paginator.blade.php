@if(!empty($records) && $records->total())
    @php
        $totalRecord  = $records->total();
        $currentPage = $records->currentPage();
        $perPage = $records->perPage();
        // paging info variables
        $fromRecord = (int)($currentPage - 1) * $perPage + 1;
        if ($fromRecord > $totalRecord) $fromRecord = $totalRecord;
        $toRecord = (($currentPage * $perPage) - $totalRecord) > 0 ? $totalRecord : ($currentPage * $perPage);
    @endphp
    <p>{{$totalRecord}}件中 {{$fromRecord}}〜{{$toRecord}}件目を表示しています</p>
@endif
