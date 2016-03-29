@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Prijzen</th>
                            <th>Verschil</th>
                            <th>Verandering (breuk)</th>
                            <th>Bijgewerkt op</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($changes as $product)
                        <tr>
			<td><a href="/products/{{ $product->id }}">{{ $product->title }}</a></td>
			<td> {{ $product->prices }} </td>
                <td>&euro;{{ $currencyFormatter->formatCurrency($product->difference, "EUR") }}</td>
			<td> {{ $product->change }} </td>
			<td>{{ $product->last_updated }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
