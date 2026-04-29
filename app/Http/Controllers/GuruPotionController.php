<?php
// [Nya] Validasi Ramuan oleh Guru

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonService;

class GuruPotionController extends Controller
{
    public function index(JsonService $json)
    {
        $potions = $json->read('potions');
        $users   = $json->read('users');

        $queue = collect($potions)->map(function ($p) use ($users) {
            $p['student'] = collect($users)->firstWhere('id', $p['student_id']);
            return $p;
        })->sortByDesc('created_at')->values()->all();

        $pending  = collect($queue)->where('status', 'pending')->count();
        $approved = collect($queue)->where('status', 'approved')->count();
        $rejected = collect($queue)->where('status', 'rejected')->count();

        return view('guru.potions.index', compact('queue', 'pending', 'approved', 'rejected'));
    }

    public function show(string $id, JsonService $json)
    {
        $potion = collect($json->read('potions'))->firstWhere('id', $id);
        abort_if(!$potion, 404);

        $student = collect($json->read('users'))->firstWhere('id', $potion['student_id']);
        return view('guru.potions.show', compact('potion', 'student'));
    }

    public function validate(Request $request, string $id, JsonService $json)
    {
        $request->validate([
            'status'       => 'required|in:approved,rejected',
            'rating'       => 'required_if:status,approved|nullable|integer|min:1|max:10',
            'guru_comment' => 'nullable|string|max:500',
        ]);

        $potions = $json->read('potions');
        $potions = collect($potions)->map(function ($p) use ($id, $request) {
            if ($p['id'] === $id) {
                $p['status']       = $request->status;
                $p['rating']       = $request->rating ? (int) $request->rating : null;
                $p['guru_comment'] = $request->guru_comment;
                $p['validated_by'] = session('user_id');
                $p['validated_at'] = now()->toIso8601String();
            }
            return $p;
        })->all();

        $json->write('potions', $potions);

        // Update student XP if approved
        if ($request->status === 'approved') {
            $potion  = collect($potions)->firstWhere('id', $id);
            $users   = $json->read('users');
            $users   = collect($users)->map(function ($u) use ($potion) {
                if ($u['id'] === $potion['student_id'] && $u['role'] === 'student') {
                    $u['xp'] = min(($u['xp'] ?? 0) + 50, $u['max_xp'] ?? 100);
                }
                return $u;
            })->all();
            $json->write('users', $users);
        }

        return redirect()->route('guru.potions')->with('success', 'Ramuan berhasil divalidasi.');
    }
}
