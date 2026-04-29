<?php
// [Nya] Layanan Racik Ramuan

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonService;

class PotionController extends Controller
{
    // [Nya] Read - Daftar ramuan milik student
    public function index(JsonService $json)
    {
        $potions = collect($json->read('potions'))
            ->where('student_id', session('user_id'))
            ->values()->all();

        return view('student.potions.index', compact('potions'));
    }

    // [Nya] Create - Form buat ramuan baru
    public function create()
    {
        return view('student.potions.create');
    }

    // [Nya] Create - Simpan ramuan baru (status: pending)
    public function store(Request $request, JsonService $json)
    {
        $request->validate([
            'name'              => 'required|string|max:100',
            'description'       => 'required|string',
            'ingredients'       => 'required|string',
            'cara_pembuatan'    => 'required|string',
            'tingkat_kesulitan' => 'required|in:Easy,Medium,Hard',
            'durasi_efek'       => 'required|string|max:50',
            'warna_ramuan'      => 'required|string|max:50',
            'efek_samping'      => 'required|string',
            'kelemahan'         => 'required|string',
            'image'             => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file      = $request->file('image');
            $filename  = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $imagePath = 'uploads/' . $filename;
        }

        $ingredients = array_filter(array_map('trim', explode(',', $request->ingredients)));

        $potions   = $json->read('potions');
        $potions[] = [
            'id'                => 'p' . (count($potions) + 1) . '_' . time(),
            'student_id'        => session('user_id'),
            'name'              => $request->name,
            'description'       => $request->description,
            'ingredients'       => array_values($ingredients),
            'cara_pembuatan'    => $request->cara_pembuatan,
            'tingkat_kesulitan' => $request->tingkat_kesulitan,
            'durasi_efek'       => $request->durasi_efek,
            'warna_ramuan'      => $request->warna_ramuan,
            'efek_samping'      => $request->efek_samping,
            'kelemahan'         => $request->kelemahan,
            'image'             => $imagePath,
            'status'            => 'pending',
            'rating'            => null,
            'guru_comment'      => null,
            'validated_by'      => null,
            'created_at'        => now()->toIso8601String(),
            'validated_at'      => null,
        ];
        $json->write('potions', $potions);

        return redirect()->route('student.potions.index')->with('success', 'Ramuan berhasil disubmit!');
    }

    // [Nya] Read - Detail ramuan milik student
    public function show(string $id, JsonService $json)
    {
        $potion = collect($json->read('potions'))->firstWhere('id', $id);
        abort_if(!$potion || $potion['student_id'] !== session('user_id'), 404);

        $guru = null;
        if ($potion['validated_by']) {
            $guru = collect($json->read('users'))->firstWhere('id', $potion['validated_by']);
        }

        return view('student.potions.show', compact('potion', 'guru'));
    }

    // [Nya] Delete - Hapus ramuan (hanya status pending)
    public function destroy(string $id, JsonService $json)
    {
        $potions = $json->read('potions');
        $potion  = collect($potions)->firstWhere('id', $id);

        abort_if(!$potion || $potion['student_id'] !== session('user_id'), 404);

        if ($potion['status'] !== 'pending') {
            return back()->withErrors(['error' => 'Hanya ramuan pending yang bisa dihapus.']);
        }

        $potions = collect($potions)->reject(fn($p) => $p['id'] === $id)->values()->all();
        $json->write('potions', $potions);

        return redirect()->route('student.potions.index')->with('success', 'Ramuan dihapus.');
    }
}
