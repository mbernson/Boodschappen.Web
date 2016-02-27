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
                @include('partials.products_table', compact('products'))
                {!! $products->links() !!}
            </div>
        </div>
    </div>
@endsection
