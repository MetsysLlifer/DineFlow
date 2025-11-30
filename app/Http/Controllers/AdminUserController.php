<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\ActivityLogger;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q',''));
        $users = User::query()
            ->when($q !== '', fn($qb) => $qb->where(function($qq) use ($q){
                $qq->where('name','like',"%$q%")
                   ->orWhere('email','like',"%$q%");
            }))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
        return view('admin.users.index', compact('users','q'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8'],
            'role' => ['required','string','in:admin,manager,cashier,kitchen,host,analyst,customer']
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);
        ActivityLogger::log('user.create', User::class, $user->id, ['role' => $user->role]);
        return redirect()->route('admin.users.index')->with('status','User created');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email,'.$user->id],
            'password' => ['nullable','string','min:8'],
            'role' => ['required','string','in:admin,manager,cashier,kitchen,host,analyst,customer']
        ]);
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];
        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }
        $user->update($payload);
        ActivityLogger::log('user.update', User::class, $user->id, ['role' => $user->role]);
        return redirect()->route('admin.users.index')->with('status','User updated');
    }

    public function destroy(User $user)
    {
        $id = $user->id;
        $snapshot = ['name' => $user->name, 'email' => $user->email, 'role' => $user->role];
        $user->delete();
        ActivityLogger::log('user.delete', User::class, $id, $snapshot);
        return redirect()->route('admin.users.index')->with('status','User deleted');
    }
}
