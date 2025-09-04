<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_REFUNDED = 'refunded';

    public const ALL_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PREPARING,
        self::STATUS_DELIVERED,
        self::STATUS_PAID,
        self::STATUS_CANCELED,
        self::STATUS_REFUNDED,
    ];
    protected $fillable = ['table_id', 'customer_name', 'status', 'payment_status', 'total_amount'];

    protected static array $TRANSITIONS = [
        'waiter' => [
            self::STATUS_PENDING => [self::STATUS_PREPARING],
            self::STATUS_PREPARING => [self::STATUS_DELIVERED],
            self::STATUS_DELIVERED => [self::STATUS_PAID], // YENİ: Garson Paid yapabilir
        ],
        'admin' => [
            self::STATUS_DELIVERED => [
                self::STATUS_PAID,
                self::STATUS_CANCELED,
                self::STATUS_REFUNDED,
            ],
        ],
    ];

    public function canTransitionTo(string $to, string $role): bool
    {
        $from = (string) $this->status;
        $map = static::$TRANSITIONS[$role] ?? [];

        return in_array($to, $map[$from] ?? [], true);
    }
    public function table(): BelongsTo
    {
        // Model adı sizde büyük ihtimalle App\Models\Table
        return $this->belongsTo(Table::class, 'table_id', 'id');
        // Eğer model adı farklıysa (ör. RestaurantTable), sınıfı ona göre değiştirin:
        // return $this->belongsTo(RestaurantTable::class, 'table_id', 'id');
    }
    public function items(): HasMany
    {
        // order_items.order_id → orders.id
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
    public function orderItems(): HasMany
    {
        return $this->items(); // aynı ilişkiye alias
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id', 'id');
    }
}
