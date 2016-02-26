@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h2>Product</h2>
                <h1><a href="">{{ $product->title }}</a></h1>
                {!! $product->renderImage() !!}
                <div class="well-lg">
                    {!! var_dump($product->toArray()) !!}
                </div>
                <h3>Prijs historie</h3>
                @include('partials.table', ['items' => $prices->toArray()])
                <h3>Attributen</h3>
                <div class="well-lg">
                    {!! var_dump($product->extended_attributes) !!}
                </div>
            </div>
        </div>
    </div>
@endsection
