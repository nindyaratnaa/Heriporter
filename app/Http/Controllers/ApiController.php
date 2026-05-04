<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonService;
use App\Services\JwtService;

class ApiController extends Controller
{
    // Auth [Khaula]

    // POST /api/auth/login - public
    public function login(Request $request, JsonService $json, JwtService $jwt)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);

        $user = collect($json->read('users'))->firstWhere('email', $request->email);

        if (!$user || !password_verify($request->password, $user['password'])) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        return response()->json([
            'token' => $jwt->generate($user),
            'user'  => $this->safeUser($user),
        ]);
    }

    // POST /api/auth/register - public
    public function register(Request $request, JsonService $json, JwtService $jwt)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email',
            'password' => 'required|min:6',
            'role'     => 'required|in:student,guru',
        ]);

        $users = $json->read('users');
        if (collect($users)->firstWhere('email', $request->email)) {
            return response()->json(['message' => 'Email sudah terdaftar.'], 422);
        }

        if ($request->role === 'student' && !str_ends_with($request->email, '@student.hogwarts.ac.id')) {
            return response()->json(['message' => 'Email student harus @student.hogwarts.ac.id'], 422);
        }
        if ($request->role === 'guru' && !str_ends_with($request->email, '@hogwarts.ac.id')) {
            return response()->json(['message' => 'Email guru harus @hogwarts.ac.id'], 422);
        }

        $newUser = [
            'id'         => 'u' . (count($users) + 1) . '_' . time(),
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => password_hash($request->password, PASSWORD_BCRYPT),
            'role'       => $request->role,
            'level'      => $request->role === 'student' ? 1 : null,
            'xp'         => $request->role === 'student' ? 0 : null,
            'max_xp'     => $request->role === 'student' ? 100 : null,
            'house'      => null,
            'wand_id'    => null,
            'created_at' => now()->toIso8601String(),
        ];

        $users[] = $newUser;
        $json->write('users', $users);

        if ($newUser['role'] === 'student') {
            RaporController::generateForStudent($newUser['id'], $json);
        }

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'token'   => $jwt->generate($newUser),
            'user'    => $this->safeUser($newUser),
        ], 201);
    }

    // GET /api/auth/me - keduanya
    public function me(Request $request, JsonService $json)
    {
        $user = collect($json->read('users'))->firstWhere('id', $request->user->sub);
        abort_if(!$user, 404);
        return response()->json($this->safeUser($user));
    }

    // Users [Khaula]

    // GET /api/users - guru only
    public function users(Request $request, JsonService $json)
    {
        $this->requireRole($request, 'guru');
        $users = collect($json->read('users'))->where('role', 'student')->map(fn($u) => $this->safeUser($u))->values();
        return response()->json($users);
    }

    // GET /api/users/{student_name} - guru only
    public function userShow(Request $request, JsonService $json, string $student_name)
    {
        $this->requireRole($request, 'guru');
        $user = collect($json->read('users'))->first(
            fn($u) => strtolower(str_replace(' ', '-', $u['name'])) === strtolower($student_name) && $u['role'] === 'student'
        );
        if (!$user) return response()->json(['message' => 'Siswa tidak ditemukan.'], 404);
        return response()->json($this->safeUser($user));
    }

    // DELETE /api/account - student only
    public function deleteAccount(Request $request, JsonService $json)
    {
        $this->requireRole($request, 'student');
        $request->validate(['password' => 'required']);

        $userId = $request->user->sub;
        $user   = collect($json->read('users'))->firstWhere('id', $userId);

        if (!$user || !password_verify($request->password, $user['password'])) {
            return response()->json(['message' => 'Password salah.'], 401);
        }

        $users = collect($json->read('users'))->reject(fn($u) => $u['id'] === $userId)->values()->all();
        $json->write('users', $users);

        return response()->json(['message' => 'Akun berhasil dihapus.']);
    }

    // Potions [Nya]

    // GET /api/potions - keduanya
    public function potions(Request $request, JsonService $json)
    {
        $potions = collect($json->read('potions'));
        if ($request->user->role === 'student') {
            $potions = $potions->where('student_id', $request->user->sub);
        }
        return response()->json($potions->values());
    }

    // GET /api/potions/{potion_name} - keduanya
    public function potionShow(Request $request, JsonService $json, string $potion_name)
    {
        $potion = collect($json->read('potions'))->first(
            fn($p) => strtolower(str_replace(' ', '-', $p['name'])) === strtolower($potion_name)
        );
        if (!$potion) return response()->json(['message' => 'Ramuan tidak ditemukan.'], 404);

        if ($request->user->role === 'student' && $potion['student_id'] !== $request->user->sub) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($potion);
    }

    // POST /api/potions - student only
    public function potionStore(Request $request, JsonService $json)
    {
        $this->requireRole($request, 'student');
        $request->validate([
            'name'              => 'required|string',
            'description'       => 'required|string',
            'ingredients'       => 'required|array',
            'cara_pembuatan'    => 'required|string',
            'tingkat_kesulitan' => 'required|in:Easy,Medium,Hard',
            'durasi_efek'       => 'required|string',
            'warna_ramuan'      => 'required|string',
            'efek_samping'      => 'required|string',
            'kelemahan'         => 'required|string',
        ]);

        $potions   = $json->read('potions');
        $newPotion = [
            'id'                => 'p' . (count($potions) + 1) . '_' . time(),
            'student_id'        => $request->user->sub,
            'name'              => $request->name,
            'description'       => $request->description,
            'ingredients'       => $request->ingredients,
            'cara_pembuatan'    => $request->cara_pembuatan,
            'tingkat_kesulitan' => $request->tingkat_kesulitan,
            'durasi_efek'       => $request->durasi_efek,
            'warna_ramuan'      => $request->warna_ramuan,
            'efek_samping'      => $request->efek_samping,
            'kelemahan'         => $request->kelemahan,
            'image'             => null,
            'status'            => 'pending',
            'rating'            => null,
            'guru_comment'      => null,
            'validated_by'      => null,
            'created_at'        => now()->toIso8601String(),
            'validated_at'      => null,
        ];
        $potions[] = $newPotion;
        $json->write('potions', $potions);

        return response()->json($newPotion, 201);
    }

    // DELETE /api/potions/{potion_name} - student only
    public function potionDestroy(Request $request, JsonService $json, string $potion_name)
    {
        $this->requireRole($request, 'student');
        $potions = $json->read('potions');
        $potion  = collect($potions)->first(
            fn($p) => strtolower(str_replace(' ', '-', $p['name'])) === strtolower($potion_name)
        );

        if (!$potion || $potion['student_id'] !== $request->user->sub) {
            return response()->json(['message' => 'Tidak ditemukan.'], 404);
        }
        if ($potion['status'] !== 'pending') {
            return response()->json(['message' => 'Hanya ramuan pending yang bisa dihapus.'], 422);
        }

        $json->write('potions', collect($potions)->reject(fn($p) => $p['id'] === $potion['id'])->values()->all());
        return response()->json(['message' => 'Ramuan dihapus.']);
    }

    // POST /api/potions/{potion_name}/validate - guru only
    public function potionValidate(Request $request, JsonService $json, string $potion_name)
    {
        $this->requireRole($request, 'guru');
        $request->validate([
            'status'       => 'required|in:approved,rejected',
            'rating'       => 'nullable|integer|min:1|max:10',
            'guru_comment' => 'nullable|string',
        ]);

        $potions = $json->read('potions');
        $found   = false;
        $potions = collect($potions)->map(function ($p) use ($potion_name, $request, &$found) {
            if (strtolower(str_replace(' ', '-', $p['name'])) === strtolower($potion_name)) {
                $found = true;
                $p['status']       = $request->status;
                $p['rating']       = $request->rating;
                $p['guru_comment'] = $request->guru_comment;
                $p['validated_by'] = $request->user->sub;
                $p['validated_at'] = now()->toIso8601String();
            }
            return $p;
        })->all();

        if (!$found) return response()->json(['message' => 'Tidak ditemukan.'], 404);

        $json->write('potions', $potions);
        return response()->json(['message' => 'Validasi berhasil.']);
    }

    // Inventory [Sefina]

    // GET /api/inventory - student only
    public function inventory(Request $request, JsonService $json)
    {
        $this->requireRole($request, 'student');
        $inv = collect($json->read('potions'))
            ->where('student_id', $request->user->sub)
            ->where('status', 'approved')
            ->values();
        return response()->json($inv);
    }

    // DELETE /api/inventory/{potion_name} - student only
    public function inventoryDestroy(Request $request, JsonService $json, string $potion_name)
    {
        $this->requireRole($request, 'student');
        $potions = $json->read('potions');
        $potion  = collect($potions)->first(
            fn($p) => strtolower(str_replace(' ', '-', $p['name'])) === strtolower($potion_name)
        );

        if (!$potion || $potion['student_id'] !== $request->user->sub) {
            return response()->json(['message' => 'Tidak ditemukan.'], 404);
        }

        $json->write('potions', collect($potions)->reject(fn($p) => $p['id'] === $potion['id'])->values()->all());
        return response()->json(['message' => 'Dihapus dari inventori.']);
    }

    // Raport [Adzkia]

    // semua role
    // GET /api/rapor
    public function rapor(Request $request, JsonService $json)
    {
        $rapors = collect($json->read('rapor'));
        $users  = collect($json->read('users'));

        if ($request->user->role === 'student') {
            $rapors = $rapors->where('student_id', $request->user->sub);
        }

        $result = $rapors->values()->map(function ($r) use ($users) {
            $student = $users->firstWhere('id', $r['student_id']);
            $r['student_name'] = $student['name'] ?? $r['student_id'];
            return $r;
        });

        return response()->json($result);
    }

    // student only
    // GET /api/rapor/{semester}
    public function raporShow(Request $request, JsonService $json, string $semester)
    {
        $this->requireRole($request, 'student');

        $semesterLabel = 'Semester ' . $semester;
        $rapor = collect($json->read('rapor'))->first(
            fn($r) => $r['student_id'] === $request->user->sub && $r['semester'] === $semesterLabel
        );

        if (!$rapor) return response()->json(['message' => 'Rapor tidak ditemukan.'], 404);

        $student = collect($json->read('users'))->firstWhere('id', $request->user->sub);
        $rapor['student_name'] = $student['name'] ?? $request->user->sub;

        return response()->json($rapor);
    }

    // guru only
    // GET /api/rapor/{student_name}/{semester}
    public function raporByNameSemester(Request $request, JsonService $json, string $name, string $semester)
    {
        $this->requireRole($request, 'guru');

        $users   = collect($json->read('users'));
        $student = $users->first(
            fn($u) => strtolower(str_replace(' ', '-', $u['name'])) === strtolower($name) && $u['role'] === 'student'
        );

        if (!$student) return response()->json(['message' => 'Siswa tidak ditemukan.'], 404);

        $semesterLabel = 'Semester ' . $semester;
        $rapor = collect($json->read('rapor'))->first(
            fn($r) => $r['student_id'] === $student['id'] && $r['semester'] === $semesterLabel
        );

        if (!$rapor) return response()->json(['message' => 'Rapor tidak ditemukan.'], 404);

        $rapor['student_name'] = $student['name'];
        return response()->json($rapor);
    }

    // PUT /api/rapor/{student_name}/{semester}
    public function raporUpdate(Request $request, JsonService $json, string $name, string $semester)
    {
        $this->requireRole($request, 'guru');
        $request->validate([
            'mata_pelajaran'         => 'required|array',
            'mata_pelajaran.*.nilai' => 'required|integer|min:0|max:100',
            'catatan'                => 'nullable|string',
        ]);

        $users   = collect($json->read('users'));
        $student = $users->first(
            fn($u) => strtolower(str_replace(' ', '-', $u['name'])) === strtolower($name) && $u['role'] === 'student'
        );
        if (!$student) return response()->json(['message' => 'Siswa tidak ditemukan.'], 404);

        $semesterLabel = 'Semester ' . $semester;
        $rapors = $json->read('rapor');
        $found  = false;
        $rapors = collect($rapors)->map(function ($r) use ($student, $semesterLabel, $request, &$found) {
            if ($r['student_id'] === $student['id'] && $r['semester'] === $semesterLabel) {
                $found = true;
                $mp = $r['mata_pelajaran'];
                foreach ($request->mata_pelajaran as $i => $input) {
                    if (isset($mp[$i])) {
                        $n = (int) $input['nilai'];
                        $mp[$i]['nilai']      = $n;
                        $mp[$i]['nilai_huruf'] = $n > 0 ? RaporController::nilaiHuruf($n) : '-';
                        $mp[$i]['keterangan']  = $n >= 85 ? 'Sangat Baik' : ($n >= 70 ? 'Baik' : ($n >= 55 ? 'Cukup' : ($n > 0 ? 'Kurang' : '-')));
                    }
                }
                $r['mata_pelajaran'] = $mp;
                $r['catatan']        = $request->catatan;
                $r['updated_by']     = $request->user->sub;
                $r['updated_at']     = now()->toIso8601String();
            }
            return $r;
        })->all();

        if (!$found) return response()->json(['message' => 'Rapor tidak ditemukan.'], 404);

        $json->write('rapor', $rapors);
        return response()->json(['message' => 'Raport diperbarui.']);
    }

    // PATCH /api/rapor/{student_name}/{semester}
    public function raporPatch(Request $request, JsonService $json, string $student_name, string $semester)
    {
        $this->requireRole($request, 'guru');
        $request->validate([
            'mata_pelajaran'         => 'required|array',
            'mata_pelajaran.*.nilai' => 'required|integer|min:0|max:100',
            'catatan'                => 'nullable|string',
        ]);

        $users   = collect($json->read('users'));
        $student = $users->first(
            fn($u) => strtolower(str_replace(' ', '-', $u['name'])) === strtolower($student_name) && $u['role'] === 'student'
        );
        if (!$student) return response()->json(['message' => 'Siswa tidak ditemukan.'], 404);

        $semesterLabel = 'Semester ' . $semester;
        $rapors = $json->read('rapor');
        $found  = false;
        $rapors = collect($rapors)->map(function ($r) use ($student, $semesterLabel, $request, &$found) {
            if ($r['student_id'] === $student['id'] && $r['semester'] === $semesterLabel) {
                $found = true;
                $mp = $r['mata_pelajaran'];
                foreach ($request->mata_pelajaran as $i => $input) {
                    if (isset($mp[$i]) && isset($input['nilai'])) {
                        $n = (int) $input['nilai'];
                        $mp[$i]['nilai']      = $n;
                        $mp[$i]['nilai_huruf'] = $n > 0 ? RaporController::nilaiHuruf($n) : '-';
                        $mp[$i]['keterangan']  = $n >= 85 ? 'Sangat Baik' : ($n >= 70 ? 'Baik' : ($n >= 55 ? 'Cukup' : ($n > 0 ? 'Kurang' : '-')));
                    }
                }
                $r['mata_pelajaran'] = $mp;
                if ($request->has('catatan')) $r['catatan'] = $request->catatan;
                $r['updated_by'] = $request->user->sub;
                $r['updated_at'] = now()->toIso8601String();
            }
            return $r;
        })->all();

        if (!$found) return response()->json(['message' => 'Rapor tidak ditemukan.'], 404);

        $json->write('rapor', $rapors);
        return response()->json(['message' => 'Raport berhasil diperbarui sebagian.']);
    }

    // Helpers

    private function safeUser(array $u): array
    {
        unset($u['password']);
        return $u;
    }

    private function requireRole(Request $request, string $role): void
    {
        if ($request->user->role !== $role) {
            abort(response()->json(['message' => 'Akses ditolak. Role ' . $role . ' diperlukan.'], 403));
        }
    }
}
