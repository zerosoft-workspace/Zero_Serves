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
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    public const ALL_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PREPARING,
        self::STATUS_DELIVERED,
        self::STATUS_PAID,
        self::STATUS_CANCELLED,
        self::STATUS_REFUNDED,
    ];

    protected $fillable = ['table_id', 'customer_name', 'status', 'payment_status', 'total_amount'];

    // Dışarıdan (ör. controller loglarında) okunabilmesi için public
    public static array $TRANSITIONS = [
        'waiter' => [
                // Garson iptal edebilir: pending|preparing -> cancelled
            self::STATUS_PENDING => [self::STATUS_PREPARING, self::STATUS_CANCELLED],
            self::STATUS_PREPARING => [self::STATUS_DELIVERED, self::STATUS_CANCELLED],
            self::STATUS_DELIVERED => [self::STATUS_PAID],
            self::STATUS_PAID => [],
            self::STATUS_CANCELLED => [],
            self::STATUS_REFUNDED => [],
        ],
        'admin' => [
                // Admin daha esnek: delivered -> paid|refunded|cancelled
            self::STATUS_PENDING => [self::STATUS_PREPARING, self::STATUS_CANCELLED],
            self::STATUS_PREPARING => [self::STATUS_DELIVERED, self::STATUS_CANCELLED],
            self::STATUS_DELIVERED => [self::STATUS_PAID, self::STATUS_REFUNDED, self::STATUS_CANCELLED],
            self::STATUS_PAID => [self::STATUS_REFUNDED], // istersen boş bırakabilirsin
            self::STATUS_CANCELLED => [],
            self::STATUS_REFUNDED => [],
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
        return $this->belongsTo(Table::class, 'table_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function orderItems(): HasMany
    {
        return $this->items(); // alias
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id', 'id');
    }
}
