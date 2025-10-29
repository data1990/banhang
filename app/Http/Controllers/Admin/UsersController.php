<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsersController extends Controller
{
    private array $roles = ['admin','staff','kitchen','shipper','customer'];

    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $users = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $this->roles,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => $this->roles,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'role' => 'required|in:' . implode(',', $this->roles),
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Tạo tài khoản thành công');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $this->roles,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'role' => 'required|in:' . implode(',', $this->roles),
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật tài khoản thành công');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Không thể tự xóa tài khoản của bạn');
        }

        $user->delete();

        return back()->with('success', 'Đã xóa tài khoản');
    }
}


