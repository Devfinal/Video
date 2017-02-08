<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserRegistered;
use App\Subscription;
use App\TransactionCurrency;
use App\TransactionReason;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Log;
use Validator;

trait RegistersUsers
{
    use RedirectsUsers;

    /**
     * The payment service url.
     *
     * @var string
     */
    protected $paymentServiceUrl = 'https://ssl.ditonlinebetalingssystem.dk/integration/ewindow/Default.aspx';

    /**
     * Show the application registration form.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm(Request $request)
    {
        return view('auth.register')
            ->with([
                'model' => User::where([
                    'id' => $request->get('model')
                ])->whereHas('roles', function (Builder $query) {
                    $query->where('name', 'model');
                })->first(),
                'registerForModel' => $request->get('model')
            ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|confirmed',
            'username' => 'required|max:255',
            'terms' => 'required',
            'subscription' => 'required'
        ])->validate();

        // @note We validate the email address separately to allow for trashed users email addresses
        if (User::get()->contains('email', $request->input('email'))) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'email' => 'The email has already been taken.'
                ]);
        }

        event(new UserRegistered($user = $this->create($request), $request));

        return $this->registered($request, $user) ?: redirect($this->redirectPath());
    }

    /**
     * Verifies a verification token.
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request, $token)
    {
        $user = $this->verifyToken($token);

        $this->guard()->login($user);

        return redirect()
            ->route('home');
    }

    /**
     * Handles the payment complete callback.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function paymentComplete(Request $request)
    {
        $user = User::findOrFail($request->get('orderid'));

        if (!$user->getMeta('pending_payment', false)) {
            Log::warning('User not found at payment.complete', $request->all());

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

        // model commission
        $model = User::find($request->get('model'));

        if (!is_null($model) && !$user->getMeta('returning_customer', false)) {
            $model->transactions()->create([
                'currency' => TransactionCurrency::EUR,
                'amount' => $model->getMeta('settings', new Collection)->get('commission', 2),
                'reason' => TransactionReason::USER_REGISTRATION,
                'note' => <<<NOTE
Subscription plan: {$request->get('subscription_plan')}
User id: {$user->id}
NOTE
            ]);
        }

        Auth::login($user);

        return redirect()
            ->route('home');
    }

    /**
     * Handles the payment canceled callback.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function paymentCanceled(Request $request)
    {
        $user = User::findOrFail($request->get('orderid'));

        if (!$user->getMeta('pending_payment', false)) {
            Log::warning('User not found at payment.complete', $request->all());

            abort('404');
        }

        // @note At the moment we don't remove the registered user.
        // $user->delete();
        $user->deleteMeta('pending_payment');

        return redirect()
            ->route('home');
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * The user has been registered.
     *
     * @param Request $request
     * @param User $user
     * @return mixed
     */
    protected function registered(Request $request, User $user)
    {
        $this->validate($request, [
            'subscription' => 'required|in:' . implode(',', Subscription::enum())
        ]);

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
                    'accepturl' => route('payments.complete', [
                        'model' => $request->get('model'),
                        'subscription_plan' => $request->input('subscription')
                    ]),
                    'cancelurl' => route('payments.canceled', [
                        'orderid' => $user->id
                    ]),
                ], null, '&', PHP_QUERY_RFC3986)
            );
    }
}
