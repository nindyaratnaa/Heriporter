<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hogwarts Potion System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    @php
        $house = session('user_house');
        $role  = session('user_role');

        $themes = [
            'Gryffindor' => [
                'accent'     => '#c0392b',
                'accentDim'  => 'rgba(192,57,43,0.3)',
                'accentBg'   => 'rgba(192,57,43,0.07)',
                'sidebarBg'  => '#130808',
                'glow'       => 'rgba(192,57,43,0.5)',
                'btnText'    => '#fff',
            ],
            'Ravenclaw' => [
                'accent'     => '#2471a3',
                'accentDim'  => 'rgba(36,113,163,0.3)',
                'accentBg'   => 'rgba(36,113,163,0.07)',
                'sidebarBg'  => '#060d14',
                'glow'       => 'rgba(36,113,163,0.5)',
                'btnText'    => '#fff',
            ],
            'Hufflepuff' => [
                'accent'     => '#d4ac0d',
                'accentDim'  => 'rgba(212,172,13,0.3)',
                'accentBg'   => 'rgba(212,172,13,0.07)',
                'sidebarBg'  => '#131000',
                'glow'       => 'rgba(212,172,13,0.5)',
                'btnText'    => '#0d0d0f',
            ],
            'Slytherin' => [
                'accent'     => '#1e8449',
                'accentDim'  => 'rgba(30,132,73,0.3)',
                'accentBg'   => 'rgba(30,132,73,0.07)',
                'sidebarBg'  => '#060f09',
                'glow'       => 'rgba(30,132,73,0.5)',
                'btnText'    => '#fff',
            ],
            // Guru = castle copper
            '_guru' => [
                'accent'     => '#c8a96e',
                'accentDim'  => 'rgba(200,169,110,0.3)',
                'accentBg'   => 'rgba(200,169,110,0.07)',
                'sidebarBg'  => '#1a1410',
                'glow'       => 'rgba(200,169,110,0.4)',
                'btnText'    => '#0d0d0f',
            ],
        ];

        $t = $role === 'guru'
            ? $themes['_guru']
            : ($themes[$house] ?? $themes['_guru']);
        $mainBg = 'Teacher Panel bg.png';
        $mainGradient = $role === 'guru'
            ? 'linear-gradient(135deg, rgba(30,20,10,0.45) 0%, rgba(60,40,10,0.2) 100%)'
            : match($house) {
                'Gryffindor' => 'linear-gradient(135deg, rgba(100,0,0,0.4) 0%, rgba(180,30,30,0.15) 100%)',
                'Ravenclaw'  => 'linear-gradient(135deg, rgba(0,20,80,0.45) 0%, rgba(20,60,140,0.15) 100%)',
                'Hufflepuff' => 'linear-gradient(135deg, rgba(60,45,0,0.4) 0%, rgba(140,110,0,0.15) 100%)',
                'Slytherin'  => 'linear-gradient(135deg, rgba(0,40,10,0.45) 0%, rgba(10,80,30,0.15) 100%)',
                default      => 'linear-gradient(135deg, rgba(30,20,10,0.4) 0%, rgba(60,40,10,0.15) 100%)',
            };
        // Card bg per house
        $cardBg = $role === 'guru'
            ? 'rgba(26,20,16,0.82)'
            : match($house) {
                'Gryffindor' => 'rgba(30,8,8,0.82)',
                'Ravenclaw'  => 'rgba(6,13,28,0.82)',
                'Hufflepuff' => 'rgba(20,16,0,0.82)',
                'Slytherin'  => 'rgba(6,18,10,0.82)',
                default      => 'rgba(26,20,16,0.82)',
            };
    @endphp

    <style>
        .sidebar {
            background-image: url('/images/navbar bg.png');
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
            position: relative;
        }
        /* Uniform fade overlay seluruh sidebar — logo area ikut kena bg */
        .sidebar::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.72);
            z-index: 0;
            pointer-events: none;
        }
        .sidebar > * { position: relative; z-index: 1; }
        /* Hapus background bawaan sidebar-logo agar bg navbar tembus */
        .sidebar-logo {
            background: linear-gradient(180deg, rgba(0,0,0,0.82) 0%, {{ $t['accentBg'] }} 100%) !important;
            border-bottom-color: {{ $t['accentDim'] }} !important;
        }
        .sidebar-logo .logo-title { color: {{ $t['accent'] }} !important; }

        .nav-item:hover, .nav-item.active {
            color: {{ $t['accent'] }} !important;
            background-color: {{ $t['accentBg'] }} !important;
            border-left-color: {{ $t['accent'] }} !important;
        }
        .sidebar-footer { border-top-color: {{ $t['accentDim'] }} !important; }
        .user-name { color: {{ $t['accent'] }} !important; }
        .house-badge-sidebar {
            background: {{ $t['accentBg'] }} !important;
            border-color: {{ $t['accentDim'] }} !important;
            color: {{ $t['accent'] }} !important;
        }

        /* Logo glow sesuai house */
        .logo-img {
            filter: drop-shadow(0 0 10px {{ $t['glow'] }});
            transition: filter 0.3s;
        }
        .logo-img:hover { filter: drop-shadow(0 0 18px {{ $t['glow'] }}); }

        /* Global accent override */
        .page-title { color: {{ $t['accent'] }} !important; text-shadow: 0 0 20px {{ $t['glow'] }}; }
        .gold { color: {{ $t['accent'] }} !important; }
        .card-gold { border-color: {{ $t['accent'] }} !important; box-shadow: 3px 3px 0 {{ $t['accentDim'] }} !important; }
        .btn-gold {
            background: {{ $t['accent'] }} !important;
            color: {{ $t['btnText'] }} !important;
        }
        .btn-outline { border-color: {{ $t['accent'] }} !important; color: {{ $t['accent'] }} !important; }
        .btn-outline:hover { background: {{ $t['accentBg'] }} !important; }
        .form-label { color: {{ $t['accent'] }} !important; }
        .form-input:focus { border-color: {{ $t['accent'] }} !important; box-shadow: 0 0 0 2px {{ $t['accentBg'] }} !important; }
        .xp-bar { border-color: {{ $t['accentDim'] }} !important; }
        .xp-fill { background: linear-gradient(90deg, {{ $t['accent'] }}, {{ $t['accent'] }}cc) !important; }
        .pixel-table th { color: {{ $t['accent'] }} !important; border-bottom-color: {{ $t['accentDim'] }} !important; }
        body {
            background: #0d0d0f;
        }
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image: url('/images/{{ $mainBg }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            filter: blur(4px) brightness(0.28);
            transform: scale(1.06);
            z-index: -2;
        }
        body::after {
            content: '';
            position: fixed; inset: 0;
            background: {{ $mainGradient }};
            z-index: -1;
            pointer-events: none;
        }
        /* Layout wrapper */
        .layout {
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            padding: 32px;
            min-height: 100vh;
            background: rgba(8,6,4,0.65);
            overflow-x: hidden;
        }
        /* Cards readable against bg */
        .card {
            background-color: {{ $cardBg }} !important;
            backdrop-filter: blur(8px);
            border-color: {{ $t['accentDim'] }} !important;
        }
        .card-gold {
            background-color: {{ $cardBg }} !important;
            backdrop-filter: blur(8px);
        }
        .btn-logout { border-color: {{ $t['accentDim'] }} !important; color: {{ $t['accentDim'] }} !important; }
        .btn-logout:hover { border-color: #c0392b !important; color: #c0392b !important; background: rgba(192,57,43,0.08) !important; }
    </style>
</head>
<body>
    <!-- Hamburger -->
    <button class="hamburger" id="hamburger" onclick="toggleSidebar()" aria-label="Menu">
        <i class="fas fa-bars" id="hamburgerIcon"></i>
    </button>
    <!-- Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    <div class="layout">
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/Hogwarts.png') }}" alt="Hogwarts"
                 class="logo-img"
                 style="width:52px; height:52px; object-fit:contain; display:block; margin:0 auto 8px;">
            <div class="logo-title">HOGWARTS</div>
            <div class="logo-sub">Potion System</div>
        </div>

        <nav class="sidebar-nav">
            @if(session('user_role') === 'student')
                <div class="nav-section">STUDENT</div>
                <a href="{{ route('student.dashboard') }}" class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="{{ route('student.potions.index') }}" class="nav-item {{ request()->routeIs('student.potions.*') ? 'active' : '' }}">
                    <i class="fas fa-flask"></i> Racik Ramuan
                </a>
                <a href="{{ route('student.inventory') }}" class="nav-item {{ request()->routeIs('student.inventory') ? 'active' : '' }}">
                    <i class="fas fa-box-open"></i> Inventori
                </a>
                <a href="{{ route('student.rapor') }}" class="nav-item {{ request()->routeIs('student.rapor') ? 'active' : '' }}">
                    <i class="fas fa-scroll"></i> Raport
                </a>
                <a href="{{ route('student.profile') }}" class="nav-item {{ request()->routeIs('student.profile') ? 'active' : '' }}">
                    <i class="fas fa-user"></i> Profil
                </a>
            @else
                <div class="nav-section">GURU</div>
                <a href="{{ route('guru.dashboard') }}" class="nav-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="{{ route('guru.potions') }}" class="nav-item {{ request()->routeIs('guru.potions*') ? 'active' : '' }}">
                    <i class="fas fa-check-square"></i> Validasi Ramuan
                </a>
                <a href="{{ route('guru.rapor') }}" class="nav-item {{ request()->routeIs('guru.rapor*') ? 'active' : '' }}">
                    <i class="fas fa-scroll"></i> Raport
                </a>
                <a href="{{ route('guru.users') }}" class="nav-item {{ request()->routeIs('guru.users') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Data Murid
                </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar" style="margin-right:10px;">
                    @if(session('user_role') === 'guru')
                        <img src="{{ asset('images/Hogwarts.png') }}" style="width:28px;height:28px;object-fit:contain;">
                    @elseif(session('user_house'))
                        @php $houseImgs=['Gryffindor'=>'gryffindor.png','Ravenclaw'=>'ravenclaw.png','Hufflepuff'=>'Hufflepuff.png','Slytherin'=>'Slytherin.png']; @endphp
                        <img src="{{ asset('images/'.($houseImgs[session('user_house')] ?? 'Hogwarts.png')) }}" style="width:28px;height:28px;object-fit:contain;">
                    @else
                        🧑🎓
                    @endif
                </div>
                <div>
                    <div class="user-name">{{ session('user_name') }}</div>
                    <div class="user-role">{{ strtoupper(session('user_role')) }}</div>
                    @if(session('user_house'))
                        <div class="house-badge-sidebar">{{ session('user_house') }}</div>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> LOGOUT
                </button>
            </form>

            {{-- Delete Account --}}
            <button onclick="document.getElementById('deleteModal').style.display='flex'"
                style="width:100%; background:transparent; border:1px solid rgba(192,57,43,0.3); color:rgba(192,57,43,0.6); padding:7px; font-size:11px; cursor:pointer; font-family:'Courier New',monospace; margin-top:6px; transition:all 0.2s;"
                onmouseover="this.style.borderColor='#c0392b';this.style.color='#c0392b'"
                onmouseout="this.style.borderColor='rgba(192,57,43,0.3)';this.style.color='rgba(192,57,43,0.6)'">
                <i class="fas fa-user-times"></i> HAPUS AKUN
            </button>
        </div>
    </div>{{-- sidebar --}}

    <div class="main-content">
        @if(session('success'))
            <div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
            @endforeach
        @endif

        @yield('content')
    </div>{{-- main-content --}}
    </div>{{-- layout --}}

{{-- Delete Account Modal --}}
<div id="deleteModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.75); z-index:999; align-items:center; justify-content:center;">
    <div style="background:#1a1410; border:1px solid rgba(192,57,43,0.4); padding:32px; max-width:380px; width:90%; position:relative;">
        <div style="color:#e07060; font-size:14px; font-weight:bold; letter-spacing:2px; margin-bottom:8px;">⚠️ HAPUS AKUN</div>
        <p style="color:#8a7d68; font-size:12px; line-height:1.6; margin-bottom:20px;">
            Akun kamu akan dihapus permanen. Semua data ramuan dan raport akan ikut terhapus.<br>
            Masukkan password untuk konfirmasi.
        </p>

        @if(session('delete_error'))
            <div style="background:rgba(192,57,43,0.12); border-left:3px solid #c0392b; color:#e07060; padding:8px 12px; font-size:12px; margin-bottom:14px;">
                {{ session('delete_error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('student.account.delete') }}">
            @csrf @method('DELETE')
            <label for="deletePassword" style="display:block; color:#c0392b; font-size:11px; font-weight:bold; letter-spacing:2px; margin-bottom:6px;">KONFIRMASI PASSWORD</label>
            <input type="password" name="password" id="deletePassword"
                   style="width:100%; padding:10px 12px; background:rgba(0,0,0,0.5); border:1px solid rgba(192,57,43,0.3); color:#d4c9b0; font-size:13px; font-family:'Courier New',monospace; margin-bottom:16px;"
                   placeholder="Masukkan password kamu" autocomplete="current-password" required>
            <div style="display:flex; gap:10px;">
                <button type="submit"
                    style="flex:1; background:#c0392b; color:#fff; font-weight:bold; padding:10px; border:none; font-size:12px; cursor:pointer; font-family:'Courier New',monospace; letter-spacing:1px;">
                    <i class="fas fa-trash"></i> HAPUS PERMANEN
                </button>
                <button type="button" onclick="document.getElementById('deleteModal').style.display='none'"
                    style="flex:1; background:transparent; border:1px solid #3d3228; color:#8a7d68; padding:10px; font-size:12px; cursor:pointer; font-family:'Courier New',monospace;">
                    BATAL
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const icon    = document.getElementById('hamburgerIcon');
    const isOpen  = sidebar.classList.toggle('open');
    overlay.classList.toggle('active', isOpen);
    icon.className = isOpen ? 'fas fa-times' : 'fas fa-bars';
}
// Close sidebar on nav click (mobile)
document.querySelectorAll('.nav-item').forEach(el => {
    el.addEventListener('click', () => {
        if (window.innerWidth <= 768) toggleSidebar();
    });
});
</script>
</body>
</html>
