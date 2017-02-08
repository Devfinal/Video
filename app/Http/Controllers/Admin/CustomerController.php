<?php

namespace App\Http\Controllers\Admin;

use App\EpayResponseErrorException;
use App\Http\Controllers\Controller;
use App\TransactionCurrency;
use App\TransactionReason;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use SoapClient;
use SoapFault;
use View;

class CustomerController extends Controller
{
    /**
     * The webservice url.
     *
     * @var string
     */
    protected $subscriptionServiceUrl = 'https://ssl.ditonlinebetalingssystem.dk/remote/subscription.asmx?WSDL';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View::make('admin.customers.index')
            ->with(
                'customers',
                User::whereHas('roles', function (Builder $query) {
                    $query->where('name', 'customer');
                })->orderBy('created_at', 'desc')->paginate(32)
            );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return View::make('admin.customers.edit')
            ->with(compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $uniqueEmail = $request->input('email') !== $user->email ? '|unique:users' : '';

        $this->validate($request, [
            'email' => "required|email|max:255{$uniqueEmail}"
        ]);

        if ($request->input('username') !== $user->username) {
            $user->update($request->only('username'));
        }

        if ($request->input('email') !== $user->email) {
            $user->update($request->only('email'));
        }

        return redirect()
            ->back()
            ->with('success', 'Successfully updated settings');
    }

    /**
     * Cancel the given users subscription.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function cancel(User $user)
    {
        try {
            $this->cancelSubscription($user);
        } catch (EpayResponseErrorException $exception) {
            return redirect()
                ->back()
                ->with('error', "Unable to cancel the subscription: {$exception->getMessage()}");
        } catch (SoapFault $exception) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    "<strong>Error contacting payment service:</strong><br>{$exception->getMessage()}"
                );
        }

        return redirect()
            ->back()
            ->with('success', 'Successfully canceled subscription');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (!is_null($user->getMeta('subscription_id'))) {
            try {
                $this->cancelSubscription($user);
            } catch (EpayResponseErrorException $exception) {
                return redirect()
                    ->back()
                    ->with('error', "Unable to cancel the subscription: {$exception->getMessage()}");
            } catch (SoapFault $exception) {
                return redirect()
                    ->back()
                    ->with(
                        'error',
                        "<strong>Error contacting payment service:</strong><br>{$exception->getMessage()}"
                    );
            }
        }

        // @note We still need to set the meta values as the user is soft deleted and may be restored later
        $user->setMeta('active_subscription', false);
        $user->deleteMeta('subscription_plan');

        $user->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Successfully removed customer');
    }

    /**
     * Update the user balance by creating a new transaction.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function updateBalance(Request $request, User $user)
    {
        $this->validate($request, [
            'amount' => 'required|numeric',
            'currency' => 'required|in:' . implode(',', TransactionCurrency::enum())
        ]);

        $user->transactions()->create([
            'amount' => $request->input('amount'),
            'currency' => $request->input('currency'),
            'reason' => TransactionReason::ADMIN_EDIT,
            'note' => $request->input('note')
        ]);

        return redirect()
            ->back()
            ->with('success', 'Successfully created transaction');
    }

    /**
     * Cancels the given users subscription.
     *
     * @param User $user
     * @return void
     * @throws EpayResponseErrorException
     */
    protected function cancelSubscription(User $user)
    {
        $client = new SoapClient($this->subscriptionServiceUrl);

        $result = $client->deletesubscription([
            'merchantnumber' => env('EPAY_MERCHANT_NUMBER'),
            'subscriptionid' => $user->getMeta('subscription_id'),
            'epayresponse' => -1
        ]);

        if (!$result->deletesubscriptionResult) {
            throw new EpayResponseErrorException($result->epayresponse);
        }

        $user->setMeta('subscription_canceled', true);
        $user->deleteMeta('subscription_id');
        $user->deleteMeta('card_number');
    }
}
