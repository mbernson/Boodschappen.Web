@if(empty($prices))
    <div class="well text-center no-results">
        <h1>Geen resultaten</h1>
        <p>Er zijn geen resultaten gevonden die voldoen aan de gegeven criteria.</p>
    </div>
@else
<table class="table table-striped">
    <thead>
    <tr>
        <th>Verkoper</th>
        <th>Prijs</th>
        <th>Waargenomen op</th>
    </tr>
    </thead>
    <tbody>
    @foreach($prices as $price)
    <tr>
        <td>{{ $price->title }}</td>
        <td>{{ $currencyFormatter->formatCurrency($price->price, "EUR") }}</td>
        <td>{{ $price->created_at }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
@endif