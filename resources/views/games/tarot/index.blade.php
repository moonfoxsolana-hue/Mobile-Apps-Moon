@extends('layouts.tarot')

@section('content')
<style>
    /* 🌑 Latar & Dasar */
    body {

        overflow-x: hidden;
        margin: 0;
        padding: 0;
    }

    /* 🌙 Container Utama */
    .tarot-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-height: 100vh;
    }

    /* 🌟 Judul Mistis */
    .mystic-title {
        color: #ffde59;
        font-size: clamp(1.3rem, 4vw, 2rem);
        margin-bottom: 1rem;
        cursor: pointer;
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        animation: pulseGlow 2s infinite alternate, float 4s ease-in-out infinite;
        filter: drop-shadow(0 0 6px #c559ffff) drop-shadow(0 0 6px #9500ffff);
    }

    /* 🌙 Wrapper Utama */
    .phase-center-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        min-height: 100vh;
        padding: 2rem;
        box-sizing: border-box;
        transition: all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* ✨ Ikon & Judul */
    .oracle-icon {
        width: 100px;
        height: auto;
        margin-bottom: 1rem;
        opacity: 0.9;
        animation: float 4s ease-in-out infinite;
    }

    .phase-title {
        font-size: clamp(1.3rem, 4vw, 2rem);
        margin: 1.5rem;
        color: #ffde59;
        filter: drop-shadow(0 0 6px #c559ffff) drop-shadow(0 0 6px #9500ffff);
        cursor: pointer;
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        animation: float 4s ease-in-out infinite;
    }

    /* 🔮 Pilihan Elemen */
    .element-selection {
        display: flex;
        gap: 6px;
        justify-content: center;
        margin-bottom: 0.5rem;
        flex-wrap: wrap;
    }

    .element-selection div {
        color: #fff;
        font-size: clamp(0.8rem, 1vw, 1.2rem);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .element-selection div:hover,
    .element-selection div:focus {
        filter: drop-shadow(0 0 40px #ffde59) drop-shadow(0 0 15px #ffa600);
        transform: scale(1.05);
    }

    .element-selection div:active {
        transform: scale(0.98);
    }

    .element-selection div.selected {
        color: #ffde59;
        filter: drop-shadow(0 0 40px #ffde59) drop-shadow(0 0 15px #ffa600);
        transform: scale(1.1);
    }

    /* 🪄 Form Input */
    .tarot-form {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
        width: 200px;
        margin-bottom: 1rem;
    }

    .tarot-form input,
    .tarot-form select {
        background: rgba(20, 20, 30, 0.85);
        border: 1px solid #a29f00ff;
        border-radius: 12px;
        padding: 8px 10px;
        color: #fff;
        font-size: 1rem;
        text-align: center;
        box-shadow: 0 0 10px rgba(78, 0, 162, 0.4) inset;
        transition: 0.3s ease;
    }

    .tarot-form input:hover,
    .tarot-form select:hover,
    .tarot-form input:focus,
    .tarot-form select:focus {
        border-color: #fff369;
        box-shadow: 0 0 15px rgba(255, 228, 105, 0.7);
        outline: none;
    }

    /* 🃏 Container Kartu */
    .tarot-cards {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        justify-items: center;
        align-items: center;
        margin: 1rem 0;
        width: 100%;
        max-width: 1000px;
        padding: 0 1rem;
        box-sizing: border-box;
    }

    .tarot-card {
        width: 120px;
        aspect-ratio: 140 / 185;
        background-image: url('/images/asset/tarot/closed-card.png');
        background-size: cover;
        background-position: center;
        border-radius: 12px;
        cursor: pointer;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s, border 0.3s;
        border: 2px solid transparent;
    }

    /* Hover & Focus Selaras */
    .tarot-card:hover {
        transform: scale(1.08);
    }

    .tarot-card:focus {
        transform: scale(1.08);
        border-color: #dc69ffff;
        box-shadow: 0 0 15px rgba(210, 105, 255, 0.7);
    }

    /* ⚡ Tetap biarkan card selected seperti aslinya */
    .tarot-card.selected {
        transform: scale(1.25) rotate(-3deg);
        filter: drop-shadow(0 0 6px #c559ffff) drop-shadow(0 0 6px #9500ffff);
        z-index: 10;
    }

    .tarot-cards-chosen {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        justify-items: center;
        align-items: center;
        margin: 1rem 0;
        width: 100%;
        max-width: 800px;
        padding: 0 1rem;
        box-sizing: border-box;
    }

    /* Bungkus tiap kartu */
    .card-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    /* Gambar kartu */
    .card-wrapper .tarot-card {
        width: 100%;
        max-width: 180px;
        aspect-ratio: 140 / 185;
        border-radius: 12px;
        background-size: cover;
        background-position: center;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .card-wrapper .tarot-card:hover {
        transform: scale(1.5);
    }

    /* Nama kartu di bawah gambar */
    .card-name {
        margin-top: 0.6rem;
        font-size: 0.9rem;
        color: #ffde59;
        text-shadow: 0 0 8px rgba(255, 222, 89, 0.6);
        letter-spacing: 0.5px;
    }

    /* Responsif */
    @media (max-width: 768px) {
        #chosenCards {
            gap: 10px;
        }

        .card-wrapper .tarot-card {
            width: 85px;
            height: 130px;
        }

        .card-name {
            font-size: 0.75rem;
        }
    }


    /* 🌟 Tombol Mistis */
    .mystic-btn {
        color: #fffde7;
        font-size: clamp(0.8rem, 2vw, 1.6rem);
        padding: clamp(8px, 2vw, 14px) clamp(22px, 4vw, 36px);
        border: 2px solid #ffff71;
        border-radius: 10px;
        cursor: pointer;
        box-shadow: 0 0 25px rgba(255, 181, 77, 0.8);
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        animation: pulseGlow 2s infinite alternate, float 4s ease-in-out infinite;
    }

    .mystic-btn:hover,
    .mystic-btn:focus {
        transform: scale(1.08);
        box-shadow: 0 0 40px rgba(255, 181, 77, 0.9);
    }

    .mystic-normal-btn {
        background-color: #000000ff;
        color: #fffde7;
        font-size: clamp(0.8rem, 1vw, 1.2rem);
        padding: clamp(8px, 1vw, 12px) clamp(16px, 2vw, 24px);
        border: 2px solid #ffbf71ff;
        border-radius: 10px;
        cursor: pointer;
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 2rem;
    }

    .mystic-normal-btn:hover,
    .mystic-normal-btn:focus {
        transform: scale(1.08);
        box-shadow: 0 0 20px rgba(255, 181, 77, 0.9);
    }

    /* 🌌 Animasi */
    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-8px);
        }
    }

    @keyframes pulseGlow {
        0% {
            border-color: #ff9971;
        }

        100% {
            border-color: #ffbe69;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(30px);
            filter: blur(5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
        }
    }

    /* 🌠 Bagian Ramalan */
    .oracle-section {
        background: rgba(20, 20, 30, 0.85);
        border: 2px solid #ffde59;
        border-radius: 16px;
        padding: clamp(10px, 2vw, 20px);
        margin-top: 2rem;
        margin-bottom: 2rem;
        color: #ffde59;
        text-align: center;
        animation: fadeIn 1.2s ease-out;
        box-shadow: 0 0 20px #292826ff;
        max-width: 800px;
        line-height: 1.6;
    }

    /* 🪞 Avatar / Gambar Peramal */
    .oracle-image {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 3px solid #ffde59;
        box-shadow: 0 0 20px #ffde59;
        object-fit: cover;
        margin-bottom: 1.2rem;
        transition: transform 0.5s ease;
    }

    .oracle-image:hover {
        transform: rotate(5deg) scale(1.05);
    }

    /* 📱 Responsif */


    #aiResult {
        opacity: 0;
        animation: fadeInUp 1.5s ease-out forwards;
        animation-delay: 0.5s;
        color: #fffde7;
        font-family: "Cormorant Garamond", serif;
        text-align: left;
        padding: 1.5rem;
        border: 1px solid rgba(255, 222, 89, 0.3);
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(255, 222, 89, 0.2);
        background: rgba(20, 20, 30, 0.7);
        margin: 20px;
    }

    /* Heading */
    #aiResult h3,
    #aiResult h4 {
        color: #ffde59;
        text-shadow: 0 0 10px rgba(255, 222, 89, 0.6);
        margin-top: 1.2rem;
        animation: pulseGlowAI 3s ease-in-out infinite alternate;
    }

    /* Bold text */
    #aiResult strong {
        color: #fff;
        text-shadow: 0 0 5px #ffde59;
    }

    /* Quote */
    #aiResult blockquote {
        border-left: 3px solid #ffde59;
        padding-left: 1rem;
        margin: 1.2rem 0;
        font-style: italic;
        color: #fff6cc;
        opacity: 0.9;
        animation: fadeInUp 1.5s ease-out forwards;
        animation-delay: 3.5s;
    }

    /* Separator */
    #aiResult hr {
        border: none;
        border-top: 1px solid rgba(255, 222, 89, 0.3);
        margin: 1.5rem 0;
    }

    @keyframes pulseGlowAI {
        from {
            text-shadow: 0 0 6px rgba(255, 222, 89, 0.4);
        }

        to {
            text-shadow: 0 0 16px rgba(255, 222, 89, 0.9);
        }
    }

    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
            filter: blur(8px);
        }

        60% {
            opacity: 0.8;
            transform: translateY(5px);
            filter: blur(2px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
        }
    }

    @media (max-width: 768px) {
        .tarot-cards {
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
            margin: 1rem 0;
        }

        .tarot-card {
            width: 100%;
            min-width: 60px;
            max-width: 80px;
        }

        .tarot-form {
            width: 80%;
        }

        .mystic-btn {
            font-size: 1rem;
            padding: 8px 22px;
        }

        #aiResult {
            margin: auto;
        }
    }
