<?php
namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    // 支持的 provider 白名单（安全）
    protected $providers = ['google', 'facebook'];

    public function redirect($provider)
    {
        if (!in_array($provider, $this->providers)) {
            abort(404);
        }
        if ($provider === 'google') {
        return Socialite::driver('google')
                ->with(['prompt' => 'select_account']) // ✅ 强制选择账号
                ->redirect();
        }
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        if (!in_array($provider, $this->providers)) {
            abort(404);
        }

        try {
            // ❗ 不用 stateless（简单稳定）
            $socialUser = Socialite::driver($provider)->user();

            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName() ?? 'User '.Str::random(4),
                    'email' => $socialUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                ]);
            }

            Auth::login($user, true);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', ucfirst($provider).' login failed');
        }
    }
}