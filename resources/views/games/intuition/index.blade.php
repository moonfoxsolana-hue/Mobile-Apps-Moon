@extends('layouts.intuition')

@section('content')
<style>
    .intuition-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        min-height: 65vh;
        position: relative;
        z-index: 2;
    }

    .intuition-card {
        background: rgba(20, 20, 20, 0.8);
        border: 1px solid rgba(118, 55, 212, 0.3);
        box-shadow: 0 0 15px rgba(160, 55, 212, 0.2);
        border-radius: 18px;
        padding: 30px;
        width: 90%;
        max-width: 600px;
        text-align: center;
        animation: intuition-fadeIn 0.6s ease;
    }

    .intuition-title {
        font-family: 'Cinzel', serif;
        font-size: 1.8rem;
        color: #b149f7ff;
        margin-bottom: 14px;
    }

    .intuition-desc {
        font-size: 1rem;
        color: #ccc;
        margin-bottom: 24px;
        line-height: 1.6;
    }

    .intuition-btn {
        background: rgba(20, 20, 20, 0.8);
        border: 1px solid #b149f7ff;
        color: #b149f7ff;
        padding: 10px 24px;
        border-radius: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .intuition-btn:hover {
        background: rgba(178, 55, 212, 0.3);
        box-shadow: 0 0 15px rgba(212, 55, 209, 0.5);
        transform: translateY(-2px);
    }

    .title {
        font-size: 1.8rem;
        color: #b149f7ff;
        margin-bottom: 14px;
    }

    #round-info {
        font-size: 16px;
        margin-bottom: 15px;
        opacity: 0.8;
    }

    .start-btn {
        padding: 10px 25px;
        font-size: 18px;
        background: linear-gradient(45deg, #6b00ff, #00e1ff);
        border: none;
        border-radius: 10px;
        color: #fff;
        cursor: pointer;
        transition: 0.3s;
    }

    .start-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 0 20px #00e1ff;
    }

    #countdown-bar {
        width: 60%;
        height: 15px;
        margin: 20px auto;
        background-color: #222;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 10px #111;
    }

    #progress {
        width: 100%;
        height: 100%;
        background: linear-gradient(to left, rgba(0, 132, 255, 1), rgba(72, 16, 255, 1), rgba(153, 0, 255, 1));
        transition: width 1s linear;
    }

    .items {
        display: flex;
        justify-content: center;
        gap: 40px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .item {
        width: 130px;
        height: 130px;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        animation: floaty 6s ease-in-out infinite;

    }

    .item img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: filter 0.4s ease, transform 0.4s ease;
    }

    .item img {
        opacity: 0;
        transform: scale(0.95);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }

    .item.fade-in img {
        opacity: 1;
        transform: scale(1);
    }

    .items.fade-out .item img {
        opacity: 0;
        transform: scale(0.9);
    }

    .item.correct img {
        filter: drop-shadow(0 0 15px #00ff88) brightness(1.2);
        transform: scale(1.05);
    }

    .item.wrong img {
        filter: drop-shadow(0 0 12px #ff0033) brightness(0.9);
        transform: scale(0.95);
    }

    .item.selected img {
        filter: drop-shadow(0 0 10px #00e1ff);
        transform: scale(1.03);
    }

    .status-text {
        margin-top: 25px;
        font-size: 18px;
        min-height: 25px;
    }

    .result-box {
        margin-top: 40px;
        padding: 25px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0, 255, 200, 0.1);
    }

    .result-box h3 {
        margin-bottom: 10px;
        font-size: 22px;
    }

    @keyframes floaty {

        0%,
        100% {
            transform: translate(0%, 0);
        }

        50% {
            transform: translate(0%, -20px);
        }
    }

    @keyframes intuition-fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* ===============================
       📱 RESPONSIVE DESIGN
       =============================== */
    @media (max-width: 768px) {
        .title {
            font-size: 22px;
        }

        .start-btn {
            font-size: 16px;
            padding: 8px 20px;
        }

        #countdown-bar {
            width: 85%;
        }

        .items {
            gap: 20px;
        }

        .item {
            width: 110px;
            height: 110px;
        }

        .status-text {
            font-size: 16px;
        }

        .result-box {
            width: 90%;
            margin: 30px auto;
        }
    }

    @media (max-width: 480px) {
        .title {
            font-size: 20px;
        }

        #round-info {
            font-size: 14px;
        }

        .item {
            width: 100px;
            height: 100px;
        }

        .items {
            gap: 15px;
        }

        #countdown-bar {
            width: 90%;
            height: 12px;
        }

        .status-text {
            font-size: 14px;
        }

        .result-box h3 {
            font-size: 18px;
        }
    }
</style>
<div class="intuition-container">
    <div class="intuition-card" id="intuition-intro">
        <h2 class="intuition-title">Selamat Datang di Uji Intuisi</h2>
        <p class="intuition-desc">“Fokuskan pikiranmu. Rasakan getaran dari setiap benda. Pilihanmu akan mengungkap seberapa tajam intuisi batinmu.”</p>
        <button class="intuition-btn" id="start-btn">Mulai Main</button>
    </div>
    <div id="game-section" class="intuition-card" style="display:none;">
        <div id="round-info">Round <span id="current-round">1</span>/<span id="total-round">?</span></div>

        <div id="countdown-bar">
            <div id="progress"></div>
        </div>

        <div id="item-container" class="items"></div>

        <div id="status" class="status-text"></div>

        <div id="result-box" class="result-box" style="display:none;">
            <h3>🌙 Hasil Permainan</h3>
            <p>Kekuatan Intuisi: <span id="final-score">0</span> benar dari <span id="final-total">0</span> ronde.</p>
            <button class="start-btn" onclick="location.reload()">Main Lagi</button>
        </div>
    </div>

