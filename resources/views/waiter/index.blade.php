@extends('layouts.waiter')

@section('title', 'Masalar')

@section('content')
    <h2 class="mb-4">Masalar</h2>
    <div class="row">
        @foreach($tables as $t)
            <div class="col-md-3 mb-3">
                <a href="{{ route('waiter.table', $t->id) }}" class="text-decoration-none">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $t->name }}</h5>
                            <p>{!! view('components.status-badge', ['status' => $t->status]) !!}</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection