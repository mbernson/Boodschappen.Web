<table class="table table-striped">
    <thead>
    <tr>
        @foreach($items[0] as $k => $v)
            <th>{{ $k }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            @foreach($item as $k => $v)
                <td>{{ $v }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>