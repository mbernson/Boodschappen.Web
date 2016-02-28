@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h2>Product</h2>
                <h1><a href="">{{ $product->title }}</a></h1>

                {!! $product->renderImage() !!}

                <table class="table table-striped">
                    <tr>
                        <th>Product categorie</th>
                        <td><a href="/categories/{{ $product->category->id }}">{{ $product->category->title }}</a></td>
                    </tr>
                    <tr>
                        <th>Hoeveelheid</th>
                        <td>{{ $product->amount }}</td>
                    </tr>
                    <tr>
                        <th>SKU</th>
                        <td>{{ $product->sku }}</td>
                    </tr>
                    <!--
                    <tr>
                        <th>Toegevoegd op</th>
                        <td>{{ $product->created_at }}</td>
                    </tr>
                    <tr>
                        <th>Bijgewerkt op</th>
                        <td>{{ $product->updated_at }}</td>
                    </tr>
                    -->
                </table>

                <h3>Prijs historie</h3>
                @include('partials.price_history_table', compact('prices', 'currencyFormatter'))

                <h3>Vergelijkbare producten</h3>
                @include('partials.products_table', ['products' => $related])

                @if(!empty($product->extended_attributes))
                    <h3>Extra product attributen</h3>
                    @include('partials.table', ['items' => [$product->extended_attributes]])
                @endif

                <h3>Rauwe productdata</h3>
                <div class="well-lg">
                    {!! var_dump($product->toArray()) !!}
                </div>
            </div>
        </div>
    </div>
@endsection
