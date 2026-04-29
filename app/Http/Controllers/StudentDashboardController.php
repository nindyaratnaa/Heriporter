<?php

namespace App\Http\Controllers;

use App\Services\JsonService;

class StudentDashboardController extends Controller
{
    public function index(JsonService $json)
    {
        $user    = collect($json->read('users'))->firstWhere('id', session('user_id'));
        $potions = collect($json->read('potions'))->where('student_id', session('user_id'));

        $stats = [
            'total'    => $potions->count(),
            'approved' => $potions->where('status', 'approved')->count(),
            'pending'  => $potions->where('status', 'pending')->count(),
            'rejected' => $potions->where('status', 'rejected')->count(),
        ];

        $recent = $potions->sortByDesc('created_at')->take(3)->values()->all();

        return view('student.dashboard', compact('user', 'stats', 'recent'));
    }
}
