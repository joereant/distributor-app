<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('salesArea')
            ->orderByRaw("case when status = 'pending' then 0 else 1 end, id desc")
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'area' => $user->salesArea?->name,
            ]);

        return Inertia::render('Admin/User', ['users' => $users]);
    }

    public function approve(Request $request, User $user)
    {
        $user->update([
            'status' => 'active',
            'verified_at' => now(),
        ]);

        $request->session()->flash('message', "Akun {$user->name} disetujui.");

        return redirect()->back();
    }

    public function reject(Request $request, User $user)
    {
        $user->update(['status' => 'rejected']);

        $request->session()->flash('message', "Akun {$user->name} ditolak.");

        return redirect()->back();
    }
}
