<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sorting Hat - Hogwarts</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --copper:#c8a96e; --stone-dark:#0d0d0f; --parchment:#d4c9b0; --parchment-dim:#8a7d68; --candle:#e8d5a3; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body {
            background: #0d0d0f;
            color: var(--parchment);
            font-family: 'Courier New', monospace;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 20px; position: relative;
        }
        body::before {
            content: ''; position: fixed; inset: 0;
            background-image: url('/images/Auth bg.png');
            background-size: cover; background-position: center;
            filter: blur(2px) brightness(0.45);
            transform: scale(1.04); z-index: 0;
        }
        .container { position: relative; z-index: 1; width: 100%; max-width: 600px; }

        /* Intro screen */
        #introScreen { text-align: center; }
        .hat-big { display:block; width:120px; height:120px; object-fit:contain; margin:0 auto 16px; animation: float 3s ease-in-out infinite; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        .title-main { color:var(--candle); font-size:26px; font-weight:bold; letter-spacing:4px; margin-bottom:8px; }
        .title-sub  { color:var(--parchment-dim); font-size:12px; letter-spacing:2px; margin-bottom:32px; }
        .btn-start {
            background:linear-gradient(135deg,var(--copper),#8b6914); color:#0d0d0f; font-weight:bold; padding:14px 40px;
            border:none; font-size:15px; cursor:pointer; font-family:'Courier New',monospace;
            letter-spacing:2px; box-shadow:4px 4px 0 rgba(0,0,0,0.5); transition:all 0.2s;
        }
        .btn-start:hover { filter:brightness(1.1); transform:translate(-2px,-2px); }

        /* Question screen */
        #questionScreen { display: none; }
        .progress-bar-wrap { background:rgba(0,0,0,0.4); border:1px solid rgba(200,169,110,0.3); height:6px; margin-bottom:24px; }
        .progress-bar-fill { background:var(--copper); height:100%; transition:width 0.4s; }
        .q-counter { color:var(--parchment-dim); font-size:11px; letter-spacing:2px; margin-bottom:8px; }
        .q-text { color:var(--candle); font-size:17px; font-weight:bold; line-height:1.5; margin-bottom:24px; }
        .option {
            background:rgba(0,0,0,0.3); border:1px solid rgba(200,169,110,0.2);
            padding:14px 18px; margin-bottom:10px; cursor:pointer;
            font-size:13px; color:var(--parchment); transition:all 0.2s; display:flex; align-items:center; gap:12px;
        }
        .option:hover { border-color:var(--copper); color:var(--candle); background:rgba(200,169,110,0.06); }
        .option.selected { border-color:var(--copper); background:rgba(200,169,110,0.12); color:var(--candle); }
        .opt-key { color:var(--copper); font-weight:bold; font-size:14px; min-width:20px; }
        .btn-next {
            width:100%; background:linear-gradient(135deg,var(--copper),#8b6914); color:#0d0d0f; font-weight:bold; padding:12px;
            border:none; font-size:14px; cursor:pointer; font-family:'Courier New',monospace;
            letter-spacing:2px; margin-top:16px; box-shadow:3px 3px 0 rgba(0,0,0,0.4); display:none;
        }
        .btn-next:hover { filter:brightness(1.1); }

        /* Thinking screen */
        #thinkingScreen { display: none; text-align: center; padding: 40px 0; }
        .hat-think { display:block; width:90px; height:90px; object-fit:contain; margin:0 auto 20px; }
        @keyframes shake { 0%,100%{transform:rotate(0)} 20%{transform:rotate(-8deg)} 40%{transform:rotate(8deg)} 60%{transform:rotate(-5deg)} 80%{transform:rotate(5deg)} }
        .hat-think.thinking { animation: shake 0.6s ease-in-out infinite; }
        .think-text { color:var(--copper); font-size:16px; letter-spacing:2px; }
        .dots::after { content:''; animation:dots 1.5s steps(4,end) infinite; }
        @keyframes dots { 0%{content:''} 25%{content:'.'} 50%{content:'..'} 75%{content:'...'} }
    </style>
</head>
<body>
<div class="container">

    {{-- INTRO --}}
    <div id="introScreen">
        <img src="/images/Sorting Hat.png" alt="Sorting Hat" class="hat-big">
        <div class="title-main">THE SORTING HAT</div>
        <div class="title-sub">HOGWARTS SCHOOL OF WITCHCRAFT AND WIZARDRY</div>
        <p style="color:var(--parchment-dim); font-size:13px; line-height:1.7; margin-bottom:32px; max-width:420px; margin-left:auto; margin-right:auto;">
            Selamat datang, <strong style="color:var(--candle);">{{ session('user_name') }}</strong>!<br>
            Aku akan menentukan House mana yang paling cocok untukmu.<br>
            Jawab 3 pertanyaan berikut dengan jujur.
        </p>
        <button class="btn-start" onclick="startQuiz()">
            🎩 TEMUKAN HOUSE SAYA!
        </button>
    </div>

    {{-- QUESTIONS --}}
    <div id="questionScreen">
        <img src="/images/Sorting Hat.png" alt="Sorting Hat" style="width:40px; height:40px; object-fit:contain; margin:0 auto 24px; display:block;">
        <div class="progress-bar-wrap">
            <div class="progress-bar-fill" id="progressBar" style="width:0%"></div>
        </div>
        <div class="q-counter" id="qCounter">PERTANYAAN 1 / 3</div>
        <div class="q-text" id="qText"></div>
        <div id="optionsContainer"></div>
        <button class="btn-next" id="btnNext" onclick="nextQuestion()">
            LANJUT →
        </button>

        <form id="submitForm" method="POST" action="{{ route('sorting-hat.assign') }}" style="display:none;">
            @csrf
            <input type="hidden" name="answers[0]" id="ans0">
            <input type="hidden" name="answers[1]" id="ans1">
            <input type="hidden" name="answers[2]" id="ans2">
        </form>
    </div>

    {{-- THINKING --}}
    <div id="thinkingScreen">
        <img src="/images/Sorting Hat.png" alt="Sorting Hat" class="hat-think thinking" id="hatThink">
        <div class="think-text">Hmm, biarkan aku berpikir<span class="dots"></span></div>
        <p style="color:#666; font-size:12px; margin-top:12px;">"Aku melihat jauh ke dalam pikiranmu..."</p>
    </div>

</div>

<script>
const questions = @json($questions);
let current = 0;
const answers = {};

function startQuiz() {
    document.getElementById('introScreen').style.display = 'none';
    document.getElementById('questionScreen').style.display = 'block';
    renderQuestion();
}

function renderQuestion() {
    const q = questions[current];
    document.getElementById('qCounter').textContent = `PERTANYAAN ${current + 1} / ${questions.length}`;
    document.getElementById('qText').textContent = q.q;
    document.getElementById('progressBar').style.width = `${(current / questions.length) * 100}%`;
    document.getElementById('btnNext').style.display = 'none';

    const container = document.getElementById('optionsContainer');
    container.innerHTML = '';
    Object.entries(q.opts).forEach(([key, opt]) => {
        const div = document.createElement('div');
        div.className = 'option';
        div.innerHTML = `<span class="opt-key">${key.toUpperCase()}.</span> ${opt.text}`;
        div.onclick = () => selectOption(key, div);
        container.appendChild(div);
    });
}

function selectOption(key, el) {
    document.querySelectorAll('.option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    answers[current] = key;
    document.getElementById('btnNext').style.display = 'block';
}

function nextQuestion() {
    if (answers[current] === undefined) return;
    current++;
    if (current < questions.length) {
        renderQuestion();
    } else {
        submitAnswers();
    }
}

function submitAnswers() {
    document.getElementById('questionScreen').style.display = 'none';
    document.getElementById('thinkingScreen').style.display = 'block';

    document.getElementById('ans0').value = answers[0] || '';
    document.getElementById('ans1').value = answers[1] || '';
    document.getElementById('ans2').value = answers[2] || '';

    const thinkAudio = new Audio('/sounds/thingking.mp3');
    thinkAudio.volume = 0.7;
    thinkAudio.play().catch(() => {});

    setTimeout(() => {
        document.getElementById('submitForm').submit();
    }, 10000);
}
</script>
</body>
</html>
