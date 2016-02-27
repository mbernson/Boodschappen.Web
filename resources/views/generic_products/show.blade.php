@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h2>Product</h2>
                <h1><a href="">{{ $product->title }}</a></h1>

                <h3>Structuur</h3>
                <ul>
                    <li><a href="{{ $product->parent->id }}">{{ $product->parent->title }}</a>
                        <ul>
                            @foreach($product->parent->children as $cat)
                            <li>
                                @if($cat->id == $product->id)
                                    <strong> <a href="/generic_products/{{ $cat->id }}">{{ $cat->title }}</a> </strong>
                                @else
                                     <a href="/generic_products/{{ $cat->id }}">{{ $cat->title }}</a>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>

                <h3>Producten in deze categorie</h3>

                @include('partials.products_table', ['items' => $products->toArray()])

                <div class="well-lg">
                    {!! var_dump($product->toArray()) !!}
                </div>
            </div>
        </div>
    </div>
@endsection
