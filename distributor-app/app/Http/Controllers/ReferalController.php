<?php

namespace App\Http\Controllers;

use App\Models\Referal;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReferalController extends Controller
{
    /**
     * List referrals user ini.
     */
    public function index()
    {
        $user = auth()->user();

        $customer = $user->customer;

        if (!$customer) {
            return inertia('Customer/Referal', [
                'customer' => null,
                'referrals' => [],
                'stats' => ['total_referrals' => 0, 'total_transactions' => 0, 'total_margin' => 0],
                'referral_code' => null,
            ]);
        }

        // Get or create referral code for this customer
        $referral = Referal::firstOrCreate(
            ['customer_id' => $customer->id, 'transaction_id' => null],
            ['referral_code' => strtoupper(Str::random(8))]
        );

        // List successful referrals (customers who used this code)
        $referrals = Referal::with(['customer', 'transaction'])
            ->whereNotNull('transaction_id')
            ->whereHas('customer', function ($q) use ($customer) {
                $q->where('referal_id', $customer->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Stats
        $totalReferrals = $referrals->count();
        $totalTransactions = $referrals->where('transaction.status', 'approved')->count();
        $totalMargin = $referrals
            ->where('transaction.status', 'approved')
            ->sum(fn ($r) => $r->transaction->margin_estimate ?? 0);

        return inertia('Customer/Referal', [
            'customer' => $customer,
            'referral_code' => $referral->referral_code,
            'referrals' => $referrals,
            'stats' => [
                'total_referrals' => $totalReferrals,
                'total_transactions' => $totalTransactions,
                'total_margin' => $totalMargin,
            ],
        ]);
    }

    /**
     * Generate kode baru.
     */
    public function regenerate()
    {
        $user = auth()->user();
        $customer = $user->customer;

        if (!$customer) {
            return back()->with('error', 'Akun belum linked ke customer.');
        }

        // Soft-delete kode lama (jika ada transaction, kode lama tetap dipakai)
        $newCode = strtoupper(Str::random(8));

        $referral = Referal::create([
            'customer_id' => $customer->id,
            'transaction_id' => null,
            'referral_code' => $newCode,
        ]);

        return back()->with('success', 'Kode referal baru: ' . $newCode);
    }
}
