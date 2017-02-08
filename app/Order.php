<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'note'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * Returns the total amount of the order.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->products->sum('price');
    }

    public static function boot()
    {
        parent::boot();

        Order::saving(function (Order $transaction) {
            // limit the status value to the OrderStatus enum
            if (!in_array($transaction->status, OrderStatus::enum())) {
                return false;
            }
        });
    }
}
