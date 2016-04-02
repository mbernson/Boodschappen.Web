@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h2>List</h2>
                <h1><a href="">{{ $shopping_list->title }}</a></h1>

                @include('partials.products_table', $shopping_list->products)
            </div>
        </div>
    </div>
@endsection
