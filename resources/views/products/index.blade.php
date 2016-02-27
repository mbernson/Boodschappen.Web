@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h3>Producten</h3>
                <form>
                    <input type="search" name="q" placeholder="Zoek een product..." />
                    <input type="submit" value="Zoeken" />
                </form>
                <p>Totaal: {{ $count }} producten</p>
                {!! $products->links() !!}
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
                {!! $products->links() !!}
            </div>
        </div>
    </div>
@endsection
