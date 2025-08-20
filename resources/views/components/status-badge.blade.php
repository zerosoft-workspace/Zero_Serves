@props(['status'])

@php
    $map = [
        'empty' => ['text' => 'Boş', 'class' => 'secondary'],
        'order_pending' => ['text' => 'Sipariş Bekliyor', 'class' => 'danger'],
        'preparing' => ['text' => 'Hazırlanıyor', 'class' => 'warning text-dark'],
        'delivered' => ['text' => 'Teslim Edildi', 'class' => 'success'],
        'paid' => ['text' => 'Ödendi', 'class' => 'info text-dark'],
    ];
@endphp

<span class="badge bg-{{ $map[$status]['class'] ?? 'secondary' }}">
    {{ $map[$status]['text'] ?? ucfirst($status) }}
</span>