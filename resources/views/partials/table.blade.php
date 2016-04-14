@if(empty($items))
    <div class="well text-center no-results">
        <h1>Geen resultaten</h1>
        <p>Er zijn geen resultaten gevonden die voldoen aan de gegeven criteria.</p>
    </div>
@else
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
                @if(is_string($v) || $k == 'id')
                <td>{{ $v }}</td>
                @elseif(is_int($v) || is_float($v))
                <td>{{ app('\NumberFormatter')->format($v) }}</td>
                @elseif(is_array($v))
                <td><code>ongestructureerde data</code></td>
                @elseif($k == 'company_id')
                <td>{{ companyName($v) }}</td>
                @else
                <td>{{ var_dump($v) }}</td>
                @endif
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
@endif
