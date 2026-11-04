<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Show the change password form.
     */
    public function edit(): View
    {
        return view('settings.password');
    }

    /**
     * Change the password, then sign out every other session.
     */
    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->forceFill([
            'password' => $request->validated('password'),
            'remember_token' => null,
        ])->save();

        if (config('session.driver') === 'database') {
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $user->getAuthIdentifier())
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        $request->session()->regenerate();

        return redirect()->route('settings.password.edit')->with('toast', [
            'type' => 'success',
            'message' => __('Password updated. Other devices have been signed out.'),
        ]);
    }
}
