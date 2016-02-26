@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h3>{{ $count }} producten</h3>
                {!! $products->links() !!}
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
                                @if($k == 'id')
                                <td><a href="/products/{{ $v }}">{{ $v }}</a></td>
                                @elseif(is_string($v))
                                <td>{{ $v }}</td>
                                @else
                                <td>{!! var_dump($v) !!}</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {!! $products->links() !!}
            </div>
        </div>
    </div>
@endsection
