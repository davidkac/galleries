<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        /** @var User $user */
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $admin = (bool) ($request->admin ?? false);

        /**
         * Based on value of admin field provided through the request,
         * decide which role to assign to new user
         */
        match ($admin) {
            true => $role = Role::findByName('admin'),
            false => $role = Role::findByName('user'),
        };

        $user->assignRole($role);

        return redirect()->route('dashboard', ['tab' => 'users'])->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update([
            'name' => $request->name,
        ]);

        /**
         * In case 'admin' field is provided, decide which role
         * to assign to user but just in case user doesn't already
         * have that role. If user have the opposite role (e.g. needs to
         * get admin role but already have user role and vice versa),
         * remove that role after new role is assigned.
         */
        if ($request->has('admin')) {
            $adminRole = Role::findByName('admin');

            if ($request->admin && !$user->hasRole($adminRole)) {
                $user->assignRole($adminRole);

                $userRole = Role::findByName('user');

                if ($user->hasRole($userRole)) {
                    $user->removeRole($userRole);
                }
            } else if (!$request->admin && $user->hasRole($adminRole)) {
                $user->removeRole($adminRole);

                $userRole = Role::findByName('user');

                if (!$user->hasRole($userRole)) {
                    $user->assignRole($userRole);
                }
            }
        }

        return redirect()->route('dashboard', ['tab' => 'users'])->with('success', 'User data updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('dashboard', ['tab' => 'users'])->with('success', 'User removed from the system successfully.');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));   
    }
}
