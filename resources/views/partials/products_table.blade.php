@if(empty($products))
    <div class="well text-center no-results">
        <h1>Geen resultaten</h1>
        <p>Er zijn geen resultaten gevonden die voldoen aan de gegeven criteria.</p>
    </div>
@else
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Naam</th>
            <th>Merk</th>
            <th>Hoeveelheid</th>
            <th>Prijs</th>
            <th>Supermarkt</th>
        </tr>
        </thead>
        <tbody>
        @foreach($products as $product)
            <tr>
                <td><a href="/products/{{ $product->id }}">{{ $product->title }}</a></td>
                <td>{{ $product->brand }}</td>
                <td>{{ $product->amount }}</td>
                <td>{{ $currencyFormatter->formatCurrency($product->price, "EUR") }}</td>
                <td>{{ companyName($product->company_id) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif