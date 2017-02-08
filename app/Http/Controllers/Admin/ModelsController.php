<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\Events\ModelActivated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreModelRequest;
use App\TransactionCurrency;
use App\TransactionReason;
use App\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use View;

class ModelsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View::make('admin.models.index')
            ->with(
                'models',
                User::whereHas('roles', function (Builder $query) {
                    $query->where('name', 'model');
                })->orderBy('created_at', 'desc')->paginate(32)
            );
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return View::make('admin.models.show')
            ->with(compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return View::make('admin.models.edit')
            ->with([
                'user' => $user,
                'settings' => $user->getMeta('settings', new Collection),
                'recentTransactions' => $user->transactions()
                    ->where('reason', '!=', TransactionReason::CUSTOMER_LIKE)
                    ->where('reason', '!=', TransactionReason::ADMIN_LIKE)
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get()
            ]);
    }

    /**
     * Update the model profile.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request, User $user)
    {
        Session::flash('tab', 'profile');

        $uniqueUsername = $request->input('username') !== $user->username ? '|unique_model' : '';

        $this->validate($request, [
            'username' => "required|max:255{$uniqueUsername}"
        ]);

        $user->profile->update($request->only('presentation'));

        if ($request->input('username') !== $user->username) {
            $user->update($request->only('username'));
        }

        return redirect()
            ->back()
            ->with('success', 'Successfully updated profile');
    }

    /**
     * Update the model settings.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function updateSettings(Request $request, User $user)
    {
        Session::flash('tab', 'settings');

        $uniqueEmail = $request->input('email') !== $user->email ? '|unique:users' : '';

        $this->validate($request, [
            'email' => "required|email|max:255{$uniqueEmail}",
            'password' => 'min:6',
            'country' => 'required|in:' . implode(',', Country::enum()),
        ]);

        $settings = $user->getMeta('settings', new Collection);
        $settings = $settings->merge($request->except(['_token', '_method', 'email', 'password']));
        $user->setMeta('settings', $settings);

        if ($request->input('email') !== $user->email) {
            $user->update($request->only('email'));
        }

        if ($request->has('password')) {
            $user->update([
                'password' => Hash::make($request->input('password'))
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Successfully updated settings');
    }

    /**
     * Update the model balance.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function updateBalance(Request $request, User $user)
    {
        Session::flash('tab', 'balance');

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

        return redirect()->back()->with('success', 'Successfully updated balance');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.models.index')
            ->with('success', 'Successfully removed model');
    }

    /**
     * Deactivate the model.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function deactivate(User $user)
    {
        $user->setMeta('deactivated', Carbon::now());

        return redirect()
            ->back()
            ->with('success', 'Successfully deactivated model');
    }

    /**
     * Activate the model.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function activate(User $user)
    {
        $user->active = true;

        // @note We also verify the user regardless of a valid email address
        $user->verified = true;

        $user->save();

        if (!$user->getMeta('deactivated', false)) {
            event(new ModelActivated($user));
        }

        $user->deleteMeta('deactivated');

        return redirect()
            ->back()
            ->with('success', 'Successfully activated model');
    }
}
