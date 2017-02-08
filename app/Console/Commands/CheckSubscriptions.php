<?php

namespace App\Console\Commands;

use App\Subscription;
use App\TransactionCurrency;
use App\TransactionReason;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Log;
use SoapClient;
use SoapFault;

class CheckSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks all subscriptions and performs payments for the ones that are due';

    /**
     * The webservice url.
     *
     * @var string
     */
    protected $subscriptionServiceUrl = 'https://ssl.ditonlinebetalingssystem.dk/remote/subscription.asmx?WSDL';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $customers = User::whereHas('roles', $this->hasRole('customer'))
            ->get()
            ->filter($this->isSubscriber());
        $dueCustomers = $customers->filter($this->subscriberIsDue());

        $this->comment("{$dueCustomers->count()} out of {$customers->count()} paying customers are due\n");

        $bar = $this->output->createProgressBar($dueCustomers->count());

        foreach ($dueCustomers as $customer) {
            $this->performPayment($customer);

            $bar->advance();
        }

        $bar->finish();

        $this->info("\n\nDone");
    }

    protected function hasRole($role)
    {
        return function (Builder $query) use ($role) {
            $query->where('name', $role);
        };
    }

    protected function isSubscriber()
    {
        return function (User $user) {
            return !is_null($user->getMeta('subscription_id'));
        };
    }

    protected function subscriberIsDue()
    {
        return function (User $user) {
            $subscriptionPlan = $user->getMeta('subscription_plan');

            if (!in_array($subscriptionPlan, Subscription::enum())) {
                Log::error('Invalid user subscription plan', [
                    'user' => $user->id,
                    'subscription_plan' => $user->getMeta('subscription_plan')
                ]);

                return false;
            }

            $latestPayment = $user->transactions()
                ->where('reason', TransactionReason::SUBSCRIPTION_PAYMENT)
                ->orderBy('created_at', 'desc')
                ->first();

            return Carbon::now()->sub(Subscription::getData($subscriptionPlan)['duration'])->gte($latestPayment->created_at);
        };
    }

    protected function performPayment(User $user)
    {
        Log::info('Performing payment', ['user' => $user->id]);

        $amount = str_replace(
            '.',
            '',
            sprintf('%.2f', Subscription::getData($user->getMeta('subscription_plan'))['amount'])
        );

        try {
            $client = new SoapClient($this->subscriptionServiceUrl);

            $result = $client->authorize([
                'merchantnumber' => env('EPAY_MERCHANT_NUMBER'),
                'subscriptionid' => $user->getMeta('subscription_id'),
                'orderid' => $user->id,
                'amount' => $amount,
                'currency' => TransactionCurrency::EUR,
                'instantcapture' => 1,
                'fraud' => 0,
                'transactionid' => -1,
                'pbsresponse' => -1,
                'epayresponse' => -1
            ]);

            if (!$result->authorizeResult) {
                Log::error('Error during payment', [
                    'user' => $user->id,
                    'pbsresponse' => $result->pbsresponse,
                    'epayresponse' => $result->epayresponse
                ]);

                $user->setMeta('active_subscription', false);

                return;
            }

            Log::info('Payment successful', ['user' => $user->id]);

            $user->transactions()->create([
                'currency' => TransactionCurrency::EUR,
                'amount' => $amount / 100,
                'reason' => TransactionReason::SUBSCRIPTION_PAYMENT,
                'note' => <<<NOTE
Transaction id: {$result->transactionid}
Subscription plan: {$user->getMeta('subscription_plan')}
NOTE
            ]);

            $user->setMeta('active_subscription', true);
        } catch (SoapFault $exception) {
            Log::error('Error contacting payment service', [
                'user' => $user->id,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
