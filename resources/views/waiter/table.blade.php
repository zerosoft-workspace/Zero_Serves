@extends('layouts.waiter')

@section('title', $table->name)

@section('content')
    <h2>{{ $table->name }}</h2>
    <p>Durum: <x-status-badge :status="$table->status" /></p>

    @if($order)
        <div class="card mb-3">
            <div class="card-header">
                Sipariş #{{ $order->id }} –
                <x-status-badge :status="$order->status" />
            </div>
            <div class="card-body">
                <ul class="list-group mb-3">
                    @foreach($order->items as $item)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $item->product->name }} (x{{ $item->quantity }})</span>
                            <span>{{ number_format($item->line_total, 2) }} ₺</span>
                        </li>
                    @endforeach
                </ul>
                <div class="fw-bold mb-3">Toplam: {{ number_format($order->total_price, 2) }} ₺</div>

                <form method="POST" action="{{ route('waiter.order.status', $order->id) }}">
                    @csrf
                    <div class="btn-group">
                        <button name="status" value="preparing" class="btn btn-warning">Mutfağa Gönder</button>
                        <button name="status" value="delivered" class="btn btn-success">Teslim Edildi</button>
                        <button name="status" value="paid" class="btn btn-primary">Ödendi</button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <p class="text-muted">Bu masada sipariş yok.</p>
    @endif

    <a href="{{ route('waiter.index') }}" class="btn btn-outline-secondary">← Masalara Dön</a>
@endsection