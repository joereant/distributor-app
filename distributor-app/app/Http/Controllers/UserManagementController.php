<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Area;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('area');

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->role) {
            $query->where('role', $role);
        }

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        $areas = Area::orderBy('name')->get();

        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'pending' => User::where('status', 'pending')->count(),
            'sales' => User::where('role', 'sales')->count(),
        ];

        return inertia('Admin/UserManagement/Index', [
            'users' => $users,
            'areas' => $areas,
            'stats' => $stats,
            'filters' => [
                'search' => $search ?? '',
                'role' => $role ?? '',
                'status' => $status ?? '',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:50',
            'role' => 'required|in:owner,admin,sales,customer',
            'sales_area_id' => 'nullable|exists:areas,id',
        ]);

        $validated['status'] = 'active';
        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);

        return redirect('/admin/users-manage')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'role' => 'required|in:owner,admin,sales,customer',
            'status' => 'required|in:active,pending,rejected',
            'sales_area_id' => 'nullable|exists:areas,id',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'min:6';
            $validated = $request->validate($rules);
            $validated['password'] = bcrypt($validated['password']);
        } else {
            $validated = $request->validate($rules);
        }

        $user->update($validated);

        return redirect('/admin/users-manage')->with('success', 'User berhasil diperbarui.');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'action' => 'required|in:activate,deactivate,change_role',
            'role' => 'nullable|in:owner,admin,sales,customer',
        ]);

        $ids = array_diff($validated['ids'], [auth()->id()]);

        if (empty($ids)) {
            return redirect('/admin/users-manage')->with('success', 'Tidak ada akun yang diubah.');
        }

        if ($validated['action'] === 'activate') {
            User::whereIn('id', $ids)->update([
                'status' => 'active',
                'verified_at' => now(),
            ]);
            $msg = count($ids) . ' user berhasil diaktifkan.';
        } elseif ($validated['action'] === 'deactivate') {
            User::whereIn('id', $ids)->update([
                'status' => 'rejected',
            ]);
            $msg = count($ids) . ' user berhasil dinonaktifkan.';
        } elseif ($validated['action'] === 'change_role' && !empty($validated['role'])) {
            User::whereIn('id', $ids)->update([
                'role' => $validated['role'],
            ]);
            $msg = 'Role ' . count($ids) . ' user berhasil diubah ke ' . ucfirst($validated['role']) . '.';
        }

        return redirect('/admin/users-manage')->with('success', $msg ?? 'Aksi masal berhasil.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['general' => 'Tidak bisa menghapus akun sendiri.']);
        }

        $user->delete();

        return redirect('/admin/users-manage')->with('success', 'User berhasil dihapus.');
    }
}
