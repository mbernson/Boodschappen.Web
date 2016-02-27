@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h2>Product</h2>
                <h1><a href="">{{ $product->title }}</a></h1>
                <div class="well-lg">
                    {!! var_dump($product->toArray()) !!}
                </div>

                <h3>Producten in deze categorie</h3>

                @include('partials.products_table', ['items' => $products->toArray()])
            </div>
        </div>
    </div>
@endsection
