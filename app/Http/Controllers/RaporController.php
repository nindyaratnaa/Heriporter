<?php
// [Adzkia] Layanan Data Student (Raport)

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonService;

class RaporController extends Controller
{
    const SEMESTERS = [
        'Semester 1', 'Semester 2', 'Semester 3', 'Semester 4',
        'Semester 5', 'Semester 6', 'Semester 7', 'Semester 8',
    ];

    // Mapel berbeda tiap semester
    const MAPEL_PER_SEMESTER = [
        'Semester 1' => [
            ['nama' => 'Potion Making',            'guru_pengampu' => 'Prof. Snape'],
            ['nama' => 'Transfiguration',           'guru_pengampu' => 'Prof. McGonagall'],
            ['nama' => 'Herbology',                 'guru_pengampu' => 'Prof. Sprout'],
            ['nama' => 'Defense Against Dark Arts', 'guru_pengampu' => 'Prof. Lupin'],
            ['nama' => 'Charms',                    'guru_pengampu' => 'Prof. Flitwick'],
        ],
        'Semester 2' => [
            ['nama' => 'Potion Making',             'guru_pengampu' => 'Prof. Snape'],
            ['nama' => 'Transfiguration',            'guru_pengampu' => 'Prof. McGonagall'],
            ['nama' => 'History of Magic',           'guru_pengampu' => 'Prof. Binns'],
            ['nama' => 'Astronomy',                  'guru_pengampu' => 'Prof. Sinistra'],
            ['nama' => 'Flying',                     'guru_pengampu' => 'Madam Hooch'],
        ],
        'Semester 3' => [
            ['nama' => 'Potion Making',             'guru_pengampu' => 'Prof. Snape'],
            ['nama' => 'Care of Magical Creatures', 'guru_pengampu' => 'Prof. Hagrid'],
            ['nama' => 'Divination',                'guru_pengampu' => 'Prof. Trelawney'],
            ['nama' => 'Arithmancy',                'guru_pengampu' => 'Prof. Vector'],
            ['nama' => 'Muggle Studies',            'guru_pengampu' => 'Prof. Burbage'],
        ],
        'Semester 4' => [
            ['nama' => 'Advanced Potions',          'guru_pengampu' => 'Prof. Snape'],
            ['nama' => 'Transfiguration',            'guru_pengampu' => 'Prof. McGonagall'],
            ['nama' => 'Defense Against Dark Arts', 'guru_pengampu' => 'Prof. Moody'],
            ['nama' => 'Herbology',                 'guru_pengampu' => 'Prof. Sprout'],
            ['nama' => 'Ancient Runes',             'guru_pengampu' => 'Prof. Babbling'],
        ],
        'Semester 5' => [
            ['nama' => 'Advanced Potions',          'guru_pengampu' => 'Prof. Snape'],
            ['nama' => 'Charms',                    'guru_pengampu' => 'Prof. Flitwick'],
            ['nama' => 'Defense Against Dark Arts', 'guru_pengampu' => 'Prof. Umbridge'],
            ['nama' => 'Transfiguration',            'guru_pengampu' => 'Prof. McGonagall'],
            ['nama' => 'Divination',                'guru_pengampu' => 'Prof. Trelawney'],
        ],
        'Semester 6' => [
            ['nama' => 'Advanced Potions',          'guru_pengampu' => 'Prof. Slughorn'],
            ['nama' => 'Defense Against Dark Arts', 'guru_pengampu' => 'Prof. Snape'],
            ['nama' => 'Transfiguration',            'guru_pengampu' => 'Prof. McGonagall'],
            ['nama' => 'Charms',                    'guru_pengampu' => 'Prof. Flitwick'],
            ['nama' => 'Herbology',                 'guru_pengampu' => 'Prof. Sprout'],
        ],
        'Semester 7' => [
            ['nama' => 'Mastery of Potions',        'guru_pengampu' => 'Prof. Slughorn'],
            ['nama' => 'Dark Arts Defense',         'guru_pengampu' => 'Prof. Lupin'],
            ['nama' => 'Advanced Transfiguration',  'guru_pengampu' => 'Prof. McGonagall'],
            ['nama' => 'Magical Theory',            'guru_pengampu' => 'Prof. Dumbledore'],
            ['nama' => 'Alchemy',                   'guru_pengampu' => 'Prof. Flamel'],
        ],
        'Semester 8' => [
            ['nama' => 'Mastery of Potions',        'guru_pengampu' => 'Prof. Slughorn'],
            ['nama' => 'Advanced Charms',           'guru_pengampu' => 'Prof. Flitwick'],
            ['nama' => 'Magical Law',               'guru_pengampu' => 'Prof. Dumbledore'],
            ['nama' => 'Healing Magic',             'guru_pengampu' => 'Madam Pomfrey'],
            ['nama' => 'Alchemy',                   'guru_pengampu' => 'Prof. Flamel'],
        ],
    ];

