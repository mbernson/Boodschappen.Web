@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h1><a href="">{{ $category->title }}</a></h1>

                <h3>Structuur</h3>
                <ul>
                    <li><a href="{{ $category->parent->id }}">{{ $category->parent->title }}</a>
                        <ul>
                            @foreach($category->parent->children as $cat)
                            <li>
                                @if($cat->id == $category->id)
                                    <strong>{{ $cat->title }}</strong>
                                @else
                                     <a href="/categories/{{ $cat->id }}">{{ $cat->title }}</a>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>

                <h3>Producten in deze categorie</h3>

                @include('partials.products_table', compact('products', 'currencyFormatter'))

                <div class="well-lg">
                    {!! var_dump($category->toArray()) !!}
                </div>
            </div>
        </div>
    </div>
@endsection
