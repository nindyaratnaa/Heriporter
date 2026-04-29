<?php

namespace App\Http\Controllers;

use App\Services\JsonService;

class GuruDashboardController extends Controller
{
    public function index(JsonService $json)
    {
        $potions  = $json->read('potions');
        $students = collect($json->read('users'))->where('role', 'student');

        $stats = [
            'total_students' => $students->count(),
            'pending'        => collect($potions)->where('status', 'pending')->count(),
            'approved'       => collect($potions)->where('status', 'approved')->count(),
            'rejected'       => collect($potions)->where('status', 'rejected')->count(),
        ];

        return view('guru.dashboard', compact('stats'));
    }
}
