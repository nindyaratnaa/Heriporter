<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Result - Hogwarts</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --copper:#c8a96e; --stone-dark:#0d0d0f; --stone-mid:#1a1410; --parchment:#d4c9b0; --parchment-dim:#8a7d68; --candle:#e8d5a3; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body {
            background: #0d0d0f;
            color: var(--parchment);
            font-family: 'Courier New', monospace;
            min-height: 100vh;
            display: flex; align-items: flex-start; justify-content: center;
            padding: 60px 20px; overflow-x: hidden; position: relative;
        }
        body::before {
            content: ''; position: fixed; inset: 0;
            background-image: url('/images/Auth bg.png');
            background-size: cover; background-position: center;
            filter: blur(2px) brightness(0.45);
            transform: scale(1.04); z-index: 0;
        }
        .container { width:100%; max-width:560px; position:relative; z-index:1; }
        #revealSection { text-align:center; }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

        .house-badge {
            width:130px; height:130px; border-radius:50%; margin:0 auto 20px;
            display:flex; align-items:center; justify-content:center;
            border:2px solid rgba(200,169,110,0.4);
            box-shadow: 0 0 40px rgba(200,169,110,0.25);
            background: rgba(0,0,0,0.5);
            animation: pulse 2s ease-in-out infinite;
            overflow:hidden;
        }
        @keyframes pulse { 0%,100%{box-shadow:0 0 20px rgba(200,169,110,0.2)} 50%{box-shadow:0 0 50px rgba(200,169,110,0.5)} }

        .house-name  { font-size:34px; font-weight:bold; letter-spacing:4px; color:var(--candle); margin-bottom:6px; }
        .house-trait { color:var(--parchment-dim); font-size:12px; letter-spacing:2px; margin-bottom:24px; }
        .divider     { border:none; border-top:1px solid rgba(200,169,110,0.2); margin:28px 0; }

        .wand-card {
            background: var(--stone-mid);
            border: 1px solid rgba(200,169,110,0.35);
            box-shadow: 0 0 30px rgba(200,169,110,0.1), inset 0 0 30px rgba(0,0,0,0.4);
            padding: 28px; position:relative;
            opacity:0; animation:fadeIn 0.8s 1.2s forwards;
        }
        .wand-card::before {
            content:'✦ OLLIVANDERS ✦';
            position:absolute; top:-10px; left:50%; transform:translateX(-50%);
            background:var(--stone-mid); color:var(--copper); font-size:10px;
            letter-spacing:3px; padding:0 12px; white-space:nowrap;
        }
        .ollivander-greeting { color:var(--parchment-dim); font-size:13px; line-height:1.8; margin-bottom:20px; font-style:italic; }
        .ollivander-greeting strong { color:var(--candle); font-style:normal; }
        .wand-display { text-align:center; margin:20px 0; }
        .wand-img { display:block; margin:0 auto 12px; animation:wandGlow 2s ease-in-out infinite; }
        @keyframes wandGlow { 0%,100%{filter:drop-shadow(0 0 4px #c8a96e)} 50%{filter:drop-shadow(0 0 16px #c8a96e)} }
        .wand-name { color:var(--copper); font-size:17px; font-weight:bold; letter-spacing:2px; margin-bottom:16px; }
        .wand-specs { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
        .spec-item { background:rgba(0,0,0,0.3); border:1px solid rgba(200,169,110,0.15); padding:10px 12px; }
        .spec-label { color:var(--parchment-dim); font-size:10px; letter-spacing:1px; margin-bottom:3px; }
        .spec-value { color:var(--parchment); font-size:12px; }
        .wand-desc { color:var(--parchment-dim); font-size:12px; line-height:1.6; margin-top:16px; font-style:italic; border-left:2px solid rgba(200,169,110,0.3); padding-left:12px; }

        .btn-enter {
            width:100%; background:linear-gradient(135deg,var(--copper),#8b6914); color:#0d0d0f;
            font-weight:bold; padding:14px; border:none; font-size:14px; cursor:pointer;
            font-family:'Courier New',monospace; letter-spacing:3px;
            box-shadow:4px 4px 0 rgba(0,0,0,0.5); margin-top:24px;
            opacity:0; animation:fadeIn 0.8s 2s forwards; transition:all 0.2s;
        }
        .btn-enter:hover { filter:brightness(1.1); transform:translate(-2px,-2px); }
    </style>
</head>
<body>
<div class="container">
    <div id="revealSection">

        @php
            $houseImgs = [
                'Gryffindor' => 'gryffindor.png',
                'Ravenclaw'  => 'ravenclaw.png',
                'Hufflepuff' => 'Hufflepuff.png',
                'Slytherin'  => 'Slytherin.png',
            ];
            $houseImg = $houseImgs[$house] ?? null;
        @endphp

        {{-- House Badge --}}
        <div class="house-badge">
            @if($houseImg)
                <img src="/images/{{ $houseImg }}" alt="{{ $house }}" style="width:100px; height:100px; object-fit:contain;">
            @endif
        </div>
        <div class="house-name">{{ strtoupper($house) }}</div>
        <div class="house-trait">{{ $houseData['trait'] }}</div>

        <p style="color:var(--parchment-dim); font-size:13px; line-height:1.7; margin-bottom:8px;">
            <img src="/images/Sorting Hat.png" alt="hat" style="width:20px; height:20px; object-fit:contain; vertical-align:middle; margin-right:6px;">
            <em>"Hmmm... kepribadian yang unik... pilihan yang sulit... kurasa kamu cocok di —
            <strong style="color:var(--candle);">{{ $house }}</strong>!"</em>
        </p>

        <hr class="divider">

        {{-- Ollivander Wand Card --}}
        @if($wand)
        <div class="wand-card">
            <div class="ollivander-greeting">
                "Selamat datang, <strong>{{ $user['name'] }}</strong>.<br>
                Aku sudah menunggumu. Tongkat sihir memilih penyihirnya, bukan sebaliknya —
                dan tongkat ini telah memilihmu."
            </div>

            <div class="wand-display">
                <img src="/images/{{ $wand['gambar'] }}" alt="{{ $wand['nama'] }}"
                     class="wand-img" style="width:55px; height:75px; object-fit:contain;">
                <div class="wand-name">{{ $wand['nama'] }}</div>
            </div>

            <div class="wand-specs">
                <div class="spec-item">
                    <div class="spec-label">BAHAN KAYU</div>
                    <div class="spec-value">{{ $wand['bahan_kayu'] }}</div>
                </div>
                <div class="spec-item">
                    <div class="spec-label">BAHAN INTI</div>
                    <div class="spec-value">{{ $wand['bahan_inti'] }}</div>
                </div>
                <div class="spec-item">
                    <div class="spec-label">PANJANG</div>
                    <div class="spec-value">{{ $wand['panjang'] }}</div>
                </div>
                <div class="spec-item">
                    <div class="spec-label">FLEKSIBILITAS</div>
                    <div class="spec-value">{{ $wand['fleksibilitas'] }}</div>
                </div>
            </div>

            <div class="wand-desc">{{ $wand['deskripsi'] }}</div>

            <p style="color:var(--parchment-dim); font-size:11px; text-align:right; margin-top:12px; letter-spacing:1px;">
                — Garrick Ollivander, Pembuat Tongkat Sihir
            </p>
        </div>
        @endif

        <form method="GET" action="{{ route('student.dashboard') }}">
            <button type="submit" class="btn-enter">
                ✨ MASUK KE HOGWARTS ✨
            </button>
        </form>

    </div>
</div>

<script>
// House announcement sound
const houseAudio = new Audio('/sounds/{{ strtolower($house) }}.mp3');
houseAudio.volume = 0.85;
window.addEventListener('load', () => {
    setTimeout(() => houseAudio.play().catch(() => {}), 600);
});

</script>
</body>
</html>