</div>
<audio id="correct-sound" src="/sound/sfx/correct-intuition.wav"></audio>
<audio id="wrong-sound" src="/sound/sfx/wrong.mp3"></audio>

<script>
    const token = localStorage.getItem("token");
    let matchId = null;
    let selectedItem = null;
    let roundTimer = null;
    let timeLeft = 10;

    let currentRound = 0;
    let totalRound = 0;
    let correctScore = 0;
    const correctSound = document.getElementById('correct-sound');
    const wrongSound = document.getElementById('wrong-sound');

    document.getElementById("start-btn").addEventListener("click", startMatch);

    async function startMatch() {
        const res = await fetch('/api/intuition/start', {
            method: 'POST',
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${token}`,
            },
        });
        const data = await res.json();
        matchId = data.match_id;
        totalRound = data.total_rounds || 5;
        currentRound = 0;
        correctScore = 0;

        document.getElementById("total-round").innerText = totalRound;
        document.getElementById("intuition-intro").style.display = "none";
        document.getElementById("game-section").style.display = "block";

        nextRound();
    }

    async function nextRound() {
        // reset pilihan lokal
        selectedItem = null;
        document.getElementById("status").innerText = "Fokus... Rasakan energi barang yang benar.";
        document.getElementById("progress").style.width = "100%";

        // ambil data ronde dari server dulu (server harus mengembalikan `round` dan `options`)
        try {
            const res = await fetch(`/api/intuition/round/${matchId}`, {
                headers: {
                    "Authorization": `Bearer ${token}`
                }
            });

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                // jika match sudah selesai di server, tampilkan hasil
                if (err.message && err.message.toLowerCase().includes('selesai')) {
                    showResult();
                    return;
                }
                console.error('Gagal ambil ronde:', err);
                document.getElementById("status").innerText = 'Terjadi kesalahan saat mengambil ronde.';
                return;
            }

            const data = await res.json();

            // gunakan nilai round dari server (human-friendly, 1-based) jika tersedia
            // fallback ke increment lokal bila tidak ada
            if (typeof data.round !== 'undefined') {
                currentRound = Number(data.round);
            } else {
                // fallback: jika belum ada nilai, naikkan satu
                currentRound = currentRound + 1;
            }

            // update UI round
            document.getElementById("current-round").innerText = currentRound;

            // render items (kode kamu sudah memakai data.options)
            renderItems(data.options || data.items || []);

            // mulai countdown untuk ronde ini
            startCountdown();
        } catch (e) {
            console.error('Error nextRound:', e);
            document.getElementById("status").innerText = 'Terjadi kesalahan jaringan.';
        }
    }

    function renderItems(items) {
        const container = document.getElementById("item-container");
        container.classList.remove("fade-out");
        container.innerHTML = "";

        items.forEach(item => {
            const div = document.createElement("div");
            div.className = "item";
            div.innerHTML = `<img src="${item.image_url}" alt="${item.name}" data-id="${item.id}">`;
            div.onclick = () => selectItem(div, item.id);
            container.appendChild(div);
        });

        // fade-in semua bersamaan
        setTimeout(() => {
            document.querySelectorAll(".item").forEach(el => el.classList.add("fade-in"));
        }, 100);
    }

    function selectItem(el, id) {
        document.querySelectorAll(".item").forEach(i => i.classList.remove("selected"));
        el.classList.add("selected");
        selectedItem = id;
    }

    function startCountdown() {
        let progress = 100;
        timeLeft = 10; // ubah durasi ke 10 detik
        clearInterval(roundTimer);
        roundTimer = setInterval(() => {
            progress -= 10; // 100 / 10 detik = 10% per detik
            timeLeft--;
            document.getElementById("progress").style.width = `${progress}%`;
            if (timeLeft <= 0) {
                clearInterval(roundTimer);
                setTimeout(submitAnswer, 1000);
            }
        }, 1000);
    }

    async function submitAnswer() {
        const res = await fetch(`/api/intuition/answer/${matchId}`, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${token}`,
            },
            body: JSON.stringify({
                chosen_item_id: selectedItem
            })
        });
        const data = await res.json();

        const correctId = data.correct_item_id;
        document.querySelectorAll(".item").forEach(i => {
            const id = i.querySelector("img").getAttribute("data-id");
            if (id == correctId) i.classList.remove("selected"), i.classList.add("correct");
            else if (selectedItem && id == selectedItem) i.classList.remove("selected"), i.classList.add("wrong");
        });

        if (data.correct) {
            correctScore++;
            correctSound.volume = 0.5;
            correctSound.currentTime = 0;
            correctSound.play();
            // window.addReward?.(10);
        } else {
            wrongSound.volume = 0.5;
            wrongSound.currentTime = 0;
            wrongSound.play();
        }


        document.getElementById("status").innerText = data.correct ?
            "✨ Intuisi kamu benar!" :
            "❌ Salah, tapi kekuatanmu semakin tajam...";

        setTimeout(() => {
            // fade out semua item
            document.getElementById("item-container").classList.add("fade-out");
        }, 1000);
        if (!data.match_completed) {
            setTimeout(nextRound, 2500);
        } else {
            setTimeout(showResult, 2500);
        }
    }

    function showResult() {
        document.getElementById("item-container").innerHTML = "";
        document.getElementById("countdown-bar").style.display = "none";
        document.getElementById("status").innerText = "";
        document.getElementById("result-box").style.display = "block";
        document.getElementById("final-score").innerText = correctScore;
        document.getElementById("final-total").innerText = totalRound;
    }
</script>
@endsection