<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->except(['show', 'edit', 'update']); // Only admin can manage users, but users can edit their own profiles
    }

    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:student,teacher,admin',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        // Check if current user is admin or viewing their own profile
        if (auth()->user()->role !== 'admin' && auth()->id() !== $user->id) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }
        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        // Check if current user is admin or editing their own profile
        if (auth()->user()->role !== 'admin' && auth()->id() !== $user->id) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Check if current user is admin or updating their own profile
        if (auth()->user()->role !== 'admin' && auth()->id() !== $user->id) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:student,teacher,admin',
        ];

        // Only admin can change roles
        if (auth()->user()->role !== 'admin') {
            unset($rules['role']);
        }

        $request->validate($rules);

        $data = $request->only('name', 'email');
        if (auth()->user()->role === 'admin') {
            $data['role'] = $request->role;
        }

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:6|confirmed'
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent self-deletion and deletion of the last admin
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('users.index')->with('error', 'Cannot delete the last admin user');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
