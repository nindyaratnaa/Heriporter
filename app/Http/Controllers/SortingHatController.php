<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonService;

class SortingHatController extends Controller
{
    const QUESTIONS = [
        [
            'q'    => 'Ketika menghadapi tantangan besar, apa yang paling menggambarkan dirimu?',
            'opts' => [
                'a' => ['text' => 'Aku akan berjuang sampai akhir, apapun risikonya.', 'house' => 'Gryffindor'],
                'b' => ['text' => 'Aku akan mencari cara paling cerdas untuk menyelesaikannya.', 'house' => 'Ravenclaw'],
                'c' => ['text' => 'Aku akan memastikan semua orang di sekitarku aman terlebih dahulu.', 'house' => 'Hufflepuff'],
                'd' => ['text' => 'Aku akan menggunakan setiap sumber daya yang ada untuk menang.', 'house' => 'Slytherin'],
            ],
        ],
        [
            'q'    => 'Apa yang paling kamu hargai dalam hidup?',
            'opts' => [
                'a' => ['text' => 'Keberanian dan kehormatan.', 'house' => 'Gryffindor'],
                'b' => ['text' => 'Pengetahuan dan kebijaksanaan.', 'house' => 'Ravenclaw'],
                'c' => ['text' => 'Kesetiaan dan kerja keras.', 'house' => 'Hufflepuff'],
                'd' => ['text' => 'Ambisi dan pencapaian.', 'house' => 'Slytherin'],
            ],
        ],
        [
            'q'    => 'Teman-temanmu menggambarkanmu sebagai seseorang yang...',
            'opts' => [
                'a' => ['text' => 'Berani dan selalu siap membela yang benar.', 'house' => 'Gryffindor'],
                'b' => ['text' => 'Cerdas dan selalu punya jawaban untuk segalanya.', 'house' => 'Ravenclaw'],
                'c' => ['text' => 'Baik hati dan bisa diandalkan kapan saja.', 'house' => 'Hufflepuff'],
                'd' => ['text' => 'Penuh tekad dan tahu cara mencapai tujuan.', 'house' => 'Slytherin'],
            ],
        ],
    ];

    const HOUSES = [
        'Gryffindor' => ['color' => '#740001', 'accent' => '#D3A625', 'emoji' => '🦁', 'trait' => 'Keberanian, Keteguhan, Kehormatan'],
        'Ravenclaw'  => ['color' => '#0E1A40', 'accent' => '#946B2D', 'emoji' => '🦅', 'trait' => 'Kecerdasan, Kreativitas, Kebijaksanaan'],
        'Hufflepuff' => ['color' => '#372E29', 'accent' => '#F0C75E', 'emoji' => '🦡', 'trait' => 'Kesetiaan, Kesabaran, Kerja Keras'],
        'Slytherin'  => ['color' => '#1A472A', 'accent' => '#AAAAAA', 'emoji' => '🐍', 'trait' => 'Ambisi, Kecerdikan, Kepemimpinan'],
    ];

    public function questions()
    {
        // Already has house → skip to wand or dashboard
        $users = app(JsonService::class)->read('users');
        $user  = collect($users)->firstWhere('id', session('user_id'));
        if ($user && !empty($user['house'])) {
            return redirect()->route('student.dashboard');
        }
        return view('auth.sorting-hat', ['questions' => self::QUESTIONS]);
    }

    public function assign(Request $request, JsonService $json)
    {
        $answers = $request->input('answers', []);

        // Tally house votes
        $tally = ['Gryffindor' => 0, 'Ravenclaw' => 0, 'Hufflepuff' => 0, 'Slytherin' => 0];
        foreach (self::QUESTIONS as $i => $q) {
            $ans = $answers[$i] ?? null;
            if ($ans && isset($q['opts'][$ans])) {
                $tally[$q['opts'][$ans]['house']]++;
            }
        }

        // Pick house with most votes, tie → random
        $max   = max($tally);
        $top   = array_keys(array_filter($tally, fn($v) => $v === $max));
        $house = $top[array_rand($top)];

        // Assign random wand
        $wands = $json->read('wands');
        $wand  = $wands[array_rand($wands)];

        // Save to user
        $users = $json->read('users');
        $users = collect($users)->map(function ($u) use ($house, $wand) {
            if ($u['id'] === session('user_id')) {
                $u['house']   = $house;
                $u['wand_id'] = $wand['id'];
            }
            return $u;
        })->all();
        $json->write('users', $users);

        session(['user_house' => $house]);

        return redirect()->route('sorting-hat.result');
    }

    public function result(JsonService $json)
    {
        $users = $json->read('users');
        $user  = collect($users)->firstWhere('id', session('user_id'));

        abort_if(!$user || empty($user['house']), 404);

        $house     = $user['house'];
        $houseData = self::HOUSES[$house];
        $wand      = collect($json->read('wands'))->firstWhere('id', $user['wand_id'] ?? null);

        return view('auth.sorting-result', compact('user', 'house', 'houseData', 'wand'));
    }
}
