<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Customer;
use App\Models\Referal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        $user = $request->user();

        if ($user->status !== 'active') {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun belum aktif. Hubungi admin.'])->onlyInput('email');
        }

        return redirect($this->redirectPathFor($user));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showRegister(Request $request)
    {
        return Inertia::render('Auth/Register', [
            'areas' => Area::orderBy('name')->get(['id', 'name']),
            'referal_code' => $request->get('ref'),
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'sales_area_id' => ['required', 'exists:areas,id'],
            'referal_code' => ['nullable', 'string', 'max:20'],
        ]);

        $referalCustomerId = null;
        if (!empty($data['referal_code'])) {
            $referal = Referal::where('referral_code', strtoupper($data['referal_code']))->first();
            $referalCustomerId = $referal?->customer_id;
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'sales_area_id' => $data['sales_area_id'],
            'role' => 'customer',
            'status' => 'pending',
        ]);

        Customer::create([
            'user_id' => $user->id,
            'company_name' => $data['name'],
            'email' => $data['email'],
            'sales_area_id' => $data['sales_area_id'],
            'referal_id' => $referalCustomerId,
        ]);

        Auth::logout();

        $msg = "Pendaftaran berhasil, {$user->name}! Akun Anda menunggu persetujuan admin.";
        if ($referalCustomerId) {
            $msg .= " Anda join via referal.";
        }

        return redirect('/login')->with('message', $msg);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google login failed: '.$e->getMessage());
            return redirect('/login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?? 'Customer Baru',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'role' => 'customer',
                'status' => 'pending',
            ]);

            return redirect('/login')->with('message', 'Akun Google terdaftar sebagai customer. Tunggu persetujuan admin sebelum login.');
        }

        if ($user->status !== 'active') {
            return redirect('/login')->with('error', 'Akun belum aktif. Hubungi admin.');
        }

        $user->forceFill([
            'google_id' => $googleUser->getId(),
            'avatar' => $user->avatar ?? $googleUser->getAvatar(),
        ])->save();

        Auth::login($user);

        return redirect($this->redirectPathFor($user));
    }

    private function redirectPathFor($user): string
    {
        return match ($user->role) {
            'owner' => '/owner',
            'admin' => '/admin',
            'sales' => '/sales',
            default => '/customer',
        };
    }
}