</style>

<div class="tarot-container">
    <!-- PHASE 1: Start -->
    <div id="phase-start">
        <h1 class="mystic-title">✨ Tarot of Mystic Nusa ✨</h1>
        <button id="startBtn" class="mystic-btn">Mulai Ritual</button>
    </div>

    <!-- PHASE 2: Choose Cards -->
    <div id="phase-select" style="display:none;">
        <div class="phase-center-wrapper">
            <h2 class="phase-title">✨ Tarot of Mystic Nusa ✨</h2>
            <div class="tarot-form">
                <input type="text" id="playerName" placeholder="Namamu..." required>
            </div>
            <div id="elementSelection" class="element-selection">
                <div value="fire" class="tarot-element">🔥 Api</div>
                <div value="water" class="tarot-element">💧 Air</div>
                <div value="earth" class="tarot-element">🌿 Tanah</div>
                <div value="air" class="tarot-element">🌬️ Angin</div>
                <div value="ether" class="tarot-element">✨ Cahaya</div>
            </div>
            <div id="cardsContainer" class="tarot-cards"></div>
            <div id="cardSelectionInfo" style="padding-top:10px;font-size: 0.9rem; color: #ccc;">Pilih 3 kartu dengan intuisimu...</div>
            <button id="pickCardsBtn" class="mystic-normal-btn" style="display:none;">Pilih Kartu</button>
        </div>
    </div>



    <!-- PHASE 3: AI Reading -->
    <div id="phase-reading" style="display:none;">
        <div class="phase-center-wrapper">

            <div class="oracle-section">
                <h2 id="oracleName" class="text-2xl"></h2>
                <p id="oracleMessage" class="italic text-lg"></p>
            </div>

            <div id="chosenCards" class="tarot-cards-chosen mt-4"></div>

            <button id="aiReadingBtn" class="mystic-normal-btn mt-6">Mulai Meramal</button>
        </div>
    </div>
    <div id="aiResultContainer" style="display:none;">
        <div id="aiResult" class="mt-6 text-lg leading-relaxed" style="max-width:1000px;margin:auto;"></div>
        <button onclick="location.reload()" class="mystic-normal-btn mt-6 mb-10">Main Lagi</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let sessionId = null;
        let allCards = [];
        let selectedCards = [];

        const phaseStart = document.getElementById('phase-start');
        const phaseSelect = document.getElementById('phase-select');
        const phaseReading = document.getElementById('phase-reading');

        const startBtn = document.getElementById('startBtn');
        const pickCardsBtn = document.getElementById('pickCardsBtn');
        const aiReadingBtn = document.getElementById('aiReadingBtn');

        const elementSelection = document.getElementById('elementSelection');
        let selectedElement = null;

        elementSelection.addEventListener('click', (e) => {
            if (e.target && e.target.matches('div')) {
                selectedElement = e.target.getAttribute('value');
                document.querySelectorAll('#elementSelection div').forEach(div => {
                    div.classList.remove('selected');
                });
                e.target.classList.add('selected');
                console.log('Selected element:', selectedElement);
            }
        });

        // ---- PHASE 1 ----
        startBtn.addEventListener('click', async () => {
            startBtn.disabled = true;
            startBtn.textContent = "Memanggil roh penuntun...";

            try {

                const res = await fetch('/api/tarot/start', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });
                const data = await res.json();

                if (data.status === 'success') {
                    sessionId = data.session_id;
                    allCards = data.cards;
                    renderCards(data.cards);
                    phaseStart.style.display = 'none';
                    phaseSelect.style.display = 'flex';
                } else {
                    alert(data.message);
                }
            } catch (err) {
                alert('Gagal memulai ritual.');
            } finally {
                startBtn.disabled = false;
                startBtn.textContent = "Mulai Ritual";
            }
        });

        function renderCards(cards) {
            const container = document.getElementById('cardsContainer');
            container.innerHTML = '';
            cards.forEach(card => {
                const el = document.createElement('div');
                const index_number = cards.indexOf(card) + 1;
                el.classList.add('tarot-card');
                el.dataset.id = card.id;
                el.image = card.image;
                el.textContent = `${index_number}`;
                el.addEventListener('click', () => {
                    if (selectedCards.some(c => c.id === card.id)) {
                        selectedCards = selectedCards.filter(c => c.id !== card.id);
                        el.classList.remove('selected');
                    } else {
                        if (selectedCards.length >= 3) return;
                        selectedCards.push(card);
                        el.classList.add('selected');
                    }
                    pickCardsBtn.style.display = selectedCards.length === 3 ? 'inline-block' : 'none';
                    cardSelectionInfo.textContent = `Kartu terpilih: ${selectedCards.length} dari 3`;
                });
                container.appendChild(el);
            });
            attachHoverSound();

        }

        // ---- PHASE 2 ----
        pickCardsBtn.addEventListener('click', async () => {
            const name = document.getElementById('playerName').value.trim();
            if (!name || !selectedElement || selectedCards.length !== 3) {
                alert('Isi nama, pilih elemen, dan pilih 3 kartu.');
                return;
            }

            const body = {
                session_id: sessionId,
                name: name,
                energy_choice: selectedElement,
                cards: selectedCards
            };

            try {
                const res = await fetch('/api/tarot/pick-card', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(body)
                });
                const data = await res.json();
                if (data.status === 'success') {
                    document.getElementById('oracleName').textContent = data.oracle;
                    document.getElementById('oracleMessage').textContent = data.message;

                    const chosenContainer = document.getElementById('chosenCards');
                    chosenContainer.innerHTML = '';

                    data.cards.forEach(card => {
                        // wrapper untuk 1 kartu
                        const wrapper = document.createElement('div');
                        wrapper.classList.add('card-wrapper');

                        // gambar kartu
                        const img = document.createElement('img');
                        img.src = card.image;
                        img.alt = card.name;
                        img.classList.add('tarot-card');
                        img.style.backgroundImage = `url(${card.image})`;

                        // teks nama kartu
                        const txt = document.createElement('div');
                        txt.textContent = card.name;
                        txt.classList.add('card-name');

                        // gabungkan
                        wrapper.appendChild(img);
                        wrapper.appendChild(txt);
                        chosenContainer.appendChild(wrapper);
                    });

                    phaseSelect.style.display = 'none';
                    phaseReading.style.display = 'flex';

                } else {
                    alert(data.message);
                }
            } catch (err) {
                alert('Gagal mengirim pilihan kartu.');
            }
        });

        // ---- PHASE 3 ----
        aiReadingBtn.addEventListener('click', async () => {
            aiReadingBtn.disabled = true;
            aiReadingBtn.textContent = "Menarik benang takdir...";

            try {
                const res = await fetch('/api/tarot/ai-reading', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        oracle_name: document.getElementById('oracleName').textContent
                    })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    phaseReading.style.display = 'none';
                    const aiContainer = document.getElementById('aiResultContainer');
                    const aiResult = document.getElementById('aiResult');

                    aiContainer.style.display = 'inline-block';
                    aiContainer.style.zIndex = 1010;
                    window.scrollTo({
                        top: aiContainer.offsetTop,
                        behavior: 'smooth'
                    });

                    // konversi Markdown ke HTML
                    const formatted = marked.parse(data.message);

                    // reset animasi dulu biar bisa main lagi setiap kali muncul
                    aiResult.style.animation = 'none';
                    void aiResult.offsetWidth; // trick untuk restart CSS animation
                    aiResult.innerHTML = formatted;
                    aiResult.style.animation = 'fadeInUp 1.2s ease-out forwards';
                    document.getElementById('gameMenu').style.display = 'none';
                    document.getElementById('exitMenu').style.display = 'none';
                } else {
                    alert(data.message);
                }

            } catch (err) {
                alert('Gagal memanggil AI oracle.');
            } finally {
                aiReadingBtn.disabled = false;
                aiReadingBtn.textContent = "Mulai Meramal";
            }
        });
    });
</script>
@endsection