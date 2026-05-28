@extends('layouts.ngepet')

@section('content')
<style>
    body {
        padding: 0rem;
        padding-top: 5rem;
        align-items: center;
        justify-content: center;
        font-family: 'Calistoga';
        font-size: 0.75rem;

    }

    .container {
        max-width: 900px;
        margin: auto;
        padding-top: 0rem !important;
    }

    h1 {
        top: 10%;
        font-size: clamp(1.8rem, 5vw, 2.8rem);
        text-shadow: 0 0 8px #facc15;
        color: rgb(240, 205, 65);
        margin-bottom: 1rem;
        line-height: 1.2;
        text-align: center;
    }

    .match-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: #000;
        color: #fff;
        border: 3px solid #999;
        border-radius: 8px;
        margin: 10px 0;
        margin-bottom: 20px;
        transition: 0.3s;
        cursor: pointer;
        flex-wrap: wrap;
    }

    .button-lobby {
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: 0.3s;
        flex-wrap: wrap;
    }

    /* COMMON */
    .tier-default {
        border-color: #ffffffff;
        box-shadow: 0 0 10px #b3b3b366, 0 0 20px #8b919133;
    }

    .tier-common {
        border-color: #00f0ff;
        box-shadow: 0 0 10px #00f0ff66, 0 0 20px #00f0ff33;
    }

    /* UNCOMMON */
    .tier-uncommon {
        border-color: #1eff00;
        box-shadow: 0 0 10px #1eff0066, 0 0 20px #1eff0033;
    }

    /* RARE */
    .tier-rare {
        border-color: #0070dd;
        box-shadow: 0 0 10px #0070dd66, 0 0 20px #0070dd33;
    }

    /* MYTHICAL */
    .tier-mythical {
        border-color: #a335ee;
        box-shadow: 0 0 12px #a335ee88, 0 0 25px #a335ee44;
    }

    /* LEGENDARY */
    .tier-legendary {
        border-color: #ff6200ff;
        box-shadow: 0 0 12px #ff6200bd, 0 0 30px #ff6200a7;
    }

    /* Tambahan efek saat hover */
    .match-item:hover {
        transform: scale(1.02);
        box-shadow: 0 0 20px currentColor, 0 0 40px currentColor;
    }

    /* Hover Glow Berdasarkan Tier */
    .tier-common:hover {
        box-shadow: 0 0 20px #00f0ffaa, 0 0 40px #00f0ff88;
    }

    .tier-uncommon:hover {
        box-shadow: 0 0 20px #1eff00aa, 0 0 40px #1eff0088;
    }

    .tier-rare:hover {
        box-shadow: 0 0 20px #0070ddaa, 0 0 40px #0070dd88;
    }

    .tier-mythical:hover {
        box-shadow: 0 0 22px #a335eeaa, 0 0 45px #a335ee88;
    }

    .tier-legendary:hover {
        box-shadow: 0 0 25px #ff6200ff, 0 0 50px #ff6200a7;
    }

    .match-left {
        display: flex;
        align-items: center;
    }

    .match-left img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 15px;
    }

    .match-info {
        display: flex;
        flex-direction: column;
    }



    .btn-intruder,
    .btn-room,
    .btn-open-house,
    .btn-refresh {
        padding: 6px 12px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        background: #00f0ff;
        color: #000;
        font-weight: bold;
        margin: 3px;
        transition: all 0.3s ease;
    }

    .btn-refresh:hover {
        scale: 1.05;
    }

    .btn-open-house {
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .match-item {
            flex-direction: column;
            /* ubah jadi vertikal */
            align-items: flex-start;
            /* rapat ke kiri */
        }

        .match-left {
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 100%;
        }

        .match-left img {
            width: 100px;
            height: auto;
        }

        .token-count,
        .btn-intruder,
        .btn-detail {
            margin-top: 10px;
            width: 100%;
            text-align: center;
        }
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1055;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: #111;
        padding: 20px;
        padding-bottom: 10px;
        border-radius: 8px;
        color: #fff;
        width: 800px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    /* Tier Variants */
    .modal-content.tier-default {
        border: 2px solid #ffffffff;
        box-shadow: 0 0 20px #bbbbbb88;
    }

    .modal-content.tier-common {
        border: 2px solid #00f0ff;
        box-shadow: 0 0 20px #00f0ff88;
    }

    .modal-content.tier-uncommon {
        border: 2px solid #1eff00;
        box-shadow: 0 0 20px #1eff0088;
    }

    .modal-content.tier-rare {
        border: 2px solid #0070dd;
        box-shadow: 0 0 20px #0070dd88;
    }

    .modal-content.tier-mythical {
        border: 2px solid #a335ee;
        box-shadow: 0 0 25px #a335ee88;
    }

    .modal-content.tier-legendary {
        border: 2px solid #ff6200ff;
        box-shadow: 0 0 30px #ff6200a7;
    }

    .close {
        cursor: pointer;
        font-size: 18px;
    }

    /* Ganti nama agar tidak bentrok dengan bootstrap */
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        justify-content: center;
        align-items: center;
    }

    .custom-modal .modal-content {
        background: #111;
        padding: 20px;
        border-radius: 8px;
        border: 2px solid #00f0ff;
        color: #fff;
        width: 400px;
    }

    .game-button {
        /* background: linear-gradient(145deg, #3a0ca3, #7209b7); */
        background: transparent;
        color: #facc15;
        font-size: 1.1rem;
        padding: 12px 24px;
        border: none;
        border-bottom: 2px solid #facc15;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        text-shadow: 0 0 8px #facc15;
        /* box-shadow: 0 0 10px rgba(114, 9, 183, 0.7),
            0 0 25px rgba(114, 9, 183, 0.4); */
        transition: all 0.3s ease;
        bottom: 15px;
        right: 15px;
    }

    /* Hover: glow lebih kuat */
    .game-button:hover {
        scale: 1.05;
        text-shadow: 0 0 8px #facc15;
    }

    @keyframes pulse-button {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(0.95);
        }

        100% {
            transform: scale(1);
        }
    }

    #tokenSliderWrapper.disabled {
        opacity: 0.5;
        pointer-events: none;
    }

    #tokenSliderWrapper label {
        font-weight: bold;
        margin-top: 5px;
    }

    input[type="range"] {
        width: 100%;
        height: 12px;
        background: linear-gradient(to right, #7b583f, #3e2c1f);
        border: 2px solid #5d422f;
        border-radius: 6px;
        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.5), 0 2px 2px rgba(255, 255, 255, 0.1);
        outline: none;
        cursor: pointer;
    }

    /* Thumb (Tombol geser) */
    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        background: #d4ac00;
        border: 2px solid #8f6a00;
        border-radius: 4px;
        box-shadow: 0 0 10px rgba(212, 172, 0, 0.8), inset 0 2px 4px rgba(255, 255, 255, 0.5);
        cursor: pointer;
    }

    input[type="range"]::-moz-range-thumb {
        width: 18px;
        height: 18px;
        background: #d4ac00;
        border: 2px solid #8f6a00;
        border-radius: 4px;
        box-shadow: 0 0 10px rgba(212, 172, 0, 0.8), inset 0 2px 4px rgba(255, 255, 255, 0.5);
        cursor: pointer;
    }

    /* Track (Jalur geser) */
    input[type="range"]::-webkit-slider-runnable-track {
        background: transparent;
    }

    input[type="range"]::-moz-range-track {
        background: transparent;
    }

    /* Sembunyikan radio button asli */
    input[type="radio"] {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    /* Gaya label saat tidak terpilih */
    input[type="radio"] {
        background-color: #343a40;
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        border: 2px solid #6c757d;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s ease;
        font-family: 'Arial', sans-serif;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    /* Gaya label saat di-hover */
    input[type="radio"]:hover {
        background-color: #495057;
        border-color: #ced4da;
    }

    /* Gaya label saat terpilih */
    input[type="radio"]:checked {
        background-color: #d4ac00;
        border-color: #8f6a00;
        box-shadow: 0 0 15px rgba(212, 172, 0, 0.8);
        color: #fff;
        transform: scale(1.05);
    }

    /* Sembunyikan radio button asli */
    input[type="checkbox"] {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    /* Gaya label saat tidak terpilih */
    input[type="checkbox"] {
        background-color: #343a40;
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        border: 2px solid #6c757d;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s ease;
        font-family: 'Arial', sans-serif;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    /* Gaya label saat di-hover */
    input[type="checkbox"]:hover {
        background-color: #495057;
        border-color: #ced4da;
    }

    /* Gaya label saat terpilih */
    input[type="checkbox"]:checked {
        background-color: #d4ac00;
        border-color: #8f6a00;
        box-shadow: 0 0 15px rgba(212, 172, 0, 0.8);
        color: #fff;
        transform: scale(1.05);
    }
</style>


<div class="container">
    <h1>Daftar Match</h1>

    <!-- Tombol refresh -->
    <div class="button-lobby">
        <button class="game-button" id="btnOpenHouse" data-bs-toggle="modal" data-bs-target="#modalOpenHouse" onclick="valtokenslider();loadAvatarHouse();">🏠 Buka Rumah</button>
        <button class="btn-refresh" id="btnRefresh">🔄 Refresh</button>
    </div>
    <!-- List awal dari Blade -->
    <div id="matchList">
        @foreach($matches as $match)
        @php
        $tier = $match['house_avatar']['tier'];
        $tierClass = match($tier) {
        'common' => 'tier-common',
        'uncommon' => 'tier-uncommon',
        'rare' => 'tier-rare',
        'mythical' => 'tier-mythical',
        'legendary' => 'tier-legendary',
        default => 'tier-default',
        };
        @endphp

        <div class="match-item {{ $tierClass }}">
            <div class="match-left">
                <img src="{{ asset($match['house_avatar']['image_url']) }}" alt="{{ $match['house_avatar']['name'] }}">
                <div class="match-info">
                    <h3>{{ $match['house_avatar']['name'] }}</h3>
                    Host: {{ $match['host_name'] }}</br>
                    Tingkat Kesulitan: {{ $match['difficulty'] }}</br>
                </div>
            </div>
            <div class="token-count">
                Token: {{ $match['token_pool'] ?? 0 }}/{{ $match['total_token_pool'] ?? 0 }}</br>
                Penyusup: {{ $match['intruders_count'] }}/{{ $match['max_intruders'] }}</br>
            </div>
            <div class="btn-intruder">
                <button
                    class="btn-intruder btn-detail"
                    data-id="{{ $match['id'] }}"
                    data-name="{{ $match['house_avatar']['name'] }}"
                    data-host="{{ $match['host_name'] }}"
                    data-token="{{ $match['token_pool'] ?? 0 }}"
                    data-totaltoken="{{ $match['total_token_pool'] ?? 0 }}"
                    data-image="{{ asset($match['house_avatar']['image_url']) }}"
                    data-tier="{{ $match['house_avatar']['tier'] }}"
                    data-intruders="{{ $match['intruders_count'] ?? 0 }}"
                    data-max="{{ $match['max_intruders'] ?? 0 }}"
                    data-duration="{{ $match['guess_duration_hours'] ?? 0 }}"
                    data-difficulty="{{ $match['difficulty'] ?? 'medium' }}"
                    data-mintoken="{{ $match['min_intruder_token'] ?? '' }}"
                    data-maxtoken="{{ $match['max_intruder_token'] ?? '' }}">
                    Lihat Detail
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="matchDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header m-0 p-2">
                <h2 class="modal-title">🏠 <strong><span id="modalHouseName"></span></strong></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center mt-0 mb-0">
                <img id="modalHouseImage" src="" alt="House" class="img-fluid mb-3" style="max-height:300px;">
                <p class=" mt-0 mb-0">Host: <strong><span id="modalHostName"></span></strong></p>
                <p class=" mt-0 mb-0">Token: <strong><span id="modalTokenPool"></span>/<span id="modalTotalTokenPool"></span></strong></p>
                <p class=" mt-0 mb-0">Tingkat Kesulitan: <strong><span id="modalDifficulty"></span></strong></p>
                <p class=" mt-0 mb-0">Durasi Waktu: <strong><span id="modalDuration"></span></strong> Jam</p>
                <p class=" mt-0 mb-0">Penyusup: <strong><span id="modalIntrudersCount"></span>/<span id="modalMaxIntruders"></span></strong> </p>
                <p class=" mt-0 mb-0">Batasan Token: <strong><span id="modalMinToken"></span> - <span id="modalMaxToken"></span></strong> </p>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <button id="btnJoinIntruderModal" class="btn btn-primary w-40" style="width:200px;">🐷 Curi</button>
                <button id="btnPreview" class="btn btn-primary w-40" style="width:200px;">🏠 Lihat</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="matchJoinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-black text-white">
            <div class="modal-header">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center mt-0 mb-0">
                <!-- Form Input Intruders -->
                <div class="mb-3 mt-4">
                    <label for="inputIntruderName" class="form-label">Nama Intruder</label>
                    <input type="text" class="form-control" id="inputIntruderName" placeholder="Masukkan nama kamu">
                </div>
                <div class="mb-3">
                    <label for="inputIntruderToken" class="form-label">Jumlah Token</label>
                    <input type="input" class="form-control" id="inputIntruderToken" min="100" placeholder="Masukkan jumlah token" required>
                    </br>
                    <input type="range" id="intruderTokenPoolSlider" name="intruder_token_pool" min="100" max="100000" value="0" step="100">
                </div>
                <div class="container my-4">
                    <h3 class="text-center">🧙🏻‍♂️ Pilih Avatar</h3>
                    <div id="avatarList" class="row g-3 justify-content-center"></div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <button id="btnJoinIntruder" class="btn btn-primary w-40" style="width:200px;">🐷 Curi</button>
                <!-- Tempat menampilkan pesan response -->
                <div id="joinIntruderResponse" class="w-100 text-center"></div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Buka Rumah (Bootstrap) -->
<div class="modal fade" id="modalOpenHouse" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-info">
            <div class="modal-header">
                <h5 class="modal-title"><strong>🏠 Buka Rumah</strong></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formOpenHouse">
                    <!-- Nama Host -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Host</label>
                        <input id='hostname' type="text" name="host_name" class="form-control" placeholder="Masukkan nama host">
                    </div>

                    <!-- Avatar House -->

                    <div class="mb-3">
                        <h5 class="text-center">🏠 Pilih Avatar Rumah</h5>
                        <div id="avatarhouseList" class="row g-3 justify-content-center"></div>
                    </div>

                    <!-- Token Pool -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Token Pool: </label>
                        <input type="input" class="form-control" id="token_pool" min="100" placeholder="Masukkan jumlah token" required></br>
                        <input type="range" id="tokenPoolSlider" name="token_pool" min="100" max="100000" value="0" step="100">
                    </div>

                    <!-- Difficulty -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Difficulty</label>
                        <div class="d-flex gap-3">
                            <div class="d-flex align-items-center gap-2"><input type="radio" name="difficulty" value="easy" checked> Easy</div>
                            <div class="d-flex align-items-center gap-2"><input type="radio" name="difficulty" value="medium"> Medium</div>
                            <div class="d-flex align-items-center gap-2"><input type="radio" name="difficulty" value="hard"> Hard</div>
                        </div>
                    </div>

                    <!-- Guess Duration -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Durasi Tebak (Jam)</label>
                        <div class="d-flex gap-3">
                            <div class="d-flex align-items-center gap-2"><input type="radio" name="guess_duration_hours" value="24" checked> 24 Jam</div>
                            <div class="d-flex align-items-center gap-2"><input type="radio" name="guess_duration_hours" value="12"> 12 Jam</div>
                            <div class="d-flex align-items-center gap-2"><input type="radio" name="guess_duration_hours" value="6"> 6 Jam</div>
                            <div class="d-flex align-items-center gap-2"><input type="radio" name="guess_duration_hours" value="3"> 3 Jam</div>
                        </div>
                    </div>

                    <!-- Max Intruders -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah Maksimum Intruder</label>
                        <div class="d-flex gap-3 align-items-center">
                            <div class="d-flex align-items-center gap-2"><input type="radio" name="max_intruders" value="1"> 1</div>
                            <div class="d-flex align-items-center gap-2"><input type="radio" name="max_intruders" value="2"> 2</div>
                            <div class="d-flex align-items-center gap-2"><input type="radio" name="max_intruders" value="3" checked> 3</div>
                            <div class="d-flex align-items-center gap-2"><input type="radio" name="max_intruders" value="4"> 4</div>
                            <div class="d-flex align-items-center gap-2"><input type="radio" name="max_intruders" value="5"> 5</div>
                        </div>
                    </div>

                    <!-- Min & Max Token Intruder -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Atur Token Intruder (Opsional)</label>
                        <div class="form-check mb-2 d-flex align-items-end gap-2">
                            <input class="form-check-input" type="checkbox" id="enableTokenLimit">
                            <label class="form-check-label">Aktifkan pengaturan min & max token intruder</label>
                        </div>
                        <div id="tokenSliderWrapper" class="disabled">
                            <label>Minimal Token: <span id="minTokenValue">0</span></label>
                            <input type="range" id="minTokenSlider" name="min_intruder_token" min="100" max="100000" value="0" step="100" disabled>
                            <label>Maksimal Token: <span id="maxTokenValue">0</span></label>
                            <input type="range" id="maxTokenSlider" name="max_intruder_token" min="100" max="100000" value="0" step="100" disabled>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="d-flex justify-content-center">
                        <button id="btnOpenhouse" type="submit" class="game-button">🚪 Buka Rumah</button>
                    </div>
                    <div id="openhouseResponse" class="w-100 text-center pt-3"></div>
                </form>
            </div>
        </div>
    </div>
</div>


<div id="exitMenu" class="exit-menu-container">
    <div id="menuButtons" class="menu-icons">
        <button id="btnExit" class="menu-btn" data-tooltip="Exit">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-24 h-24">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
        </button>
    </div>
</div>


<script>
    let selectedAvatar = null;
    let selectedAvatarHouse = null;
    const tokenpool = document.getElementById('token_pool');
    tokenpool.addEventListener('keypress', function(event) {
        if (event.key === 'e' || event.key === '.' || event.key === '-' || isNaN(event.key)) {
            event.preventDefault();
        }
    });

    tokenpool.addEventListener('paste', function(event) {
        const pasteData = event.clipboardData.getData('text');

        if (isNaN(pasteData) || parseFloat(pasteData) <= 0) {
            event.preventDefault();
        }
    });
    const inputIntruderToken = document.getElementById('inputIntruderToken');
    inputIntruderToken.addEventListener('keypress', function(event) {
        if (event.key === 'e' || event.key === '.' || event.key === '-' || isNaN(event.key)) {
            event.preventDefault();
        }
    });

    inputIntruderToken.addEventListener('paste', function(event) {
        const pasteData = event.clipboardData.getData('text');

        if (isNaN(pasteData) || parseFloat(pasteData) <= 0) {
            event.preventDefault();
        }
    });

    // Dapatkan elemen checkbox
    const checkbox = document.getElementById('enableTokenLimit');

    // Atur status checked menjadi false
    checkbox.checked = false;
    document.addEventListener("DOMContentLoaded", async () => {

        // Submit Form
        document.getElementById("formOpenHouse").addEventListener("submit", async (e) => {
            e.preventDefault();
            valtokenslider();
            const formData = new FormData(e.target);
            const payload = Object.fromEntries(formData.entries());
            payload.house_avatar_id = selectedAvatarHouse;
            const responseDiv = document.getElementById("openhouseResponse");

            const hostname = document.getElementById("hostname").value.trim();
            const tokenPoolSlider = document.getElementById("tokenPoolSlider").value.trim();
            responseDiv.innerHTML = "";
            if (!hostname) {
                responseDiv.innerHTML = `<span class="text-danger">⚠️ Hostname wajib diisi!</span>`;
                return;
            }
            if (!tokenPoolSlider || isNaN(tokenPoolSlider) || tokenPoolSlider <= 0) {
                responseDiv.innerHTML = `<span class="text-danger">⚠️ Jumlah token harus valid!</span>`;
                return;
            }

            const btn = document.getElementById("btnOpenhouse");
            btn.disabled = true;
            btn.innerHTML = "⏳ Sedang memproses...";
            const res = await fetch("/api/ngepet/match/create", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${token}`,

                    },
                    body: JSON.stringify(payload),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.error?.avatar_id) {
                        responseDiv.innerHTML = `<span class="text-danger">❌ ${data.error.avatar_id}</span>`;
                        return;
                    }
                    if (data.status == "error") {
                        responseDiv.innerHTML = `<span class="text-danger">❌ ${data.message}</span>`;
                        setTimeout(() => {
                            responseDiv.innerHTML = "";
                        }, 3000);
                        return;
                    }

                    if (data.status == "success") {
                        const matchid = data.id
                        responseDiv.innerHTML = `<span class="text-success">✅ ${data.message || "Berhasil membuka rumah!"}</span>`;
                        setTimeout(() => {
                            const modalEl = document.getElementById('modalOpenHouse');
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            responseDiv.innerHTML = "";
                            modal.hide();
                        }, 2000);
                        setTimeout(() => {
                            window.location.href = `/games/ngepet/match/` + matchid;
                        }, 3000);
                        return;
                    } else {
                        responseDiv.innerHTML = `<span class="text-warning">⚠️ ${data.message || "Gagal masuk!"}</span>`;
                    }
                })
                .catch(err => {
                    console.error("Error:", err);
                    responseDiv.innerHTML = `<span class="text-danger">❌ Terjadi kesalahan saat masuk kerumah! ${err.message || "Coba lagi nanti"}</span>`;
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = "🚪 Buka Rumah";
                });
        });
    });


    document.addEventListener("DOMContentLoaded", function() {
        attachHoverSound();
        const btnRefresh = document.getElementById("btnRefresh");
        if (btnRefresh) {
            btnRefresh.addEventListener("click", function() {
                loadMatches();
            });
        }
    });

    function closeModal(id) {
        document.getElementById(id).style.display = "none";
    }

    async function loadAvatarHouse() {
        const avatarhouseList = document.getElementById("avatarhouseList");
        const confirmBtn = document.getElementById("confirmAvatar");
        avatarhouseList.innerHTML = "";
        const token = localStorage.getItem("token");

        try {

            const response = await fetch("/api/ngepet/avatar/own", {
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${token}`,
                }
            });
            const result = await response.json();

            if (result.status !== "success") {
                avatarhouseList.innerHTML = `<div class="text-danger text-center">Gagal memuat avatar.</div>`;
                return;
            }

            const avatarshouse = result.data.filter(item => item.avatar.type === "house");

            if (avatarshouse.length === 0) {
                avatarhouseList.innerHTML = `<div class="text-warning text-center">Kamu belum memiliki avatar.</div>`;
                return;
            }

            // === RENDER AVATAR HOUSE===
            avatarshouse.forEach(item => {
                const col = document.createElement("div");
                col.classList.add("col-6", "col-md-3", "text-center");
                if (item.is_equipped) {
                    selectedAvatarHouse = item.avatar.id;
                }
                col.innerHTML = `
                <div class="card avatar-card ${item.is_equipped ? 'selected' : ''}"
                   data-id="${item.avatar.id}"
                    data-tier="${item.avatar.tier}">
                    <img src="/${item.avatar.image_url}" class="card-img" alt="${item.avatar.name}">
                    <div class="card-body p-2">
                        <small style="color:white;">${item.avatar.name}</small>
                    </div>
                </div>
            `;
                avatarhouseList.appendChild(col);
            });
            // === HANDLE PILIH AVATAR HOUSE===
            document.querySelectorAll(".avatar-card").forEach(card => {
                card.addEventListener("click", () => {
                    document.querySelectorAll(".avatar-card").forEach(c => {
                        c.classList.remove("selected");
                    });

                    card.classList.add("selected");
                    selectedAvatarHouse = card.dataset.id;
                });
            });

        } catch (error) {
            console.error(error);
            avatarhouseList.innerHTML = `<div class="text-danger text-center">Terjadi kesalahan koneksi.</div>`;
        }
    }

    function valtokenslider() {

        const totaltoken = localStorage.getItem("total_token") || 100000;

        const enableTokenLimit = document.getElementById("enableTokenLimit");
        const tokenSliderWrapper = document.getElementById("tokenSliderWrapper");
        const minTokenSlider = document.getElementById("minTokenSlider");
        const maxTokenSlider = document.getElementById("maxTokenSlider");
        const minTokenValue = document.getElementById("minTokenValue");
        const maxTokenValue = document.getElementById("maxTokenValue");
        const TokenPoolSlider = document.getElementById("tokenPoolSlider");
        // Set max slider berdasarkan totalToken
        minTokenSlider.max = totaltoken;
        maxTokenSlider.max = totaltoken;
        TokenPoolSlider.max = totaltoken;
        // Aktifkan / Nonaktifkan Slider Token
        enableTokenLimit.addEventListener("change", () => {
            if (enableTokenLimit.checked) {
                tokenSliderWrapper.classList.remove("disabled");
                minTokenSlider.disabled = false;
                maxTokenSlider.disabled = false;
                minTokenSlider.value = 100;
                maxTokenSlider.value = totaltoken;
                minTokenValue.textContent = minTokenSlider.value;
                maxTokenValue.textContent = maxTokenSlider.value;
            } else {
                tokenSliderWrapper.classList.add("disabled");
                minTokenSlider.disabled = true;
                maxTokenSlider.disabled = true;
                minTokenSlider.value = 0;
                maxTokenSlider.value = 0;
                minTokenValue.textContent = 0;
                maxTokenValue.textContent = 0;
            }
        });

        minTokenSlider.addEventListener("input", () => {
            let val = Math.round(minTokenSlider.value / 100) * 100;
            // Jika nilai min lebih besar dari max, set sama dengan max
            if (val > parseInt(maxTokenSlider.value)) {
                val = parseInt(maxTokenSlider.value);
            }
            minTokenSlider.value = val;
            minTokenValue.textContent = val;
        });

        maxTokenSlider.addEventListener("input", () => {
            let val = Math.round(maxTokenSlider.value / 100) * 100;
            // Jika nilai max lebih kecil dari min, set sama dengan min
            if (val < parseInt(minTokenSlider.value)) {
                val = parseInt(minTokenSlider.value);
            }
            maxTokenSlider.value = val;
            maxTokenValue.textContent = val;
        });
        TokenPoolSlider.addEventListener("input", () => {
            let val = Math.round(TokenPoolSlider.value / 100) * 100;
            TokenPoolSlider.value = val;
            token_pool.value = val;
        });
        token_pool.addEventListener("change", () => {
            TokenPoolSlider.value = token_pool.value;
        });
    }

    function intrudervaltokenslider() {

        const totaltoken = localStorage.getItem("total_token") || 100000;

        const intruderTokenPoolSlider = document.getElementById("intruderTokenPoolSlider");

        intruderTokenPoolSlider.max = totaltoken;

        intruderTokenPoolSlider.addEventListener("input", () => {
            let val = Math.round(intruderTokenPoolSlider.value / 100) * 100;
            intruderTokenPoolSlider.value = val;
            inputIntruderToken.value = val;
        });
        inputIntruderToken.addEventListener("change", () => {
            intruderTokenPoolSlider.value = inputIntruderToken.value;
        });
    }

    function getTierClass(tier) {
        switch (tier) {
            case 'common':
                return 'tier-common';
            case 'uncommon':
                return 'tier-uncommon';
            case 'rare':
                return 'tier-rare';
            case 'mythical':
                return 'tier-mythical';
            case 'legendary':
                return 'tier-legendary';
            default:
                return 'tier-default';
        }
    }

    function loadMatches() {
        const token = localStorage.getItem("token");

        fetch('/api/ngepet/match', {
                method: 'GET',
                headers: {
                    "Authorization": `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                let html = '';
                const baseUrl = window.location.origin;
                data.matches.forEach(match => {
                    const tierClass = getTierClass(match.house_avatar.tier);
                    html += `
                <div class="match-item ${tierClass}">
                    <div class="match-left">
                <img src="${baseUrl}/${match.house_avatar.image_url}" alt="${match.house_avatar.name}">                        <div class="match-info">
                            <h3>${match.house_avatar.name}</h3>
                            Host: ${match.host_name}</br>
                            Tingkat Kesulitan: ${match.difficulty}</br>
                        </div>
                    </div>
                    <div class="token-count">
                        Token: ${match.token_pool ?? 0}/${match.total_token_pool ?? 0}</br>
                        Penyusup: ${match.intruders_count ?? 0}/${match.max_intruders ?? 0}</br>
                    </div>
                    <div class="btn-intruder">
                        <button
                            class="btn-intruder btn-detail"
                            data-id="${match.id}"
                            data-name="${match.house_avatar.name}"
                            data-host="${match.host_name}"
                            data-token="${match.token_pool ?? 0}"
                            data-totaltoken="${match.total_token_pool ?? 0}"
                            data-image="${baseUrl}/${match.house_avatar.image_url}"
                            data-tier="${match.house_avatar.tier}"
                            data-intruders="${match.intruders_count ?? 0}"
                            data-max="${match.max_intruders ?? 0}"
                            data-duration="${match.guess_duration_hours ?? 0}"
                            data-difficulty="${match.difficulty ?? 'medium'}"
                            data-mintoken="${match.min_intruder_token ?? ''}"
                            data-maxtoken="${match.max_intruder_token ?? ''}">
                            Lihat Detail
                        </button>
                    </div>
                </div>
            `;
                });

                document.getElementById("matchList").innerHTML = html;
                attachHoverSound();
            })
            .catch(err => console.error(err));
    }
    let currentMatchId = null;

    document.getElementById("matchList").addEventListener("click", function(e) {
        if (e.target.classList.contains("btn-detail")) {
            const btn = e.target;
            currentMatchId = btn.dataset.id;
            const tier = btn.dataset.tier;

            document.getElementById("modalHouseImage").src = btn.dataset.image;
            document.getElementById("modalHouseName").innerText = btn.dataset.name;
            document.getElementById("modalHostName").innerText = btn.dataset.host;
            document.getElementById("modalTokenPool").innerText = btn.dataset.token;
            document.getElementById("modalTotalTokenPool").innerText = btn.dataset.totaltoken;
            document.getElementById("modalIntrudersCount").innerText = btn.dataset.intruders;
            document.getElementById("modalMaxIntruders").innerText = btn.dataset.max;
            document.getElementById("modalDuration").innerText = btn.dataset.duration;
            document.getElementById("modalMinToken").innerText = btn.dataset.mintoken;
            document.getElementById("modalMaxToken").innerText = btn.dataset.maxtoken;
            document.getElementById("modalDifficulty").innerText = btn.dataset.difficulty;

            let modalContent = document.querySelector("#matchDetailModal .modal-content");
            modalContent.className = "modal-content";
            modalContent.classList.add(`tier-${tier}`);

            const modal = new bootstrap.Modal(document.getElementById("matchDetailModal"));
            modal.show();
        }
    });
    document.getElementById("btnJoinIntruderModal").addEventListener("click", async () => {
        const avatarList = document.getElementById("avatarList");
        const token = localStorage.getItem("token");
        intrudervaltokenslider()
        avatarList.innerHTML = "";
        try {

            const response = await fetch("/api/ngepet/avatar/own", {
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${token}`,
                }
            });
            const result = await response.json();

            if (result.status !== "success") {
                avatarList.innerHTML = `<div class="text-danger text-center">Gagal memuat avatar.</div>`;
                return;
            }

            const avatars = result.data.filter(item => item.avatar.type === "player");


            if (avatars.length === 0) {
                avatarList.innerHTML = `<div class="text-warning text-center">Kamu belum memiliki avatar.</div>`;
            }
            // === RENDER AVATAR ===
            avatars.forEach(item => {
                const col = document.createElement("div");
                col.classList.add("col-6", "col-md-3", "text-center");
                if (item.is_equipped) {
                    selectedAvatar = item.avatar.id;
                }
                col.innerHTML = `
                <div class="card avatar-card ${item.is_equipped ? 'selected' : ''}"
                   data-id="${item.avatar.id}"
                    data-tier="${item.avatar.tier}">
                    <img src="/${item.avatar.image_url}" class="card-img" alt="${item.avatar.name}">
                    <div class="card-body p-2">
                        <small style="color:white;">${item.avatar.name}</small>
                    </div>
                </div>
            `;
                avatarList.appendChild(col);
            });

            // === HANDLE PILIH AVATAR ===
            document.querySelectorAll(".avatar-card").forEach(card => {
                card.addEventListener("click", () => {
                    document.querySelectorAll(".avatar-card").forEach(c => {
                        c.classList.remove("selected");
                    });

                    card.classList.add("selected");
                    selectedAvatar = card.dataset.id;
                });
            });

        } catch (error) {
            console.error(error);
            avatarList.innerHTML = `<div class="text-danger text-center">Terjadi kesalahan koneksi.</div>`;
        }
        let modalContentSource = document.querySelector("#matchDetailModal .modal-content");
        let tierClass = Array.from(modalContentSource.classList).find(c => c.startsWith("tier-"));
        if (tierClass) {
            let modalContentTarget = document.querySelector("#matchJoinModal .modal-content");
            if (modalContentTarget) {
                let existingTierClass = Array.from(modalContentTarget.classList).find(c => c.startsWith("tier-"));
                if (existingTierClass) {
                    modalContentTarget.classList.remove(existingTierClass);
                }
                modalContentTarget.classList.add(tierClass);
            }
        }
        const modal = new bootstrap.Modal(document.getElementById("matchJoinModal"));
        modal.show();
    });
    document.getElementById("btnPreview").addEventListener("click", function() {
        window.location.href = `/games/ngepet/match/` + currentMatchId;
    });

    document.getElementById("btnJoinIntruder").addEventListener("click", function() {
        const intruderName = document.getElementById("inputIntruderName").value.trim();
        const intruderToken = document.getElementById("inputIntruderToken").value.trim();
        const avatarId = selectedAvatar;
        const responseDiv = document.getElementById("joinIntruderResponse");

        responseDiv.innerHTML = "";

        if (!intruderName) {
            responseDiv.innerHTML = `<span class="text-danger">⚠️ Nama intruder wajib diisi!</span>`;
            return;
        }
        if (!intruderToken || isNaN(intruderToken) || intruderToken <= 0) {
            responseDiv.innerHTML = `<span class="text-danger">⚠️ Jumlah token harus valid!</span>`;
            return;
        }
        if (intruderToken < 100) {
            responseDiv.innerHTML = `<span class="text-danger">⚠️ Jumlah token minimal 100</span>`;
            return;
        }

        if (!currentMatchId) {
            responseDiv.innerHTML = `<span class="text-danger">⚠️ Match tidak ditemukan!</span>`;
            return;
        }

        const payload = {
            name: intruderName,
            token_amount: parseInt(intruderToken, 10),
            avatar_id: avatarId ? parseInt(avatarId, 10) : null
        };

        const token = localStorage.getItem("token");

        const btn = document.getElementById("btnJoinIntruder");
        btn.disabled = true;
        btn.innerHTML = "⏳ Sedang memproses...";

        fetch(`/api/ngepet/match/${currentMatchId}/join`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": `Bearer ${token}`
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.error?.avatar_id) {
                    responseDiv.innerHTML = `<span class="text-danger">❌ ${data.error.avatar_id}</span>`;
                    setTimeout(() => {
                        responseDiv.innerHTML = "";
                    }, 3000);
                    return;
                }
                if (data.error) {
                    responseDiv.innerHTML = `<span class="text-danger">❌ ${data.error}</span>`;
                    setTimeout(() => {
                        responseDiv.innerHTML = "";
                    }, 3000);
                    btn.disabled = false;
                    return;
                }

                if (data.success) {
                    responseDiv.innerHTML = `<span class="text-success">✅ ${data.success || "Berhasil!"}</span>`;
                    setTimeout(() => {
                        const modalEl = document.getElementById('matchDetailModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        responseDiv.innerHTML = "";
                        modal.hide();
                    }, 2000);
                    setTimeout(() => {
                        window.location.href = `/games/ngepet/match/` + currentMatchId;
                    }, 3000);
                    return;
                } else {
                    responseDiv.innerHTML = `<span class="text-warning">⚠️ ${data.message || "Gagal masuk!"}</span>`;
                }
            })
            .catch(err => {
                console.error("Error:", err);
                btn.disabled = false;
                btn.innerHTML = "🐷 Curi";
                responseDiv.innerHTML = `<span class="text-danger">❌ Terjadi kesalahan saat masuk kerumah! ${err.message || "Coba lagi nanti"}</span>`;
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = "🐷 Curi";
            });
    });
</script>
@endsection