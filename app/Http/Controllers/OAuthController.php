<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Exception;
use Hash;
use Illuminate\Http\RedirectResponse;
use Redirect;
use Socialite;
use Str;

class OAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleCallback(): RedirectResponse
    {
        try {
            $oauthUser = Socialite::driver('github')->user();
        } catch (Exception) {
            return Redirect::route('login');
        }

        $user = User::firstOrCreate([
            'email' => $oauthUser->email,
        ], [
            'name' => $oauthUser->name,
            'password' => Hash::make(Str::random(10)),
        ]);

        Auth::login($user);

        return Redirect::route('dashboard');
    }
}
