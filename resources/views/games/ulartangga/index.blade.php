@extends('layouts.ulartangga')

@section('content')
<style>
    /* 🎴 ulartangga Game CSS (Isolated) */
    .ulartangga-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        min-height: 75vh;
        position: relative;
        z-index: 2;
    }

    .ulartangga-card {
        background: rgba(20, 20, 20, 0.8);
        border: 1px solid rgba(204, 212, 55, 0.3);
        box-shadow: 0 0 15px rgba(191, 212, 55, 0.2);
        border-radius: 18px;
        padding: 30px;
        width: 90%;
        max-width: 600px;
        text-align: center;
        animation: ulartangga-fadeIn 0.6s ease;
    }

    .ulartangga-title {
        font-family: 'Cinzel', serif;
        font-size: 1.8rem;
        color: #c9d437ff;
        margin-bottom: 14px;
    }

    .ulartangga-desc {
        font-size: 1rem;
        color: #ccc;
        margin-bottom: 24px;
        line-height: 1.6;
    }

    .ulartangga-btn {
        background: rgba(20, 20, 20, 0.8);
        border: 1px solid #ccd437ff;
        color: #bcd437ff;
        padding: 10px 24px;
        margin: 4px;
        border-radius: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .ulartangga-btn:hover {
        background: rgba(201, 212, 55, 0.3);
        box-shadow: 0 0 15px rgba(212, 212, 55, 0.5);
        transform: translateY(-2px);
    }

    .ulartangga-btn-refresh {
        background: rgba(20, 20, 20, 0.8);
        border: 1px solid #ccd437ff;
        color: #bcd437ff;
        padding: 4px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .ulartangga-btn-refresh:hover {
        background: rgba(201, 212, 55, 0.3);
        box-shadow: 0 0 15px rgba(212, 212, 55, 0.5);
        transform: translateY(-2px);
    }

    .ulartangga-progress-bar {
        width: 100%;
        height: 6px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
        margin-bottom: 16px;
    }

    #ulartangga-progress {
        width: 0%;
        height: 6px;
        background: #ccd437ff;
        border-radius: 4px;
        transition: width 0.4s ease;
    }

    .ulartangga-result-title {
        font-family: 'Cinzel', serif;
        color: #c7d437ff;
        font-size: 1.6rem;
        margin-bottom: 20px;
    }

    .ulartangga-input {
        width: 100%;
        max-width: 300px;
        padding: 10px;
        border: 1px solid #ccd437ff;
        border-radius: 8px;
        background: rgba(20, 20, 20, 0.8);
        color: #eee;
        font-size: 1rem;
        margin-bottom: 12px;
        text-align: center;
    }

    .ulartangga-input::placeholder {
        color: #bbb;
    }

    .ulartangga-input:focus {
        outline: none;
        border-color: #ffe600ff;
        box-shadow: 0 0 8px rgba(157, 212, 55, 0.5);
    }

    small {
        color: #bbb;
        display: block;
        font-size: 8px;
        margin-top: 8px;
        margin-left: 8px;
        text-align: left;
    }

    .match-item {
        background: rgba(255, 255, 255, 0.05);
        padding: 10px 15px;
        border-radius: 12px;
        margin: 12px 12px;
        transition: all 0.3s ease;
        color: #fff;
        cursor: pointer;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .match-item:hover {
        background: rgba(255, 215, 0, 0.15);
        border-color: rgba(255, 215, 0, 0.4);
        transform: translateY(-2px);
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.2);
        color: #ffd700;
    }

    @keyframes ulartangga-fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .ulartangga-waiting {
        display: none;
        text-align: center;
        margin-top: 16px;
        color: #ffc107;
        font-size: 14px;
    }

    .ulartangga-loader {
        width: 36px;
        height: 36px;
        margin: 0 auto 8px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top-color: #ffc107;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .ulartangga-finished {
        color: #00ff99;
        font-weight: bold;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.6;
        }

        100% {
            opacity: 1;
        }
    }

    strong {
        color: #ccd437ff;
        font-size: 1.1rem;
    }

    #countdown-bar {
        width: 60%;
        height: 15px;
        margin: 20px auto;
        background-color: #222;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 10px #111;
        display: none;
    }

    #progress {
        width: 100%;
        height: 100%;
        background: linear-gradient(to left, rgba(107, 183, 255, 1), rgba(125, 85, 255, 1), rgba(189, 89, 255, 1));
        transition: width 1s linear;
    }

    #match-countdown-bar {
        width: 60%;
        height: 15px;
        margin: 20px auto;
        background-color: #222;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 10px #111;
        display: none;
    }

    #match-progress {
        width: 100%;
        height: 100%;
        background: linear-gradient(to left, rgba(107, 183, 255, 1), rgba(125, 85, 255, 1), rgba(189, 89, 255, 1));
        transition: width 1s linear;
    }

    @media (max-width: 768px) {

        #countdown-bar {
            width: 85%;
        }

        #match-countdown-bar {
            width: 85%;
        }
    }

    @media (max-width: 480px) {
        #countdown-bar {
            width: 90%;
            height: 12px;
        }

        #match-countdown-bar {
            width: 90%;
            height: 12px;
        }
    }

    #utBoard {
        width: 90vw;
        max-width: 600px;
        height: 90vw;
        max-height: 600px;
        display: grid;
        grid-template-columns: repeat(10, 1fr);
        grid-template-rows: repeat(10, 1fr);
        background: #111;
        border: 2px solid #333;
        position: relative;
        box-shadow: 0 0 20px rgba(0, 255, 255, 0.2);
    }

    .tile {
        border: 1px solid #222;
        font-size: 11px;
        color: #bbb;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .ladder {
        background: rgba(0, 150, 0, 0.35);
    }

    .snake {
        background: rgba(150, 0, 0, 0.35);
    }

    .ut-card {
        background: rgba(20, 20, 20, 0.8);
        border: 1px solid rgba(55, 199, 212, 0.3);
        padding: 12px;
        border-radius: 12px;
        margin-top: 15px;
        width: 92%;
        max-width: 600px;
        text-align: center;
    }

    .ut-btn {
        background: rgba(20, 20, 20, 0.8);
        border: 1px solid #ccd437ff;
        padding: 10px 25px;
        border-radius: 12px;
        cursor: pointer;
        color: #bcd437ff;
        margin: 5px;
    }

    #utLog {
        background: rgba(0, 0, 0, 0.5);
        padding: 10px;
        font-size: 13px;
        height: 150px;
        overflow-y: auto;
        border-radius: 10px;
        margin-top: 15px;
    }

    #dice-animation {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        filter: invert(1);
    }

    #dice-image {
        width: 80px;
        height: 80px;
        animation: dice-spin 0.5s linear infinite;
    }

    @keyframes dice-spin {
        0% {
            transform: rotate(0deg) scale(1);
        }

        50% {
            transform: rotate(180deg) scale(1.2);
        }

        100% {
            transform: rotate(360deg) scale(1);
        }
    }
