<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'currency',
        'amount',
        'reason',
        'note'
    ];

    public static function boot()
    {
        parent::boot();

        // validate the reason and currency
        Transaction::saving(function (Transaction $transaction) {
            if (
                !in_array($transaction->reason, TransactionReason::enum()) ||
                !in_array($transaction->currency, TransactionCurrency::enum())
            ) {
                return false;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
