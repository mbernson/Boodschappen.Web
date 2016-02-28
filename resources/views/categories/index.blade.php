@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h3>{{ $count }} categorieën</h3>
                @include('partials.recursive_list', compact('categories'))
            </div>
        </div>
    </div>
@endsection