</style>
<div class="ulartangga-container">
    <div class="ulartangga-card" id="ulartangga-intro">
        <h2 class="ulartangga-title">Selamat Datang di Ular Tangga Mystic Nusa</h2>
        <p class="ulartangga-desc">Permainan Ular Tangga dengan sentuhan mistis Indonesia.</p>
        <button class="ulartangga-btn" id="ulartangga-match-btn">Main Bersama</button>
    </div>


    <!-- ulartangga match CARD -->
    <div class="ulartangga-card" id="ulartangga-match-intro" style="display:none;">
        <h2 class="ulartangga-title">Ular Tangga Mystic Nusa</h2>
        <h3>Ruang Tersedia:</h3>
        <button class="ulartangga-btn-refresh" id="ulartangga-match-refresh-btn">Refresh</button>
        <div id="listMatches"></div>
        <div>
            <button class="ulartangga-btn" id="ulartangga-create-match-modal-btn">Buat Ruang</button>
            <button class="ulartangga-btn" id="ulartangga-exit-match-modal-btn">Kembali</button>
        </div>
    </div>

    <div class="ulartangga-card" id="ulartangga-match" style="display:none;">
        <h2 class="ulartangga-title">Ular Tangga Mystic Nusa!</h2>
        <div id="matchInfo" style="margin-bottom: 12px;"></div>
        <div id="listPlayers"></div>
        <button class="ulartangga-btn" id="ulartangga-ready-match-btn" value='1' style="display:none;">Siap</button>
        <button class="ulartangga-btn" id="ulartangga-not-ready-match-btn" value='0' style="display:none;">Batal</button>
        <button class="ulartangga-btn" id="ulartangga-exit-match-btn" onclick="exitPlayer()">Keluar</button>
        <button class="ulartangga-btn" id="ulartangga-start-match-btn" style="display:none;">Mulai bermain</button>
        <div id="ulartangga-match-message" style="margin-top: 12px;font-size: 12px;color:red;"></div>
    </div>

    <div class="ulartangga-card" id="ulartangga-match-board" style="display:none;">
        <div id="utBoard-container"></div>
        <div class="ut-card">
            <div id="dice-animation" style="display:none;">
                <img id="dice-image" src="" alt="Dice" />
            </div>
            <button class="ut-btn" id="rollBtn">🎲 Lempar Dadu</button>
            <div id="turnInfo" style="margin-top:10px;color:#ccc;font-size:14px;"></div>
        </div>
        <div class="ut-card">
            <h4>Event Log</h4>
            <div id="utLog"></div>
        </div>
    </div>

    <div class="ulartangga-card" id="ulartangga-match-result-card" style="display:none;">
        <h2 class="ulartangga-title">Hasil Permainan</h2>
        <h3 class="ulartangga-subtitle">🏆 Leaderboard</h3>
        <div id="ulartangga-match-leaderboard" class="ulartangga-leaderboard">
            <!-- Leaderboard akan dirender di sini -->
        </div>
        <!-- 🔮 Loader Animasi -->
        <div id="ulartangga-waiting-animation" class="ulartangga-waiting" style="display:none;">
            <div class="ulartangga-loader"></div>
            <p>Menunggu semua pemain menyelesaikan permainan...</p>
        </div>
        <div id="ulartangga-match-result-message" style="margin-top: 12px;font-size: 12px;color:yellow;"></div>
        <div class="ulartangga-actions">
            <button class="ulartangga-btn" onclick="matchIntroulartanggaGame();" id="ulartangga-match-quit-btn">Keluar</button>
        </div>
    </div>


    <div id="creatematchModal" class="mystic-modal" style="display:none;">
        <div class="mystic-modal-content">
            <span class="close-modal" onclick="closeModal('creatematchModal')">&times;</span>
            <div style="text-align: center;">
                <h2>Buat Ruang Baru</h2>
                <input class="ulartangga-input" id="name-match" placeholder="Nama ruang" required maxlength="50" /></br>
                <select class="ulartangga-input" id="max-player-match" required>
                    <option value="" disabled selected>Pilih Max Pemain</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select></br>
                <button class="ulartangga-btn" id="ulartangga-create-match-btn">Buat Ruang</button>
                <div id="matchMessage" style="margin-top: 12px;font-size: 12px;"></div>
            </div>
        </div>
    </div>
    <div id="joinmatchModal" class="mystic-modal" style="display:none;">
        <div class="mystic-modal-content">
            <span class="close-modal" onclick="closeModal('joinmatchModal')">&times;</span>
            <div style="text-align: center;">
                <h2>Bergabung ke Ruang</h2>
                <p id="join-match-name"></p>
                <p id="join-match-players-count" style="font-size: 12px;color:#bbb;margin-bottom:12px;"></p>
                <button class="ulartangga-btn" id="ulartangga-join-match-btn">Bergabung</button>
                <div id="matchJoinMessage" style="margin-top: 12px;font-size: 12px;"></div>
            </div>
        </div>
    </div>
