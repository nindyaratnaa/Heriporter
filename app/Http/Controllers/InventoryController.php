<?php
// [Sefina] Layanan Inventori Ramuan

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonService;

class InventoryController extends Controller
{
    // [Sefina] Read - Tampilkan ramuan approved milik student
    public function index(JsonService $json)
    {
        $inventory = collect($json->read('potions'))
            ->where('student_id', session('user_id'))
            ->where('status', 'approved')
            ->values()->all();

        return view('student.inventory', compact('inventory'));
    }

    // [Sefina] Delete - Hapus ramuan dari inventori milik student
    public function destroy(string $id, JsonService $json)
    {
        $potions = $json->read('potions');
        $potion  = collect($potions)->firstWhere('id', $id);

        abort_if(!$potion || $potion['student_id'] !== session('user_id'), 404);

        $potions = collect($potions)->reject(fn($p) => $p['id'] === $id)->values()->all();
        $json->write('potions', $potions);

        return redirect()->route('student.inventory')->with('success', 'Ramuan dihapus dari inventori.');
    }
}
