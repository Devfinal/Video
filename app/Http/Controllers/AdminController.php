<?php

namespace App\Http\Controllers;

use App\CartItem;
use App\Country;
use App\Meta;
use App\Order;
use App\OrderStatus;
use App\Product;
use App\Transaction;
use App\TransactionCurrency;
use App\TransactionReason;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Storage;
use View;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Displays the default admin view.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $context = compact('user');

        if ($user->hasRole('model')) {
            $context['recentTransactions'] = $user->transactions()
                ->where('reason', '!=', TransactionReason::CUSTOMER_LIKE)
                ->where('reason', '!=', TransactionReason::ADMIN_LIKE)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        }

        return View::make($user->hasRole('admin') ? 'admin.admin-dashboard' : 'admin.model-dashboard')
            ->with($context);
    }

    /**
     * Displays the admin panel settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $siteMeta = Meta::where('key', 'site_meta')->first();
            $settings = !is_null($siteMeta) ? $siteMeta->value : new Collection;
        } else {
            $settings = $user->getMeta('settings', new Collection);
        }
        $background = isset($siteMeta) ? $siteMeta->background : new Collection;

        return View::make($user->hasRole('admin') ? 'admin.settings' : 'admin.model-settings')
            ->with(compact('settings', 'background'));
    }

    /**
     * Updates the authorized users settings.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $siteMeta = Meta::where('key', 'site_meta')->first();

            if (is_null($siteMeta)) {
                $settings = collect($request->except(['_token', '_method']));
                Meta::create([
                    'key' => 'site_meta',
                    'value' => $settings
                ]);
            } else {
                $settings = $siteMeta->value->merge($request->except(['_token', '_method']));
                $siteMeta->update([
                    'value' => $settings
                ]);
            }
        } else {
            $settings = $user->getMeta('settings', new Collection);
            $settings = $settings->merge($request->except(['_token', '_method']));

            $user->setMeta('settings', $settings);
        }

        return redirect()
            ->back()
            ->with('success', 'Successfully updated settings');
    }

    /**
     * Displays the models orders page.
     *
     * @return \Illuminate\Http\Response
     */
    public function modelOrders()
    {
        return View::make('admin.model-orders')
            ->with('user', Auth::user());
    }

    /**
     * Displays the models order edit page.
     *
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function modelEditOrder(Order $order)
    {
        return View::make('admin.model-orders-edit')
            ->with(compact('order'));
    }

    /**
     * Displays the shop page.
     *
     * @return \Illuminate\Http\Response
     */
    public function shop()
    {
        return View::make('admin.shop')
            ->with([
                'user' => Auth::user(),
                'products' => Product::orderBy('created_at', 'desc')->get()
            ]);
    }

    /**
     * Displays the shop checkout page.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkout()
    {
        $user = Auth::user();
        $cart = $user->getMeta('cart');

        if (is_null($cart)) {
            return redirect()
                ->route('admin.shop')
                ->with('message', 'You have no items in your cart');
        }

        return View::make('admin.checkout')
            ->with([
                'user' => $user,
                'cart' => $cart,
                'cartTotal' => $cart->sum(function (CartItem $item) {
                    return $item->product->price;
                })
            ]);
    }

    /**
     * Performs a shop purchase.
     *
     * @return \Illuminate\Http\Response
     */
    public function purchase()
    {
        $user = Auth::user();
        $cart = $user->getMeta('cart');

        if (is_null($cart)) {
            return redirect()
                ->route('admin.shop')
                ->with('message', 'You have no items in your cart');
        }

        $order = $user->orders()->create([
            'status' => OrderStatus::PENDING,
            'note' => $cart->map(function (CartItem $item) {
                if (!is_null($item->size)) {
                    return "{$item->product->title} ({$item->product->id}): {$item->size}";
                }

                return "{$item->product->title} ({$item->product->id})";
            })->implode('\n')
        ]);

        $order->products()->attach($cart->map(function (CartItem $item) {
            return $item->product->id;
        })->all());

        $user->transactions()->create([
            'amount' => -$cart->sum(function (CartItem $item) {
                return $item->product->price;
            }),
            'currency' => TransactionCurrency::HEARTS,
            'reason' => TransactionReason::PURCHASE,
            'note' => "Order id: {$order->id}"
        ]);

        $user->deleteMeta('cart');

        return redirect()
            ->route('admin.model-orders')
            ->with('success', 'Successfully placed order');
    }

    /**
     * Adds an item to the users cart.
     *
     * @param Request $request
     * @param Product $product
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request, Product $product, User $user)
    {
        $cart = $user->getMeta('cart', new Collection);
        $cartTotal = $cart->sum(function ($item) {
            return $item->product->price;
        });

        if ($product->items <= 0) {
            return redirect()
                ->back()
                ->with('error', 'There are no items left in stock');
        }

        if ($cartTotal + $product->price > $user->getBalance(TransactionCurrency::HEARTS)) {
            return redirect()
                ->back()
                ->with('error', 'You do not have enough hearts to add the item');
        }

        $productFound = $cart->search(function (CartItem $item) use ($product) {
            return $item->product->id === $product->id;
        });

        if ($productFound !== false) {
            return redirect()
                ->back()
                ->with('error', 'You can only add one of each item');
        }

        $product->subtractItems(1);

        $cart->push(new CartItem($product, $request->input('size')));
        $user->setMeta('cart', $cart);

        return redirect()
            ->back()
            ->with([
                'success' => 'Successfully added item to cart',
                'sidebar-open' => 1
            ]);
    }

    /**
     * Removes an item from the users cart.
     *
     * @param User $user
     * @param $index
     * @return \Illuminate\Http\Response
     */
    public function removeFromCart(User $user, $index)
    {
        $cart = $user->getMeta('cart', new Collection);
        $productChunk = $cart->splice($index, 1);
        $user->setMeta('cart', $cart);

        $productChunk->first()->product->addItems(1);

        if ($cart->isEmpty()) {
            $user->deleteMeta('cart');
        }

        return redirect()
            ->back()
            ->with('success', 'Successfully removed item from cart');
    }

    /**
     * Displays the admin FAQ page.
     *
     * @return \Illuminate\Http\Response
     */
    public function faq()
    {
        $user = Auth::user();

        $siteMeta = Meta::where('key', 'site_meta')->first();
        $settings = !is_null($siteMeta) ? $siteMeta->value : new Collection;

        return View::make('admin.faq')
            ->with([
                'content' => $settings->get('faq', ''),
                'user' => $user
            ]);
    }

    /**
     * Updates the admin FAQ page content.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateFaq(Request $request)
    {
        $siteMeta = Meta::where('key', 'site_meta')->first();

        if (is_null($siteMeta)) {
            $settings = collect(['faq' => $request->input('content', '')]);
            Meta::create([
                'key' => 'site_meta',
                'value' => $settings
            ]);
        } else {
            $settings = $siteMeta->value;
            $settings->put('faq', $request->input('content', ''));
            $siteMeta->update([
                'value' => $settings
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Successfully updated FAQ content');
    }

    /**
     * Displays the report page.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function reports(Request $request)
    {
        $report = null;

        if ($request->get('generate')) {
            switch ($request->get('report')) {
                case '1':
                    $report = $this->getCustomerReport($request);
                    break;
                case '2':
                    $report = $this->getCountryReport($request);
                    break;
            }
        }

        return View::make('admin.reports.index')
            ->with(compact('report'));
    }

    /**
     * Generates and displays a report.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateReport(Request $request)
    {
        $this->validate($request, [
            'report' => 'required|in:1,2',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'limit' => 'required|integer|min:0'
        ]);

        return redirect()
            ->route('admin.reports.index', [
                'report' => $request->get('report'),
                'start' => $request->get('start'),
                'end' => $request->get('end'),
                'limit' => $request->get('limit'),
                'generate' => true
            ]);
    }

    /**
     * Returns a file download response for a report.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function downloadReport(Request $request)
    {
        $this->validate($request, [
            'report' => 'required|in:1,2',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'limit' => 'required|integer|min:0'
        ]);

        switch ($request->get('report')) {
            case '1':
                $report = $this->getCustomerReport($request);
                break;
            case '2':
                $report = $this->getCountryReport($request);
                break;
        }

        $start = new Carbon($request->get('start'));
        $end = new Carbon($request->get('end'));
        $filename = "tmp/report-{$request->get('report')}_{$start->format('Y-m-d')}_{$end->format('Y-m-d')}.csv";
        $content = $report->first()->keys()->implode(',') . "\r\n";
        $content .= $report->map(function ($row) {
            return $row->implode(',');
        })->implode("\r\n");

        Storage::disk('local')->put($filename, $content);

        return response()
            ->download(storage_path("app/public/{$filename}"));
    }

    /**
     * Returns a customer report given the request parameters.
     *
     * @param Request $request
     * @return Collection
     */
    protected function getCustomerReport(Request $request)
    {
        $transactions = Transaction::where('reason', TransactionReason::SUBSCRIPTION_PAYMENT)
            ->whereBetween('created_at', $request->only('start', 'end'))
            ->take($request->get('limit') ?: null)
            ->get();

        $report = $transactions->map(function (Transaction $transaction) {
            $user = $transaction->user()->withTrashed()->first();

            return collect([
                'id' => $user->id,
                'email' => "deleted_user_{$user->id}@example.com" === $user->email ? '' : $user->email,
                'date' => $transaction->created_at,
                'country' => Country::getString($user->getMeta('country')),
                'countryCode' => $user->getMeta('country'),
                'subscription' => preg_match('/Subscription plan: (.*?)$/', $transaction->note, $matches) ? $matches[1] : '',
                'amount' => $transaction->amount
            ]);
        });

        if (in_array($request->get('sort'), ['id', 'email', 'date', 'country', 'subscription', 'amount'])) {
            $report = $request->get('sort_direction') === 'asc' ?
                $report->sortBy($request->get('sort')) :
                $report->sortByDesc($request->get('sort'));
        }

        return $report;
    }

    /**
     * Returns a country report given the request parameters.
     *
     * @param Request $request
     * @return Collection
     */
    protected function getCountryReport(Request $request)
    {
        $transactions = Transaction::whereHas('user', function (Builder $query) {
                $query->withTrashed()->whereHas('roles', function (Builder $query) {
                    $query->where('name', 'customer');
                });
            })
            ->where('currency', TransactionCurrency::EUR)
            ->where(function (Builder $query) {
                $query
                    ->where('reason', TransactionReason::SUBSCRIPTION_PAYMENT)
                    ->orWhere('reason', TransactionReason::ADMIN_EDIT);
            })
            ->whereBetween('created_at', $request->only('start', 'end'))
            ->take($request->get('limit') ?: null)
            ->get();

        $report = $transactions
            ->groupBy(function (Transaction $transaction) {
                return $transaction->user()->withTrashed()->first()->getMeta('country');
            })
            ->values()
            ->map(function ($transactions) {
                return collect([
                    'country' => Country::getString($transactions->first()->user->getMeta('country')),
                    'countryCode' => $transactions->first()->user->getMeta('country'),
                    'amount' => $transactions->sum('amount')
                ]);
            });

        if (in_array($request->get('sort'), ['country', 'amount'])) {
            $report = $request->get('sort_direction') === 'asc' ?
                $report->sortBy($request->get('sort')) :
                $report->sortByDesc($request->get('sort'));
        }

        return $report;
    }
}
