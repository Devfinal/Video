<?php

namespace App\Http\Controllers;

use App\Country;
use App\Subscription;
use App\TransactionCurrency;
use App\TransactionReason;
use App\User;
use Auth;
use Illuminate\Http\Request;
use View;

class SettingsController extends Controller
{
    /**
     * The payment service url.
     *
     * @var string
     */
    protected $paymentServiceUrl = 'https://ssl.ditonlinebetalingssystem.dk/integration/ewindow/Default.aspx';

    /**
     * Displays the customer settings page.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('customer')) {
            return redirect()
                ->route('admin.settings');
        }

        $subscriptionEnding = null;

        if ($user->hasActiveSubscription()) {
            $latestPayment = $user->transactions()
                ->where('reason', TransactionReason::SUBSCRIPTION_PAYMENT)
                ->orderBy('created_at', 'desc')
                ->first();

            $subscriptionPlan = $user->getMeta('subscription_plan');

            if (in_array($subscriptionPlan, Subscription::enum())) {
                $subscriptionEnding = $latestPayment->created_at
                    ->add(Subscription::getData($subscriptionPlan)['duration'])
                    ->format('Y-m-d');
            }
        }

        return View::make('pages/settings')
            ->with(compact('user', 'subscriptionEnding'));
    }

    /**
     * Updates the users settings.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $uniqueEmail = $request->input('email') !== $user->email ? '|unique:users' : '';

        $this->validate($request, [
            'email' => "required|email|max:255{$uniqueEmail}",
            'country' => 'required|in:' . implode(',', Country::enum())
        ]);

        $user->update($request->only('email'));

        return redirect()
            ->back()
            ->with('success', 'Successfully updated settings');
    }

    /**
     * Create a subscription.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function storeSubscription(Request $request, User $user)
    {
        $this->validate($request, [
            'subscription' => 'required|in:' . implode(',', Subscription::enum())
        ]);

        $user->setMeta('pending_payment', true);

        return redirect()
            ->to(
                $this->paymentServiceUrl . '?' . http_build_query([
                    'merchantnumber' => env('EPAY_MERCHANT_NUMBER'),
                    'orderid' => $user->id,
                    'amount' => str_replace(
                        '.',
                        '',
                        sprintf('%.2f', Subscription::getData($request->input('subscription'))['amount'])
                    ),
                    'currency' => TransactionCurrency::EUR,
                    'subscription' => 1,
                    'instantcapture' => 1,
                    'windowstate' => 3,
                    'mobilecssurl' => asset('css/epay-mobile.css'),
                    'accepturl' => route('customers.payments.complete', [
                        'subscription_plan' => $request->input('subscription')
                    ]),
                    'cancelurl' => route('customers.payments.canceled'),
                ], null, '&', PHP_QUERY_RFC3986)
            );
    }

    /**
     * Handles the payment complete callback.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function paymentComplete(Request $request)
    {
        $user = Auth::user();

        if (
            is_null($user) ||
            $user->id !== (int) $request->get('orderid') ||
            !$user->getMeta('pending_payment', false)
        ) {
            Log::warning('User not found at customers.payments.complete', $request->all());

            abort('404');
        }

        $user->transactions()->create([
            'currency' => $request->get('currency'),
            'amount' => $request->get('amount') / 100,
            'reason' => TransactionReason::SUBSCRIPTION_PAYMENT,
            'note' => <<<NOTE
Transaction id: {$request->get('txnid')}
Subscription plan: {$request->get('subscription_plan')}
NOTE
        ]);
        $user->setMeta('subscription_id', $request->get('subscriptionid'));
        $user->setMeta('active_subscription', true);
        $user->setMeta('subscription_plan', $request->get('subscription_plan'));
        $user->setMeta('card_number', $request->get('cardno'));
        $user->deleteMeta('pending_payment');

        return redirect()
            ->route('settings.edit');
    }

    /**
     * Handles the payment canceled callback.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function paymentCanceled(Request $request)
    {
        $user = Auth::user();

        if (!is_null($user)) {
            $user->deleteMeta('pending_payment');
        }

        return redirect()
            ->route('settings.edit');
    }
}
