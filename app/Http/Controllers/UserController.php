<?php
// [Khaula] Layanan User Management

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonService;

class UserController extends Controller
{
    // [Khaula] Read - Daftar semua student (akses guru)
    public function index(JsonService $json)
    {
        $users = collect($json->read('users'))
            ->where('role', 'student')
            ->values()->all();
        return view('guru.users', compact('users'));
    }

    // [Khaula] Read - Profil student yang sedang login
    public function profile(JsonService $json)
    {
        $user    = collect($json->read('users'))->firstWhere('id', session('user_id'));
        $potions = collect($json->read('potions'))->where('student_id', session('user_id'));
        $wand    = $user['wand_id'] ? collect($json->read('wands'))->firstWhere('id', $user['wand_id']) : null;

        $stats = [
            'total'    => $potions->count(),
            'approved' => $potions->where('status', 'approved')->count(),
            'pending'  => $potions->where('status', 'pending')->count(),
            'rejected' => $potions->where('status', 'rejected')->count(),
        ];

        return view('student.profile', compact('user', 'stats', 'wand'));
    }

    // [Khaula] Update - Upload foto profil student
    public function uploadPhoto(Request $request, JsonService $json)
    {
        $request->validate(['photo' => 'required|image|max:2048']);

        $file     = $request->file('photo');
        $filename = 'avatar_' . session('user_id') . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/avatars'), $filename);

        $users = $json->read('users');
        $users = collect($users)->map(function ($u) use ($filename) {
            if ($u['id'] === session('user_id')) {
                if (!empty($u['photo']) && file_exists(public_path($u['photo']))) {
                    unlink(public_path($u['photo']));
                }
                $u['photo'] = 'uploads/avatars/' . $filename;
            }
            return $u;
        })->all();

        $json->write('users', $users);
        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    // [Khaula] Delete - Hapus akun student
    public function deleteAccount(Request $request, JsonService $json)
    {
        $request->validate(['password' => 'required']);

        $userId = session('user_id');
        $user   = collect($json->read('users'))->firstWhere('id', $userId);

        if (!$user || !password_verify($request->password, $user['password'])) {
            return back()->with('delete_error', 'Password salah.');
        }

        $users = collect($json->read('users'))->reject(fn($u) => $u['id'] === $userId)->values()->all();
        $json->write('users', $users);

        session()->flush();
        return redirect()->route('login')->with('success', 'Akun berhasil dihapus.');
    }
}
