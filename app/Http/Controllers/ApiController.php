<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonService;
use App\Services\JwtService;

class ApiController extends Controller
{
    // ─── AUTH ────────────────────────────────────────────────────────────────

    // POST /api/auth/login
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

    // POST /api/auth/register
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

    // GET /api/auth/me
    public function me(Request $request, JsonService $json)
    {
        $user = collect($json->read('users'))->firstWhere('id', $request->user->sub);
        abort_if(!$user, 404);
        return response()->json($this->safeUser($user));
    }

    // ─── USERS (Guru only) ───────────────────────────────────────────────────

    // GET /api/users
    public function users(Request $request, JsonService $json)
    {
        $this->requireRole($request, 'guru');
        $users = collect($json->read('users'))->where('role', 'student')->map(fn($u) => $this->safeUser($u))->values();
        return response()->json($users);
    }

    // GET /api/users/{id}
    public function userShow(Request $request, JsonService $json, string $id)
    {
        $this->requireRole($request, 'guru');
        $user = collect($json->read('users'))->firstWhere('id', $id);
        abort_if(!$user, 404);
        return response()->json($this->safeUser($user));
    }

    // ─── POTIONS (Racik Ramuan - Nya) ────────────────────────────────────────

    // GET /api/potions
    public function potions(Request $request, JsonService $json)
    {
        $role = $request->user->role;
        $potions = collect($json->read('potions'));

        if ($role === 'student') {
            $potions = $potions->where('student_id', $request->user->sub);
        }

        return response()->json($potions->values());
    }

    // GET /api/potions/{id}
    public function potionShow(Request $request, JsonService $json, string $id)
    {
        $potion = collect($json->read('potions'))->firstWhere('id', $id);
        abort_if(!$potion, 404);

        if ($request->user->role === 'student' && $potion['student_id'] !== $request->user->sub) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($potion);
    }

    // POST /api/potions
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

    // DELETE /api/potions/{id}
    public function potionDestroy(Request $request, JsonService $json, string $id)
    {
        $this->requireRole($request, 'student');
        $potions = $json->read('potions');
        $potion  = collect($potions)->firstWhere('id', $id);

        if (!$potion || $potion['student_id'] !== $request->user->sub) {
            return response()->json(['message' => 'Tidak ditemukan.'], 404);
        }
        if ($potion['status'] !== 'pending') {
            return response()->json(['message' => 'Hanya ramuan pending yang bisa dihapus.'], 422);
        }

        $json->write('potions', collect($potions)->reject(fn($p) => $p['id'] === $id)->values()->all());
        return response()->json(['message' => 'Ramuan dihapus.']);
    }

    // POST /api/potions/{id}/validate  (Guru)
    public function potionValidate(Request $request, JsonService $json, string $id)
    {
        $this->requireRole($request, 'guru');
        $request->validate([
            'status'       => 'required|in:approved,rejected',
            'rating'       => 'nullable|integer|min:1|max:10',
            'guru_comment' => 'nullable|string',
        ]);

        $potions = $json->read('potions');
        $found   = false;
        $potions = collect($potions)->map(function ($p) use ($id, $request, &$found) {
            if ($p['id'] === $id) {
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

    // ─── INVENTORY (Sefina) ──────────────────────────────────────────────────

    // GET /api/inventory
    public function inventory(Request $request, JsonService $json)
    {
        $this->requireRole($request, 'student');
        $inv = collect($json->read('potions'))
            ->where('student_id', $request->user->sub)
            ->where('status', 'approved')
            ->values();
        return response()->json($inv);
    }

    // DELETE /api/inventory/{id}
    public function inventoryDestroy(Request $request, JsonService $json, string $id)
    {
        $this->requireRole($request, 'student');
        $potions = $json->read('potions');
        $potion  = collect($potions)->firstWhere('id', $id);

        if (!$potion || $potion['student_id'] !== $request->user->sub) {
            return response()->json(['message' => 'Tidak ditemukan.'], 404);
        }

        $json->write('potions', collect($potions)->reject(fn($p) => $p['id'] === $id)->values()->all());
        return response()->json(['message' => 'Dihapus dari inventori.']);
    }

    // ─── RAPORT (Adzkia) ─────────────────────────────────────────────────────

    // GET /api/rapor
    public function rapor(Request $request, JsonService $json)
    {
        $role   = $request->user->role;
        $rapors = collect($json->read('rapor'));

        if ($role === 'student') {
            $rapors = $rapors->where('student_id', $request->user->sub);
        }

        return response()->json($rapors->values());
    }

    // GET /api/rapor/{id}
    public function raporShow(Request $request, JsonService $json, string $id)
    {
        $rapor = collect($json->read('rapor'))->firstWhere('id', $id);
        abort_if(!$rapor, 404);

        if ($request->user->role === 'student' && $rapor['student_id'] !== $request->user->sub) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($rapor);
    }

    // PUT /api/rapor/{id}  (Guru)
    public function raporUpdate(Request $request, JsonService $json, string $id)
    {
        $this->requireRole($request, 'guru');
        $request->validate([
            'mata_pelajaran'              => 'required|array',
            'mata_pelajaran.*.nilai'      => 'required|integer|min:0|max:100',
            'mata_pelajaran.*.keterangan' => 'required|string',
            'catatan'                     => 'nullable|string',
        ]);

        $rapors = $json->read('rapor');
        $rapors = collect($rapors)->map(function ($r) use ($id, $request) {
            if ($r['id'] === $id) {
                $mp = $r['mata_pelajaran'];
                foreach ($request->mata_pelajaran as $i => $input) {
                    if (isset($mp[$i])) {
                        $n = (int) $input['nilai'];
                        $mp[$i]['nilai']       = $n;
                        $mp[$i]['nilai_huruf']  = $n > 0 ? RaporController::nilaiHuruf($n) : '-';
                        $mp[$i]['keterangan']  = $input['keterangan'];
                    }
                }
                $r['mata_pelajaran'] = $mp;
                $r['catatan']        = $request->catatan;
                $r['updated_by']     = $request->user->sub;
                $r['updated_at']     = now()->toIso8601String();
            }
            return $r;
        })->all();

        $json->write('rapor', $rapors);
        return response()->json(['message' => 'Raport diperbarui.']);
    }

    // ─── HELPERS ─────────────────────────────────────────────────────────────

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
