<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hogwarts Potion System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --copper: #c8a96e; --copper-dim: rgba(200,169,110,0.25);
            --parchment: #d4c9b0; --parchment-dim: #8a7d68; --candle: #e8d5a3;
            --stone-mid: #1a1410; --stone-border: rgba(200,169,110,0.2);
        }
        * { box-sizing:border-box; margin:0; padding:0; }

        body {
            background: #0d0d0f;
            font-family: 'Courier New', Courier, monospace;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            position: relative;
            overflow: hidden;
        }

        /* Blurred bg */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image: url('/images/Auth bg.png');
            background-size: cover; background-position: center;
            filter: blur(2px) brightness(0.45);
            transform: scale(1.04);
            z-index: 0;
        }
        /* + cross shadow pattern */
        body::after {
            content: '';
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(200,169,110,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(200,169,110,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 1;
            pointer-events: none;
        }

        .auth-wrap {
            position: relative; z-index: 2;
            display: flex; width: 100%; min-height: 100vh;
        }

        /* LEFT PANEL */
        .panel-left {
            flex: 1;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 60px 48px;
            background: linear-gradient(135deg,
                rgba(30,20,10,0.75) 0%,
                rgba(60,40,10,0.5) 50%,
                rgba(200,169,110,0.08) 100%);
            border-right: 1px solid rgba(200,169,110,0.15);
            text-align: center;
        }
        .panel-left img.hero-logo {
            width: 110px; height: 110px; object-fit: contain;
            filter: drop-shadow(0 0 24px rgba(200,169,110,0.6));
            margin-bottom: 28px;
            animation: float 4s ease-in-out infinite;
        }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .panel-left h1 {
            color: var(--candle); font-size: 28px; font-weight: bold;
            letter-spacing: 4px; margin-bottom: 8px;
        }
        .panel-left .subtitle {
            color: var(--copper); font-size: 13px; letter-spacing: 3px; margin-bottom: 24px;
        }
        .panel-left p {
            color: var(--parchment-dim); font-size: 13px; line-height: 1.8;
            max-width: 340px;
        }
        .divider-line {
            width: 60px; height: 1px;
            background: linear-gradient(90deg, transparent, var(--copper), transparent);
            margin: 24px auto;
        }
        .house-row {
            display: flex; gap: 16px; justify-content: center; margin-top: 28px;
        }
        .house-row img {
            width: 36px; height: 36px; object-fit: contain;
            opacity: 0.7; transition: all 0.2s;
            filter: drop-shadow(0 0 4px rgba(200,169,110,0.3));
        }
        .house-row img:hover { opacity: 1; transform: scale(1.15); }

        /* RIGHT PANEL */
        .panel-right {
            width: 420px; flex-shrink: 0;
            display: flex; flex-direction: column; justify-content: center;
            padding: 48px 40px;
            background: rgba(13,10,8,0.88);
            backdrop-filter: blur(8px);
            border-left: 1px solid rgba(200,169,110,0.12);
            overflow-y: auto;
        }
        .form-input {
            width:100%; padding:11px 12px; background:rgba(0,0,0,0.5);
            border:1px solid var(--stone-border); color:var(--parchment);
            font-size:13px; font-family:'Courier New',monospace;
            margin-bottom:14px; transition:border-color 0.2s;
        }
        .form-input:focus { outline:none; border-color:var(--copper); box-shadow:0 0 0 2px rgba(200,169,110,0.08); }
        .form-input::placeholder { color:#4a3f30; }
        .form-label { display:block; color:var(--copper); font-size:11px; font-weight:bold; letter-spacing:2px; margin-bottom:6px; }
        .btn-login {
            width:100%; background:linear-gradient(135deg,var(--copper),#8b6914);
            color:#0d0d0f; font-weight:bold; padding:12px; border:none; font-size:14px;
            cursor:pointer; font-family:'Courier New',monospace;
            letter-spacing:2px; box-shadow:3px 3px 0 rgba(0,0,0,0.5); transition:all 0.2s;
        }
        .btn-login:hover { filter:brightness(1.1); transform:translate(-1px,-1px); }
        .demo-box { background:rgba(200,169,110,0.04); border:1px solid rgba(200,169,110,0.12); padding:12px; margin-top:18px; }
        .demo-item { background:rgba(0,0,0,0.3); padding:8px 10px; margin-bottom:6px; cursor:pointer; border:1px solid transparent; transition:border-color 0.2s; display:flex; align-items:center; gap:8px; }
        .demo-item:hover { border-color:var(--copper); }
        .demo-item:last-child { margin-bottom:0; }
        .demo-item img { width:20px; height:20px; object-fit:contain; flex-shrink:0; }
        .alert-error { background:rgba(192,57,43,0.12); border-left:3px solid #c0392b; color:#e07060; padding:10px; font-size:13px; margin-bottom:14px; }

        /* Responsive */
        @media (max-width: 768px) {
            .auth-wrap { flex-direction: column; }
            .panel-left { padding: 40px 24px; flex: none; }
            .panel-right { width: 100%; padding: 32px 24px; }
            .house-row { display: none; }
        }
    </style>
</head>
<body>
<div class="auth-wrap">

    {{-- LEFT: Welcome Panel --}}
    <div class="panel-left">
        <img src="/images/Hogwarts.png" alt="Hogwarts" class="hero-logo">
        <h1>HOGWARTS</h1>
        <div class="subtitle">POTION LAB SYSTEM</div>
        <div class="divider-line"></div>
        <p>
            Selamat datang di sistem manajemen praktikum ramuan sihir.<br>
            Racik, submit, dan validasi ramuanmu bersama para guru terbaik Hogwarts.
        </p>
        <div class="house-row">
            <img src="/images/gryffindor.png" alt="Gryffindor" title="Gryffindor">
            <img src="/images/ravenclaw.png"  alt="Ravenclaw"  title="Ravenclaw">
            <img src="/images/Hufflepuff.png" alt="Hufflepuff" title="Hufflepuff">
            <img src="/images/Slytherin.png"  alt="Slytherin"  title="Slytherin">
        </div>
    </div>

    {{-- RIGHT: Form Panel --}}
    <div class="panel-right">
        <div style="margin-bottom:28px;">
            <div style="color:var(--candle); font-size:18px; font-weight:bold; letter-spacing:2px; margin-bottom:4px;">LOGIN</div>
            <div style="color:var(--parchment-dim); font-size:12px;">Masuk ke akun Hogwarts kamu</div>
        </div>

        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
            @endforeach
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <label for="email" class="form-label"><i class="fas fa-envelope" style="margin-right:6px;"></i>EMAIL</label>
            <input type="email" id="email" name="email" class="form-input" placeholder="your@email.com" value="{{ old('email') }}" autocomplete="email" required>

            <label for="password" class="form-label"><i class="fas fa-lock" style="margin-right:6px;"></i>PASSWORD</label>
            <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" autocomplete="current-password" required>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt" style="margin-right:8px;"></i>LOGIN
            </button>
        </form>

        <div style="text-align:center; margin-top:14px;">
            <a href="{{ route('register') }}" style="color:var(--parchment-dim); font-size:12px; text-decoration:none;">
                Belum punya akun? <span style="color:var(--copper);">Register</span>
            </a>
        </div>

        <div class="demo-box">
            <div style="color:var(--copper); font-size:10px; font-weight:bold; letter-spacing:2px; margin-bottom:8px;">📌 DEMO ACCOUNTS</div>
            <div class="demo-item" onclick="fillLogin('mione@student.hogwarts.ac.id','password')">
                <img src="/images/gryffindor.png" alt="G">
                <div><div style="color:#e74c3c; font-size:11px; font-weight:bold;">mione@student.hogwarts.ac.id</div><div style="color:var(--parchment-dim); font-size:10px;">Gryffindor · password</div></div>
            </div>
            <div class="demo-item" onclick="fillLogin('snivellus@hogwarts.ac.id','password')">
                <img src="/images/Hogwarts.png" alt="Guru">
                <div><div style="color:var(--copper); font-size:11px; font-weight:bold;">snivellus@hogwarts.ac.id</div><div style="color:var(--parchment-dim); font-size:10px;">Guru · password</div></div>
            </div>
        </div>
    </div>

</div>
<script>
    function fillLogin(email, pass) {
        document.querySelector('input[name=email]').value = email;
        document.querySelector('input[name=password]').value = pass;
    }
</script>
</body>
</html>
