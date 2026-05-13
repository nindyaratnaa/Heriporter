<?php
// [KELOMPOK] Auth - Khaula: login/register/JWT logic
// [KELOMPOK] Auth - Adzkia: validasi input & hashing
// [KELOMPOK] Auth - Nya: middleware autentikasi
// [KELOMPOK] Auth - Sefina: middleware role/authorization

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonService;
use App\Http\Controllers\RaporController;

class AuthController extends Controller
{
    // [Khaula] Tampilkan halaman login
    public function showLogin()
    {
        if (session('user_id')) {
            return $this->redirectByRole(session('user_role'));
        }
        return view('auth.login');
    }

    // [Khaula] Proses login & JWT session
    // [Adzkia] Validasi input & password hashing check
    public function login(Request $request, JsonService $json)
    {
        // [Adzkia] Validasi input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $users = $json->read('users');
        $user  = collect($users)->firstWhere('email', $request->email);

        // [Adzkia] Hashing check
        if (!$user || !password_verify($request->password, $user['password'])) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
        }

        // [Khaula] Simpan session
        session([
            'user_id'    => $user['id'],
            'user_name'  => $user['name'],
            'user_email' => $user['email'],
            'user_role'  => $user['role'],
            'user_house' => $user['house'] ?? null,
        ]);

        // Student tanpa house → sorting hat
        if ($user['role'] === 'student' && empty($user['house'])) {
            return redirect()->route('sorting-hat.questions');
        }

        return $this->redirectByRole($user['role']);
    }

    // [Khaula] Tampilkan halaman register
    public function showRegister()
    {
        return view('auth.register');
    }

    // [Khaula] Proses registrasi user baru (Create)
    // [Adzkia] Validasi input & hashing password
    public function register(Request $request, JsonService $json)
    {
        // [Adzkia] Validasi input register
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:student,guru',
        ]);

        $users = $json->read('users');

        // Cek email sudah ada
        if (collect($users)->firstWhere('email', $request->email)) {
            return back()->withErrors(['email' => 'Email sudah terdaftar.'])->withInput();
        }

        // [Adzkia] Validasi domain email sesuai role
        if ($request->role === 'student' && !str_ends_with($request->email, '@student.hogwarts.ac.id')) {
            return back()->withErrors(['email' => 'Email student harus menggunakan @student.hogwarts.ac.id'])->withInput();
        }
        if ($request->role === 'guru' && !str_ends_with($request->email, '@hogwarts.ac.id')) {
            return back()->withErrors(['email' => 'Email guru harus menggunakan @hogwarts.ac.id'])->withInput();
        }

        $newUser = [
            'id'         => 'u' . (count($users) + 1) . '_' . time(),
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => password_hash($request->password, PASSWORD_BCRYPT), // [Adzkia] hash
            'role'       => $request->role,
            'level'      => $request->role === 'student' ? 1 : null,
            'xp'         => $request->role === 'student' ? 0 : null,
            'max_xp'     => $request->role === 'student' ? 100 : null,
            'created_at' => now()->toIso8601String(),
        ];

        $users[] = $newUser;
        $json->write('users', $users);

        // Auto-generate rapor kosong untuk student baru
        if ($newUser['role'] === 'student') {
            RaporController::generateForStudent($newUser['id'], $json);
            session(['user_id' => $newUser['id'], 'user_name' => $newUser['name'], 'user_email' => $newUser['email'], 'user_role' => $newUser['role']]);
            return redirect()->route('sorting-hat.questions');
        }

        session([
            'user_id'    => $newUser['id'],
            'user_name'  => $newUser['name'],
            'user_email' => $newUser['email'],
            'user_role'  => $newUser['role'],
            'user_house' => null,
        ]);

        return $this->redirectByRole($newUser['role']);
    }

    // [Khaula] Logout & hapus session
    public function logout()
    {
        session()->flush();
        session()->regenerate();
        return redirect()->route('login');
    }

    // [Sefina] Redirect berdasarkan role
    private function redirectByRole($role)
    {
        return $role === 'guru'
            ? redirect()->route('guru.dashboard')
            : redirect()->route('student.dashboard');
    }
}
