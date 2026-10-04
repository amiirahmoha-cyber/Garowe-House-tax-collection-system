<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {
    public function index() {
        $users = User::orderByDesc('user_id')->get();
        return view('users.index', compact('users'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:Administrator,Collector,Viewer',
        ]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'Created user', 'details' => $data['username']]);
        return back()->with('success', 'User-ka cusub waa la keydiyay.');
    }
}
