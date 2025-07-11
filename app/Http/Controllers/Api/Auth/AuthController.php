<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Rules\Recaptcha;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\TwoFactorAuthenticatable;

class AuthController extends Controller
{
    /**
     * Where user's can log into the API
     *
     * @return void
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
            // 'recaptcha' => App::environment(['local', 'testing']) ? 'nullable' : [new Recaptcha, 'required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            event(new Failed('web', $user, [
                Fortify::username() => $request->email,
                'reason' => 'Failed login: Invalid credentials',
            ]));

            throw ValidationException::withMessages([
                'email' => [__('The provided credentials are incorrect.')],
            ]);
        }

        // Lock out users that don't have an app role
        if (! $user->can('app.login')) {
            event(new Failed('web', $user, [
                Fortify::username() => $request->email,
                'reason' => 'Failed login: No app login for user role',
            ]));

            throw ValidationException::withMessages([
                'email' => [__('Sorry the app is not setup for your user account type.')],
            ]);
        }

        // Check to see if app and user has 2FA
        if (Features::enabled(Features::twoFactorAuthentication()) &&
            optional($user)->two_factor_secret &&
            optional($user)->two_factor_confirmed_at &&
            in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {

            return response()->json(['two_factor' => true, 'token' => $user->createToken($request->device_name, ['login'])->plainTextToken], 200);
        }

        event(new Login('web', $user, false));

        return response()->json(['token' => $user->createToken($request->device_name)->plainTextToken], 200);
    }

    /**
     * Where user's can log out
     *
     * @return void
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $user = $request->user();
            $token = $request->bearerToken();

            try {
                // Everywhere or not a token request, delete all tokens for this user
                if ($request->has('everywhere') || ! $token) {
                    $user->tokens()->delete();

                    // Make sure it's a token request
                } elseif ($token) {
                    $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
                }

                event(new Logout('web', $user));
            } catch (\Exception $e) {
                Log::debug(__METHOD__." issue with logout. {$e->getMessage()}");
                report($e);
            }
        }

        return response()->json(['success' => true], 200);
    }

    /**
     * Where user's can revoke tokens currently active in their account
     *
     * @return void
     */
    public function revokeTokens(Request $request)
    {
        $user = $request->user();

        try {
            $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();
            event(new Logout('web', $user));
        } catch (\Exception $e) {
            report($e);
        }

        return response()->json(['success' => true], 200);
    }
}
