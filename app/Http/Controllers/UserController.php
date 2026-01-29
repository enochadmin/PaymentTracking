<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     $perm = \App\Http\Middleware\CheckPermission::class;
    //     $this->middleware($perm . ':users.index')->only('index', 'show');
    //     $this->middleware($perm . ':users.create')->only('create', 'store');
    //     $this->middleware($perm . ':users.update')->only('edit', 'update');
    //     $this->middleware($perm . ':users.delete')->only('destroy');
    // }

    public function index()
    {
        $users = User::with(['roles', 'team.manager'])->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $teams = Team::all();
        return view('users.create', compact('roles', 'teams'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = isset($data['password']) ? Hash::make($data['password']) : Hash::make('password');

        $user = User::create($data);

        if (!empty($data['roles'])) {
            $roleIds = Role::whereIn('id', $data['roles'])->pluck('id')->all();
            $user->roles()->sync($roleIds);
        }

        return redirect()->route('users.index')->with('success', 'User created');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $teams = Team::all();
        return view('users.edit', compact('user', 'roles', 'teams'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if (array_key_exists('roles', $data)) {
            $roleIds = Role::whereIn('id', $data['roles'])->pluck('id')->all();
            $user->roles()->sync($roleIds);
        }

        return redirect()->route('users.show', $user)->with('success', 'User updated');
    }

    public function destroy(User $user)
    {
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User removed');
    }
}