    public static function nilaiHuruf(int $nilai): string
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 85) return 'A-';
        if ($nilai >= 80) return 'B+';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 70) return 'B-';
        if ($nilai >= 65) return 'C+';
        if ($nilai >= 60) return 'C';
        if ($nilai >= 55) return 'C-';
        if ($nilai >= 50) return 'D';
        return 'E';
    }

    public static function generateForStudent(string $studentId, JsonService $json): void
    {
        $rapors = $json->read('rapor');

        foreach (self::SEMESTERS as $semester) {
            $exists = collect($rapors)->first(fn($r) =>
                $r['student_id'] === $studentId && $r['semester'] === $semester
            );
            if ($exists) continue;

            $mapel = self::MAPEL_PER_SEMESTER[$semester];
            $rapors[] = [
                'id'             => 'r_' . $studentId . '_' . strtolower(str_replace([' ', ''], ['', ''], $semester)) . '_' . time(),
                'student_id'     => $studentId,
                'semester'       => $semester,
                'mata_pelajaran' => collect($mapel)->map(fn($mp) => [
                    'nama'          => $mp['nama'],
                    'nilai'         => 0,
                    'nilai_huruf'   => '-',
                    'guru_pengampu' => $mp['guru_pengampu'],
                    'keterangan'    => '-',
                ])->all(),
                'catatan'    => '',
                'updated_by' => null,
                'updated_at' => null,
            ];
        }

        $json->write('rapor', $rapors);
    }

    public function studentIndex(Request $request, JsonService $json)
    {
        $allRapor = collect($json->read('rapor'))
            ->where('student_id', session('user_id'))
            ->sortBy(fn($r) => (int) filter_var($r['semester'], FILTER_SANITIZE_NUMBER_INT))
            ->values();

        $selectedSemester = $request->get('semester', $allRapor->first()['semester'] ?? self::SEMESTERS[0]);
        $rapor = $allRapor->firstWhere('semester', $selectedSemester);

        $allNilai = $allRapor->flatMap(fn($r) => collect($r['mata_pelajaran'])->pluck('nilai'));
        $rataKeseluruhan = $allNilai->filter(fn($n) => $n > 0)->avg() ?? 0;

        return view('student.rapor', compact('allRapor', 'rapor', 'selectedSemester', 'rataKeseluruhan'));
    }

    public function guruIndex(JsonService $json)
    {
        $students = collect($json->read('users'))->where('role', 'student')->values()->all();
        return view('guru.rapor.index', compact('students'));
    }

    public function edit(Request $request, JsonService $json)
    {
        $studentId = $request->get('student_id');
        $semester  = $request->get('semester');
        abort_if(!$studentId || !$semester, 400);

        $student = collect($json->read('users'))->firstWhere('id', $studentId);
        abort_if(!$student, 404);

        $rapor = collect($json->read('rapor'))->first(
            fn($r) => $r['student_id'] === $studentId && $r['semester'] === $semester
        );

        if (!$rapor) {
            self::generateForStudent($studentId, $json);
            $rapor = collect($json->read('rapor'))->first(
                fn($r) => $r['student_id'] === $studentId && $r['semester'] === $semester
            );
        }

        $semesters = self::SEMESTERS;
        $students  = collect($json->read('users'))->where('role', 'student')->values()->all();

        return view('guru.rapor.edit', compact('rapor', 'student', 'semesters', 'students'));
    }

    public function update(Request $request, string $id, JsonService $json)
    {
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
                        $mp[$i]['nilai']      = $n;
                        $mp[$i]['nilai_huruf'] = $n > 0 ? self::nilaiHuruf($n) : '-';
                        $mp[$i]['keterangan'] = $input['keterangan'];
                    }
                }
                $r['mata_pelajaran'] = $mp;
                $r['catatan']        = $request->catatan;
                $r['updated_by']     = session('user_id');
                $r['updated_at']     = now()->toIso8601String();
            }
            return $r;
        })->all();

        $json->write('rapor', $rapors);
        return redirect()->route('guru.rapor')->with('success', 'Raport berhasil diperbarui.');
    }
}
