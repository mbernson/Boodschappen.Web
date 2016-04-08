@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h3>Producten</h3>
                <form>
                    <input type="search" name="q" placeholder="Zoek een product..." value="{{ Request::has('q') ? Request::get('q') : '' }}" />
                    <input type="checkbox" name="update" id="update" value="1" />
                    <label for="update">Update</label>
                    <input type="submit" value="Zoeken" />
                </form>
                <p>Doorzoek {{ $products_count }} producten in {{ $categories_count }} categorie&euml;n.</p>
                {!! $products->links() !!}
                @include('partials.products_table', compact('products'))
                {!! $products->links() !!}
            </div>
        </div>
    </div>
@endsection
