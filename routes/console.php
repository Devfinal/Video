<?php

use App\Subscription;
use App\TransactionReason;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('reset-view-counters', function () {
    Log::debug('Resetting view counters');

    User::whereHas('roles', function (Builder $query) {
        $query->where('name', 'model');
    })->each(function (User $user) {
        $user->setMeta('visit_count', 0);
    });
});

Artisan::command('check-canceled-subscriptions', function () {
    Log::debug('Checking canceled subscriptions');

    User::whereHas('roles', function (Builder $query) {
        $query->where('name', 'customer');
    })
        ->get()
        ->filter(function (User $user) {
            return $user->getMeta('subscription_canceled', false);
        })
        ->filter(function (User $user) {
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

            return Carbon::now()
                ->sub(Subscription::getData($subscriptionPlan)['duration'])
                ->gte($latestPayment->created_at);
        })
        ->each(function (User $user) {
            $user->setMeta('active_subscription', false);
            $user->deleteMeta('subscription_canceled');
            $user->deleteMeta('subscription_plan');
        });
});

Artisan::command('check-deactivated-blogs', function () {
    Log::debug('Checking deactivated blogs');

    User::whereHas('roles', function (Builder $query) {
        $query->where('name', 'model');
    })
        ->get()
        ->filter(function (User $user) {
            return (bool) $user->getMeta('deactivated', false);
        })
        ->filter(function (User $user) {
            return Carbon::now()
                ->sub(CarbonInterval::months(2))
                ->gte($user->getMeta('deactivated'));
        })
        ->each(function (User $user) {
            $user->deleteMeta('deactivated');
            $user->active = false;

            $user->save();
        });
});
