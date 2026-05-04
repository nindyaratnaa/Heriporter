<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Tidak Ditemukan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body {
            background:#0d0d0f; color:#d4c9b0;
            font-family:'Courier New',monospace;
            min-height:100vh; display:flex; align-items:center; justify-content:center;
            position:relative;
        }
        body::before {
            content:''; position:fixed; inset:0;
            background-image:url('/images/Auth bg.png');
            background-size:cover; background-position:center;
            filter:blur(2px) brightness(0.35); transform:scale(1.04); z-index:0;
        }
        .container { position:relative; z-index:1; text-align:center; padding:40px 20px; max-width:500px; }
        .code { font-size:80px; font-weight:bold; color:#c8a96e; letter-spacing:8px; text-shadow:0 0 30px rgba(200,169,110,0.4); }
        .title { font-size:18px; color:#e8d5a3; letter-spacing:3px; margin:12px 0 8px; }
        .message { color:#8a7d68; font-size:13px; line-height:1.7; margin-bottom:32px; }
        .btn {
            display:inline-block; background:linear-gradient(135deg,#c8a96e,#8b6914);
            color:#0d0d0f; font-weight:bold; padding:12px 32px; text-decoration:none;
            font-family:'Courier New',monospace; letter-spacing:2px; font-size:13px;
            box-shadow:3px 3px 0 rgba(0,0,0,0.5); transition:all 0.2s;
        }
        .btn:hover { filter:brightness(1.1); transform:translate(-2px,-2px); }
        .hat { font-size:60px; margin-bottom:16px; animation:float 3s ease-in-out infinite; display:block; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
    </style>
</head>
<body>
<div class="container">
    <img src="/images/Sorting Hat.png" alt="hat" style="width:80px;height:80px;object-fit:contain;margin-bottom:16px;animation:float 3s ease-in-out infinite;">
    <div class="code">404</div>
    <div class="title">HALAMAN TIDAK DITEMUKAN</div>
    <div class="message">
        {{ $exception->getMessage() ?: 'Halaman yang kamu cari tidak ada atau sudah dipindahkan.' }}
    </div>
    <a href="javascript:history.back()" class="btn">← KEMBALI</a>
</div>
</body>
</html>
