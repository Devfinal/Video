<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Order;
use App\OrderStatus;
use App\Product;
use App\Transaction;
use App\TransactionReason;
use Auth;
use View;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View::make('admin.orders.index')
            ->with(
                'orders',
                Order::orderBy('created_at', 'desc')->paginate(25)
            );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        // set the pending status when the order is viewed
        if ($order->status === OrderStatus::PENDING) {
            $order->update([
                'status' => OrderStatus::PROCESSING
            ]);
        }

        return View::make('admin.orders.edit')
            ->with(compact('order'));
    }

    /**
     * Complete the order.
     *
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function ship(Order $order)
    {
        $order->update([
            'status' => OrderStatus::SHIPPED
        ]);

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Successfully completed order');
    }

    /**
     * Cancel the order.
     *
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function cancel(Order $order)
    {
        // refill stock
        $order->products->each(function (Product $product) {
            $product->addItems(1);
        });

        $order->update([
            'status' => OrderStatus::CANCELED
        ]);

        $transaction = Transaction::where('reason', TransactionReason::PURCHASE)
            ->where('note', 'LIKE', "%Order id: {$order->id}%")
            ->first();

        // refund order
        if (!is_null($transaction)) {
            $order->user->transactions()->create([
                'amount' => -$transaction->amount,
                'currency' => $transaction->currency,
                'reason' => TransactionReason::REFUND,
                'note' => "Order id: {$order->id}"
            ]);
        }

        return redirect()
            ->route(Auth::user()->hasRole('admin') ? 'admin.orders.index' : 'admin.model-orders')
            ->with('success', 'Successfully canceled order');
    }
}
