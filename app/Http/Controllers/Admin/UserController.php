<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()->latest()->paginate(12);
        return view('admin.users.manage', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'], // yeni kullanıcı için zorunlu
            'role' => ['required', Rule::in(['admin', 'user', 'waiter'])],
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role = $data['role'] ?? 'user';
        $user->save();

        return back()->with('success', 'Kullanıcı oluşturuldu.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:190',
                Rule::unique('users', 'email')->ignore($user->id), // kendi kaydını hariç tut
            ],
            'password' => ['nullable', 'string', 'min:6'], // güncellemede opsiyonel
            'role' => ['required', Rule::in(['admin', 'user', 'waiter'])],
            'verified' => ['nullable', 'boolean'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->role = $data['role'];

        // E-posta doğrulama toggle
        if (array_key_exists('verified', $data)) {
            $user->email_verified_at = $data['verified'] ? now() : null;
        }

        $user->save();

        return back()->with('success', 'Kullanıcı güncellendi.');
    }

    public function destroy(User $user)
    {
        // Kendini silemesin (opsiyonel güvenlik)
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Kendi hesabınızı silemezsiniz.');
        }

        $user->delete();
        return back()->with('success', 'Kullanıcı silindi.');
    }
}
