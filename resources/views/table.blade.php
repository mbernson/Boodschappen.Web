@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            @foreach($items[0] as $k => $v)
                            <th>{{ $k }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        <tr>
                            @foreach($item as $k => $v)
                            <td>{{ $v }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
