@extends('layouts.waiter')

@section('title', 'Masalar')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="mb-0">Masalar</h2>
    </div>

    @php
        // Sipariş statülerinin Türkçe karşılıkları
        $statusMap = [
            'pending' => 'Sipariş Bekliyor',
            'preparing' => 'Hazırlanıyor',
            'delivered' => 'Teslim Edildi',
            'paid' => 'Boş',
        ];
    @endphp

    @if(isset($tables) && count($tables))
        <div class="row g-3">
            @foreach($tables as $table)
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card shadow-sm card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <h5 class="card-title mb-0">{{ $table->name }}</h5>
                                <a href="{{ url('/waiter/table/' . $table->id) }}" class="btn btn-sm btn-outline-primary">
                                    Detaya Git
                                </a>
                            </div>

                            {{-- Sipariş durumu rozetleri --}}
                            @if($table->active_order)
                                @php $orderStatus = $table->active_order->status; @endphp
                                @if($orderStatus === 'pending')
                                    <span class="badge bg-danger">{{ $statusMap[$orderStatus] }}</span>
                                @elseif($orderStatus === 'preparing')
                                    <span class="badge bg-warning text-dark">{{ $statusMap[$orderStatus] }}</span>
                                @elseif($orderStatus === 'delivered')
                                    <span class="badge bg-success">{{ $statusMap[$orderStatus] }}</span>
                                @elseif($orderStatus === 'paid')
                                    <span class="badge bg-secondary">{{ $statusMap[$orderStatus] }}</span>
                                @else
                                    <span class="badge bg-dark">{{ strtoupper($orderStatus) }}</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">Boş</span>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">Kayıtlı masa bulunamadı.</div>
    @endif
@endsection