</div>

<audio id="finish-match-sound" src="/sound/sfx/finish.mp3"></audio>
<audio id="move-pion" ><source src="/sound/sfx/move-pion.mp3"></audio>
<audio id="click-dice"><source src="/sound/sfx/click-dice.mp3"></audio>


<script>
    // const token = localStorage.getItem('token');
    let ulartanggamatchId = null;
    let ulartanggamatchplayers = null;
    let matchCheckInterval = null;
    let playerId = null;
    let roundTimer = null;
    let timeLeft = 10;
    let ulartanggamatchboard = null;
    const waitDiv = document.getElementById('ulartangga-waiting-animation');
    const msgDiv = document.getElementById('ulartangga-match-result-message');
    const quitBtn = document.getElementById('ulartangga-match-quit-btn');
    const leaderboardDiv = document.getElementById('ulartangga-match-leaderboard');
    const ulartanggaReadymatchBtn = document.getElementById('ulartangga-ready-match-btn');
    const ulartanggaNotReadymatchBtn = document.getElementById('ulartangga-not-ready-match-btn');
    const ulartanggaExitmatchBtn = document.getElementById('ulartangga-exit-match-btn');
    const rollBtn = document.getElementById('rollBtn');
    const utLog = document.getElementById('utLog');
    const movePion = document.getElementById('move-pion');
    movePion.volume = 0.5;
    const clickDice = document.getElementById('click-dice');
    clickDice.volume = 0.5;


    const finishmatchSound = document.getElementById('finish-match-sound');

    document.getElementById('ulartangga-match-btn').addEventListener('click', matchIntroulartanggaGame);
    document.getElementById('ulartangga-match-refresh-btn').addEventListener('click', matchIntroulartanggaGame);
    document.getElementById('ulartangga-create-match-btn').addEventListener('click', creatematchulartangga);
    document.getElementById('ulartangga-join-match-btn').addEventListener('click', joinmatchulartangga);
    document.getElementById('ulartangga-ready-match-btn').addEventListener('click', readymatchulartangga, 1);
    document.getElementById('ulartangga-not-ready-match-btn').addEventListener('click', readymatchulartangga, 0);
    document.getElementById('ulartangga-start-match-btn').addEventListener('click', startmatchulartangga);
    document.getElementById('ulartangga-create-match-modal-btn').addEventListener('click', async () => {
        openModal('creatematchModal');
    });
    document.getElementById('ulartangga-match-btn').addEventListener('click', matchIntroulartanggaGame);

    rollBtn.addEventListener('click', throwDiceulartangga);

    document.querySelectorAll('.numeric-only').forEach(input => {
        input.addEventListener('keypress', e => {
            if (e.key === 'e' || e.key === '.' || e.key === '-' || isNaN(e.key)) {
                e.preventDefault();
            }
        });

        input.addEventListener('paste', e => {
            const pasteData = e.clipboardData.getData('text');
            if (isNaN(pasteData) || parseFloat(pasteData) <= 0) {
                e.preventDefault();
            }
        });
    });

    function exitmatchulartangga() {
        document.getElementById('ulartangga-intro').style.display = 'block';
        document.getElementById('ulartangga-match').style.display = 'none';
        document.getElementById('ulartangga-match-intro').style.display = 'none';

    }


    // === match ===
    async function matchIntroulartanggaGame() {
        const listMatchesDiv = document.getElementById('listMatches');
        listMatchesDiv.innerHTML = '';
        document.getElementById('ulartangga-intro').style.display = 'none';
        document.getElementById('ulartangga-match-intro').style.display = 'block';
        document.getElementById('ulartangga-match').style.display = 'none';
        document.getElementById('ulartangga-match-board').style.display = 'none';
        document.getElementById('ulartangga-match-result-card').style.display = 'none';
        ulartanggaReadymatchBtn.style.display = 'none';
        ulartanggaNotReadymatchBtn.style.display = 'none';
        leaderboardDiv.innerHTML = '';
        msgDiv.innerHTML = '';
        quitBtn.style.display = 'in-line-block';
        waitDiv.style.display = 'none';
        ulartanggamatchId = null;

        const res = await fetch('/api/ulartangga/list-match', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            }
        });
        const data = await res.json();
        if (data.status == 'success') {
            playerId = data.player_id;
            if (data.matches.length == 0) {
                listMatchesDiv.innerHTML += '<p style="margin: 6px 0;">Tidak ada ruang yang tersedia.</p>';
                if (data.match_detail != null) {
                    ulartanggamatchId = data.match_detail.id;
                    continuematchulartangga(data.match_detail);
                }
            } else {
                data.matches.forEach(match => {
                    listMatchesDiv.innerHTML += `
                <p id="${match.id}" class="match-item" style="cursor:pointer;">
                    🧩 Ruang: <b>${match.name}</b> (${match.match_players_count}/${match.max_players} - ${match.status})
                </p>`;
                });
                if (data.match_detail != null) {
                    ulartanggamatchId = data.match_detail.id;
                    continuematchulartangga(data.match_detail);
                }
            }

            // tambahkan event click untuk buka modal
            document.querySelectorAll('.match-item').forEach(btn => {
                btn.addEventListener('click', () => {
                    ulartanggamatchId = btn.id;
                    document.getElementById('join-match-name').innerText = data.matches.find(r => r.id == ulartanggamatchId).name;
                    document.getElementById('join-match-players-count').innerText = `${data.matches.find(r => r.id == ulartanggamatchId).match_players_count}/${data.matches.find(r => r.id == ulartanggamatchId).max_players}`;
                    openModal('joinmatchModal');
                });
            });

        } else {
            listMatchesDiv.innerHTML += `<p style="color: red;">Gagal memuat ruang: ${data.message}</p>`;
        }
    }


    async function creatematchulartangga() {
        const name = document.getElementById('name-match').value;
        const maxPlayers = document.getElementById('max-player-match').value;
        if (!name || !maxPlayers) {
            document.getElementById('matchMessage').innerHTML = `<p style="color: red; font-size: 12px;">Nama ruang dan jumlah maksimal pemain harus diisi.</p>`;
            return;
        }
        if (maxPlayers < 2 || maxPlayers > 6) {
            document.getElementById('matchMessage').innerHTML = `<p style="color: red; font-size: 12px;">Jumlah maksimal pemain harus antara 2 dan 6.</p>`;
            return;
        }
        const res = await fetch('/api/ulartangga/create-match', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                max_players: maxPlayers,
            })
        });
        const data = await res.json();
        if (data.status == 'error') {
            document.getElementById('matchMessage').innerHTML = `<p style="color: red; font-size: 12px;">Gagal membuat match: ${data.message}</p>`;
            setTimeout(() => {
                document.getElementById('matchMessage').innerHTML = "";
            }, 3000);
            return;
        }
        closeModal('creatematchModal');
        playerId = data.player_id;
        ulartanggamatchId = data.match_detail.id;
        continuematchulartangga(data.match_detail);

    }

    async function joinmatchulartangga() {
        const res = await fetch('/api/ulartangga/join-match', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                match_id: ulartanggamatchId
            })
        });
        const data = await res.json();
        if (data.status == 'error') {
            document.getElementById('matchJoinMessage').innerHTML = `<p style="color: red;">${data.message}</p>`;
            setTimeout(() => {
                document.getElementById('matchJoinMessage').innerHTML = "";
            }, 3000);
            return;
        }
        ulartanggamatchId = data.match_detail.id;
        closeModal('joinmatchModal');
        continuematchulartangga(data.match_detail);
    }

    async function continuematchulartangga(data) {
        if (!ulartanggamatchId || ulartanggamatchId <= 0) {
            alert('match tidak ditemukan');
            return;
        }
        document.getElementById('ulartangga-match-intro').style.display = 'none';
        document.getElementById('ulartangga-match').style.display = 'block';
        const matchInfoDiv = document.getElementById('matchInfo');
        matchInfoDiv.innerHTML = '<h5></br>' + data.name + '</h5>';
        const playersDiv = document.getElementById('listPlayers');
        playersDiv.innerHTML = '<h5>Pemain dalam Ruang:</h5>';
        data.match_players.forEach(p => {
            const isHost = p.player_id === data.host_id; // siapa host match-nya
            const isSelf = p.player_id === playerId; // siapa user yang sedang login
            const isCurrentUserHost = playerId === data.host_id; // apakah user login adalah host
            if (isCurrentUserHost) {
                ulartanggaExitmatchBtn.style.display = 'none';
            } else {
                ulartanggaExitmatchBtn.style.display = 'inline-block';
            }

            const hostTag = isHost ? ' 👑' : '';
            const hostQuitTag = isHost && isSelf ?
                `<button class='btn btn-sm btn-danger' style='font-size:10px;padding:2px 4px;' onclick='exitmatch()'>Keluar</button> <button class='btn btn-sm btn-success' style='font-size:10px;padding:2px 4px;' onclick='createbot()'>+ Bot</button>` :
                '';

            const readyTag = p.is_ready ? ' ✅' : '';

            // Tombol kick hanya muncul jika user yg login adalah host dan target player bukan host
            const kickBtn = isCurrentUserHost && !isHost ?
                `<button class='btn btn-sm btn-danger' style='font-size: 10px;padding:2px;padding-top:1px;padding-bottom:1px;' onclick='kickPlayer("${p.player_id}")'>Kick</button>` :
                '';

            playersDiv.innerHTML += `
                <p style="margin-bottom:6px;margin-top:6px;">
                    🧩 ${p.name}${hostTag}${hostQuitTag}${readyTag} ${kickBtn}
                </p>`;
        });

        ulartanggamatchId = data.id;
        if (playerId === data.host_id) {
            document.getElementById('ulartangga-start-match-btn').style.display = 'inline-block';
        } else {
            document.getElementById('ulartangga-ready-match-btn').style.display = 'inline-block';
        }
        if (data.status === 'playing' && data.board_state) {
            ulartanggamatchboard = data.board_state;
            ulartanggamatchplayers = data.match_players;
            startOngoing();
            showulartanggamatchBoard();
            return;
        }
        startPolling();
    }

    async function pollmatchStatus() {
        if (!ulartanggamatchId) return;

        try {
            const response = await fetch('/api/ulartangga/active-match', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });
            const result = await response.json();

            if (result.status !== 'success') {
                ulartanggamatchId = null;
                stopPolling();
                alert('Anda di-kick atau Ruang telah dihapus.');
                matchIntroulartanggaGame();
                return;
            }


            const match = result.match_detail;
            const playersDiv = document.getElementById('listPlayers');
            playersDiv.innerHTML = '<h5>Pemain dalam Ruang:</h5>';

            match.match_players.forEach(p => {
                const isHost = p.player_id === match.host_id; // siapa host match-nya
                const isSelf = p.player_id === playerId; // siapa user yang sedang login
                const isCurrentUserHost = playerId === match.host_id; // apakah user login adalah host
                if (isCurrentUserHost) {
                    ulartanggaExitmatchBtn.style.display = 'none';
                } else {
                    ulartanggaExitmatchBtn.style.display = 'inline-block';
                }
                const hostTag = isHost ? ' 👑' : '';
                const hostQuitTag = isHost && isSelf ?
                    `<button class='btn btn-sm btn-danger' style='font-size:10px;padding:2px 4px;' onclick='exitmatch()'>Keluar</button> <button class='btn btn-sm btn-success' style='font-size:10px;padding:2px 4px;' onclick='createbot()'>+ Bot</button>` :
                    '';

                const readyTag = p.is_ready ? ' ✅' : '';

                // Tombol kick hanya muncul jika user yg login adalah host dan target player bukan host
                const kickBtn = isCurrentUserHost && !isHost ?
                    `<button class='btn btn-sm btn-danger' style='font-size: 10px;padding:2px;padding-top:1px;padding-bottom:1px;' onclick='kickPlayer("${p.player_id}")'>Kick</button>` :
                    '';

                playersDiv.innerHTML += `
        <p style="margin-bottom:6px;margin-top:6px;">
            🧩 ${p.name}${hostTag}${hostQuitTag}${readyTag} ${kickBtn}
        </p>`;
            });

            // update status teks

            if (playerId === match.host_id) {
                document.getElementById('ulartangga-start-match-btn').style.display = 'inline-block';
            } else {
                if (ulartanggaNotReadymatchBtn.style.display === 'none') {
                    ulartanggaReadymatchBtn.style.display = 'inline-block';
                }
            }
            if (match.state === 'finished') {
                stopPolling();
                showulartanggamatchResult();
                return;
            }
            if (match.board_state) {
                // stopPolling();
                ulartanggamatchboard = match.board_state;
                ulartanggamatchplayers = match.match_players;

                showulartanggamatchBoard();
            }

        } catch (error) {
            console.error('Gagal memuat status match:', error);
        }
    }

    function startPolling() {
        if (matchCheckInterval) clearInterval(matchCheckInterval);
        matchCheckInterval = setInterval(pollmatchStatus, 7000);
    }

    function stopPolling() {
        if (matchCheckInterval) clearInterval(matchCheckInterval);
    }
    async function exitmatch() {
        const res = await fetch('/api/ulartangga/match/exit', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                match_id: ulartanggamatchId
            })
        });
        const data = await res.json();
        if (data.status == 'error') {
            document.getElementById('ulartangga-match-message').innerText = data.message || 'Gagal keluar dari match. Silakan coba lagi.';
            setTimeout(() => {
                document.getElementById('ulartangga-match-message').innerText = "";
            }, 3000);
            return;
        }
        isHost = false;
        document.getElementById('ulartangga-start-match-btn').style.display = 'none';
        matchIntroulartanggaGame();
    }

    async function exitPlayer() {
        if (!confirm('Apakah Anda yakin ingin keluar dari ruang?')) {
            return;
        }
        const res = await fetch('/api/ulartangga/match/exit-player', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                match_id: ulartanggamatchId
            })
        });

        const data = await res.json();
        if (data.status == 'error') {
            document.getElementById('ulartangga-match-message').innerText = data.message || 'Gagal keluar dari ruang. Silakan coba lagi.';
            setTimeout(() => {
                document.getElementById('ulartangga-match-message').innerText = "";
            }, 3000);
            return;
        }
        matchIntroulartanggaGame();
    }
    async function kickPlayer(playerId) {
        if (!confirm('Apakah Anda yakin ingin mengeluarkan pemain ini dari ruang?')) {
            return;
        }
        await fetch('/api/ulartangga/match/kick-player', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                match_id: ulartanggamatchId,
                player_id: playerId
            })
        });

        await pollmatchStatus(); // refresh manual setelah kick
    }

    async function createbot() {
        const res = await fetch('/api/ulartangga/match/create-bot', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                match_id: ulartanggamatchId
            })
        });
        const data = await res.json();
        if (data.status == 'error') {
            document.getElementById('ulartangga-match-message').innerText = data.message || 'Gagal keluar dari match. Silakan coba lagi.';
            setTimeout(() => {
                document.getElementById('ulartangga-match-message').innerText = "";
            }, 3000);
            return;
        }
        await pollmatchStatus(); // refresh manual setelah tambah bot
    }

    async function readymatchulartangga(isReady) {
        readyVal = isReady.target.value;
        const res = await fetch('/api/ulartangga/match/ready', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                match_id: ulartanggamatchId,
                is_ready: readyVal
            })
        });
        const data = await res.json();

        if (data.status == 'error') {
            document.getElementById('ulartangga-match-message').innerText = data.message || 'Gagal menandai siap. Silakan coba lagi.';
            setTimeout(() => {
                document.getElementById('ulartangga-match-message').innerText = "";
            }, 3000);
            return;
        }
        if (readyVal == 1) {
            ulartanggaReadymatchBtn.style.display = 'none';
            ulartanggaNotReadymatchBtn.style.display = 'inline-block';
            return;
        } else {
            ulartanggaReadymatchBtn.style.display = 'inline-block';
            ulartanggaNotReadymatchBtn.style.display = 'none';
        }
    }
    async function startmatchulartangga() {
        stopPolling();
        const res = await fetch('/api/ulartangga/match/start', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                match_id: ulartanggamatchId
            })
        });
        const data = await res.json();
        if (data.status == 'error') {
            startPolling();
            document.getElementById('ulartangga-match-message').innerText = data.message || 'Gagal memulai permainan. Silakan coba lagi.';
            setTimeout(() => {
                document.getElementById('ulartangga-match-message').innerText = "";
            }, 3000);
            return;
        }
        if (data.status == 'success') {
            startOngoing();
            ulartanggamatchboard = data.match_detail.board_state;
            ulartanggamatchplayers = data.match_detail.match_players;
            showulartanggamatchBoard();
        }
    }

    async function showulartanggamatchBoard() {
        document.getElementById('ulartangga-match-board').style.display = "block";
        document.getElementById('ulartangga-match').style.display = "none";

        const container = document.getElementById('utBoard-container');
        container.innerHTML = "";

        const board = ulartanggamatchboard; // array 1–100

        // --- TRANSFORMASI BOARD → GRID ULAR TANGGA ---
        const rows = [];
        for (let i = 0; i < 10; i++) {
            let start = i * 10;
            let row = board.slice(start, start + 10);

            // Zigzag → baris ganjil dibalik
            if (i % 2 === 1) row.reverse();

            rows.push(row);
        }

        rows.reverse(); // Biar baris 1 ada di bawah

        // --- Grid 10x10 ---
        const boardGrid = document.createElement('div');
        boardGrid.style.display = "grid";
        boardGrid.style.gridTemplateColumns = "repeat(10, 1fr)";
        boardGrid.style.gap = "4px";
        boardGrid.style.background = "url('/images/asset/ulartangga/ulartangga-board.webp') center/cover no-repeat";
        boardGrid.style.padding = "10px";

        rows.flat().forEach(tile => {
            const cell = document.createElement('div');
            cell.classList.add('ulartangga-tile');
            cell.style.border = "1px solid #444";
            cell.style.padding = "8px";
            cell.style.background = "rgba(0,0,0,0.2)";
            cell.style.color = "#fbff00ff";
            cell.style.fontSize = "12px";
            cell.style.position = "relative";
            cell.style.minHeight = "60px";

            const num = document.createElement('div');
            num.innerText = tile.number;
            num.style.position = "absolute";
            num.style.top = "4px";
            num.style.right = "6px";
            num.style.opacity = "1";
            num.style.textShadow = "0 0 2px black";
            cell.appendChild(num);

            if (tile.effect === "ladder") {
                const mark = document.createElement('div');
                mark.innerText = "↥ " + tile.effect_target;
                mark.style.position = "absolute";
                mark.style.bottom = "6px";
                mark.style.left = "6px";
                mark.style.color = "#00ff88";
                mark.style.fontSize = "11px";
                cell.appendChild(mark);
            }
            if (tile.effect === "snake") {
                const mark = document.createElement('div');
                mark.innerText = "↧ " + tile.effect_target;
                mark.style.position = "absolute";
                mark.style.bottom = "6px";
                mark.style.left = "6px";
                mark.style.color = "#ff4444";
                mark.style.fontSize = "11px";
                cell.appendChild(mark);
            }

            // TANDA TEMPAT PION AKAN DIPASANG
            cell.setAttribute("data-tile", tile.number);

            boardGrid.appendChild(cell);
        });
        const startGrid = document.createElement('div');
        startGrid.className = 'ulartangga-tile';
        startGrid.setAttribute('data-tile', '0');
        startGrid.style.gridColumn = 'span 10';
        startGrid.style.border = '1px solid rgb(68, 68, 68)';
        startGrid.style.padding = '8px';
        startGrid.style.background = 'rgba(0, 0, 0, 0.2)';
        startGrid.style.color = 'rgb(251, 255, 0)';
        startGrid.style.fontSize = '12px';
        startGrid.style.position = 'relative';
        startGrid.style.display = 'flex';
        startGrid.style.justifyContent = 'center';
        startGrid.style.alignItems = 'center';
        startGrid.style.minHeight = '60px';

        const startText = document.createElement('div');
        startText.style.position = 'absolute';
        startText.style.top = '4px';
        startText.style.left = '6px';
        startText.style.opacity = '0.6';
        startText.innerText = 'Mulai disini';
        startGrid.style.textAlign = "center";
        startGrid.appendChild(startText);
        boardGrid.appendChild(startGrid);
        container.appendChild(boardGrid);

        // Setelah papan selesai, render pion
        renderPlayerPieces();
    }

    async function throwDiceulartangga() {
        const turnIndfoDiv = document.getElementById('turnInfo');
        turnIndfoDiv.innerText = 'Sedang melempar dadu...';

        // if (typeof clickDice !== "undefined") {
        //     clickDice.currentTime = 0;
        //     clickDice.play();
        // }
        const res = await fetch('/api/ulartangga/match/throw-dice', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                match_id: ulartanggamatchId,
            })
        });

        const data = await res.json();

        if (data.status == 'error') {
            if (data.next_turn_is_bot) {
                setTimeout(() => {
                    botThrowDiceulartangga();
                }, 2000);
            }
            turnIndfoDiv.innerText = data.message;
            setTimeout(() => turnIndfoDiv.innerText = "", 3000);
            return;
        }

        if (data.status == 'success') {

            showDiceAnimation(data.dice);
            // --- UPDATE PLAYERS STATE ---
            ulartanggamatchplayers = data.match_players;

            // --- ANIMASIKAN PION YANG MELEMPAR (BISA KAMU ATAU LAWAN) ---
            setTimeout(() => animatePiece(data.player_id, data.from, data.to), 2000);

            // --- TAMPILKAN LOG ---
            utLog.innerHTML += `<p>🎲 ${data.player_name} melempar dadu dan mendapatkan ${data.dice}. Pindah dari ${data.from} ke ${data.to}.</p><br>`;

            // Jika kamu yang lempar
            if (data.player_id === playerId) {
                turnIndfoDiv.innerText = `Anda mendapat ${data.dice} → ${data.from} → ${data.to}`;
            } else {
                turnIndfoDiv.innerText = `${data.player_name} berpindah ke ${data.to}`;
            }
            if (data.next_turn_is_bot) {
                setTimeout(() => {
                    botThrowDiceulartangga();
                }, 5000);
            }
        }
    }

    async function botThrowDiceulartangga() {
        const turnIndfoDiv = document.getElementById('turnInfo');
        turnIndfoDiv.innerText = 'Bot Sedang melempar dadu...';

        const res = await fetch('/api/ulartangga/match/bot-throw-dice', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                match_id: ulartanggamatchId,
            })
        });

        const data = await res.json();

        if (data.status == 'error') {
            if (data.next_turn_is_bot) {

                setTimeout(() => {
                    botThrowDiceulartangga();
                }, 2000);
            }
            turnIndfoDiv.innerText = data.message;
            setTimeout(() => turnIndfoDiv.innerText = "", 3000);
            return;
        }

        if (data.status == 'success') {

            // --- UPDATE PLAYERS STATE ---
            ulartanggamatchplayers = data.match_players;

            // --- ANIMASIKAN PION YANG MELEMPAR (BISA KAMU ATAU LAWAN) ---
            animatePiece(data.player_id, data.from, data.to);

            // --- TAMPILKAN LOG ---
            utLog.innerHTML += `<p>🎲 ${data.player_name} melempar dadu dan mendapatkan ${data.dice}. Pindah dari ${data.from} ke ${data.to}.</p><br>`;

            // Jika kamu yang lempar
            if (data.player_id === playerId) {
                turnIndfoDiv.innerText = `Anda mendapat ${data.dice} → ${data.from} → ${data.to}`;
            } else {
                turnIndfoDiv.innerText = `${data.player_name} berpindah ke ${data.to}`;
            }
            if (data.next_turn_is_bot) {
                setTimeout(() => {
                    botThrowDiceulartangga();
                }, 2000);
            }
        }
    }


    function renderPlayerPieces() {
        const players = ulartanggamatchplayers;

        players.forEach(p => {
            const tileCell = document.querySelector(`[data-tile="${p.position}"]`);
            if (!tileCell) return;

            const old = document.querySelector(`#piece-${p.player_id}`);
            if (old) old.remove();

            const piece = document.createElement('img');
            piece.id = `piece-${p.player_id}`;
            piece.src = `${p.player_state.avatar}`;
            piece.style.width = "20px";
            piece.style.height = "20px";
            piece.style.borderRadius = "50%";
            piece.style.position = "absolute"; // PENTING!
            piece.style.bottom = "4px";
            piece.style.right = "4px";
            piece.style.border = "2px solid #fff";
            piece.style.objectFit = "cover";
            tileCell.appendChild(piece);
        });
    }


    function animatePiece(playerId, from, to) {

        const player = ulartanggamatchplayers.find(p => p.player_id === playerId);
        if (!player) return;

        let current = from;

        function step() {
            if (current >= to) {
                player.position = to;
                renderPlayerPieces();
                return;
            }

            current++;

            const oldPiece = document.querySelector(`#piece-${playerId}`);
            if (oldPiece) oldPiece.remove();

            const cell = document.querySelector(`[data-tile="${current}"]`);

            if (cell) {
                const piece = document.createElement('img');
                piece.id = `piece-${playerId}`;
                piece.src = `${player.player_state.avatar}`;
                piece.style.width = "20px";
                piece.style.height = "20px";
                piece.style.borderRadius = "50%";
                piece.style.bottom = "4px";
                piece.style.right = "4px";
                piece.style.border = "2px solid #fff";
                piece.style.objectFit = "cover";
                cell.appendChild(piece);

                if (typeof movePion !== "undefined") {
                    movePion.currentTime = 0;
                    movePion.play();
                }
            }

            setTimeout(step, 300);
        }

        step();
    }

    function startOngoing() {
        if (matchCheckInterval) clearInterval(matchCheckInterval);
        matchCheckInterval = setInterval(ongoingMatchUlartangga, 7000);
    }

    function stopOngoing() {
        if (matchCheckInterval) clearInterval(matchCheckInterval);
    }

    async function ongoingMatchUlartangga() {
        const turnIndfoDiv = document.getElementById('turnInfo');
        const res = await fetch('/api/ulartangga/match/ongoing-match', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                match_id: ulartanggamatchId
            })
        });
        const data = await res.json();
        if (data.status == 'error') {
            stopOngoing();
            turnIndfoDiv.innerText = `${data.message}`;
            return;
        }
        if (data.status == 'success') {
            // --- UPDATE PLAYERS STATE ---
            ulartanggamatchplayers = data.match_players;

            if (data.match_detail.status === 'finished') {
                stopOngoing();
                showulartanggamatchResult();
                return;
            }

            // --- ANIMASIKAN PION YANG MELEMPAR (BISA KAMU ATAU LAWAN) ---
            animatePiece(data.player_id, data.from, data.to);

            // --- TAMPILKAN LOG ---
            utLog.innerHTML += `<p>🎲 ${data.player_name} melempar dadu dan mendapatkan ${data.dice}. Pindah dari ${data.from} ke ${data.to}.</p><br>`;

            // Jika kamu yang lempar
            if (data.player_id === playerId) {
                turnIndfoDiv.innerText = `Anda mendapat ${data.dice} → ${data.from} → ${data.to}`;
            } else {
                // turnIndfoDiv.innerText = `${data.player_name} berpindah ke ${data.to}`;
            }
            if (data.next_turn_is_bot) {
                setTimeout(() => {
                    botThrowDiceulartangga();
                }, 5000);
            }
            animatePiece(data.player_id, data.from, data.to);

        }
    }

    let ulartanggaPollingInterval = null;

    function showulartanggamatchResult() {
        document.getElementById('ulartangga-match-board').style.display = "none";
        document.getElementById('ulartangga-match-result-card').style.display = "block";

        leaderboardDiv.innerHTML = '';
        msgDiv.innerHTML = '';
        //renderpemenang dari matchplayer position descending berdasarkan position
        leaderboardDiv.innerHTML = ulartanggamatchplayers
            .sort((a, b) => b.position - a.position)
            .map((p, i) =>
                `<p>${i + 1}. ${p.name} — Posisi: ${p.position}</p>`
            ).join('');
    }


    async function pollulartanggamatchResult() {
        try {
            const res = await fetch(`/api/ulartangga/match/finish`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    match_id: ulartanggamatchId
                })
            });

            const data = await res.json();
            if (data.status !== 'success') return;

            if (data.match_finished !== true) {
                // Tampilkan animasi loading
                quitBtn.disabled = true;
            } else {
                leaderboardDiv.innerHTML = data.leaderboard.map((p, i) =>
                    `<p>${i + 1}. ${p.name} — ${p.score} poin (${p.duration}s)</p>`
                ).join('');
                // Semua sudah selesai
                waitDiv.style.display = 'none';
                msgDiv.innerHTML = `<span class="ulartangga-finished">✨ Pemenang pertama adalah : <strong>${data.leaderboard[0].name}</strong>! ✨</span>`;
                quitBtn.disabled = false;
                finishmatchSound.volume = 0.3;
                finishmatchSound.currentTime = 0;
                finishmatchSound.play();
                clearInterval(ulartanggaPollingInterval);
            }

        } catch (err) {
            console.error('Polling error:', err);
        }
    }

    function showDiceAnimation(resultNumber) {
        const diceBox = document.getElementById("dice-animation");
        const diceImg = document.getElementById("dice-image");

        diceBox.style.display = "block";

        // Start random spin
        diceImg.style.animation = "dice-spin 0.2s linear infinite";

        // Ganti-ganti angka setiap 120ms
        let spinInterval = setInterval(() => {
            const randomNum = Math.floor(Math.random() * 6) + 1;
            diceImg.src = `/images/asset/ulartangga/dice${randomNum}.png`;
        }, 120);

        // Setelah 1 detik → stop spin dan tampilkan angka asli
        setTimeout(() => {
            clearInterval(spinInterval);
            diceImg.style.animation = "none"; // stop rotation
            diceImg.src = `/images/asset/ulartangga/dice${resultNumber}.png`;
        }, 1000);

        // Hilangkan animasi setelah 2 detik
        setTimeout(() => {
            diceBox.style.display = "none";
        }, 2200);
    }
</script>
@endsection