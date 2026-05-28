@extends('layouts.ngepet')

@section('content')
<style>
    body {
        padding: 0rem;
        padding-top: 1rem;
        align-items: center;
        justify-content: center;
        font-family: 'Calistoga';
        font-size: 0.75rem;

    }

    h1 {
        top: 10%;
        font-size: clamp(1.8rem, 2vw, 2.8rem);
        text-shadow: 0 0 8px #facc15;
        color: rgb(240, 205, 65);
        line-height: 1.2;
        text-align: center;
    }

    .main-room-container {
        color: white;
        position: relative;
        padding-top: 2rem;
        width: 100vw;
        height: 100vh;
        /* background-color: #000; */
        display: block;
        justify-content: center;
        align-items: center;
    }

    /* === Bagian Rumah === */
    .house-info {
        text-align: center;
        margin-bottom: 30px;
    }

    .house-image {
        width: 90%;
        max-width: 400px;
        height: auto;
        margin: 15px auto;
        display: block;
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
        border: 2px solid #ff6200ff;
        box-shadow: 0 0 30px #ff6200a7;
    }

    .token-info {
        margin: 0px auto;
        width: 80%;
        max-width: 800px;
    }

    .token-bar {
        background: #222;
        height: 18px;
        border-radius: 8px;
        margin-top: 5px;
        overflow: hidden;
    }

    .token-fill {
        background: linear-gradient(90deg, #ffcc00, #ff8800);
        height: 100%;
    }

    .game-details {
        display: flex;
        grid-template-columns: repeat(auto-fit, minmax(10px, 1fr));
        gap: 10px;
        margin: 0;
        align-items: center;
        justify-content: center;
    }

    .items-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(120px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }

    .item-card {
        background: #1a1a1a;
        border: 1px solid #e4a5ff;
        border-radius: 8px;
        text-align: center;
        padding: 5px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .item-card:hover {
        transform: scale(1.05);
        border-color: #ff9dff;
    }

    .item-name {
        font-size: 13px;
        margin-top: 4px;
    }

    .item-preview-container {
        text-align: center;
        margin-bottom: 10px;
        animation: fadeIn 0.3s ease-in-out;
    }

    .item-preview-container img {
        max-width: 180px;
        width: 80%;
        border: 2px solid #e4a5ff;
        border-radius: 10px;
        margin-bottom: 6px;
    }

    .item-preview-container div {
        font-weight: bold;
        font-size: 15px;
    }

    .items-action {
        margin: 20px 0;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
    }

    .item-card.greyed {
        opacity: 0.5;
        pointer-events: none;
        filter: grayscale(100%);
    }

    .item-card.selected {
        border-color: #2ecc71;
        box-shadow: 0 0 15px #2ecc71;
    }

    .hidden-items-grid {
        display: inline-grid;
        grid-template-columns: repeat(5, minmax(40px, 1fr));
        gap: 10px;
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .hidden-item-card {
        background: #1a1a1a;
        max-width: 60px;
        border: 1px solid #e4a5ff;
        border-radius: 8px;
        text-align: center;
        padding: 5px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .hidden-item-card:hover {
        transform: scale(1.05);
        border-color: #ff9dff;
    }

    .btn {
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-primary {
        background-color: #3498db;
        color: white;
    }

    .btn-warning {
        background-color: #f39c12;
        color: white;
    }

    .btn-success {
        background-color: #2ecc71;
        color: white;
    }

    .events-log {
        margin: 15px auto;
        padding-bottom: 30px;
        width: 90%;
        max-width: 800px;
    }

    .events-list {
        max-height: 180px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .event-card {
        background: #1c1c1c;
        padding: 8px 12px;
        margin-bottom: 8px;
        border-radius: 8px;
        font-size: 13px;
    }

    .event-card.role-host {
        border-left: 4px solid #00d1ff;
    }

    .event-card.role-intruder {
        border-left: 4px solid #ff4444;
    }

    .event-card.role-system {
        border-left: 4px solid #44ff44;
    }

    .intruders-section {
        margin-top: 1rem;
    }

    .intruders-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }

    .intruder-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: #1e1e2e;
        color: #fff;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
    }

    .intruder-avatar img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 0.5rem;
        border: 2px solid #444;
    }

    .intruder-info h4 {
        margin: 0.3rem 0;
        font-size: 1.1rem;
    }

    .intruder-info p {
        margin: 0.2rem 0;
        font-size: 0.9rem;
    }

    .intruder-actions button {
        margin-top: 0.5rem;
        padding: 0.4rem 0.8rem;
        background: #ff4757;
        border: none;
        border-radius: 6px;
        color: #fff;
        cursor: pointer;
        transition: background 0.2s;
    }

    .intruder-actions button:hover {
        background: #e84118;
    }

    /* Animasi Getar (Shaking Animation) */
    @keyframes shake {
        0% {
            transform: translateX(0);
        }

        10% {
            transform: translateX(-5px);
        }

        20% {
            transform: translateX(5px);
        }

        30% {
            transform: translateX(-5px);
        }

        40% {
            transform: translateX(5px);
        }

        50% {
            transform: translateX(-5px);
        }

        60% {
            transform: translateX(5px);
        }

        70% {
            transform: translateX(-5px);
        }

        80% {
            transform: translateX(5px);
        }

        90% {
            transform: translateX(-5px);
        }

        100% {
            transform: translateX(0);
        }
    }

    @media (max-width: 576px) {
        .items-grid {
            grid-template-columns: repeat(5, 1fr);
        }

        .item-card img {
            width: 100%;
            border-radius: 6px;
        }

        .item-name {
            display: none;
        }
    }

    .shake-effect {
        animation: shake 0.5s cubic-bezier(.36, .07, .19, .97) both;
        transform: translate3d(0, 0, 0);
        backface-visibility: hidden;
        perspective: 1000px;
    }

    .btn-mystic {
        color: #ff5050ff;
        font-weight: bold;
        font-size: 14px;
        text-shadow: 0 0 8px rgba(108, 59, 189, 0.6);
        transition: all 0.3s ease;
        display: none;
    }

    .btn-mystic:hover {
        transform: scale(1.2);
        text-shadow: 0 0 12px rgba(150, 80, 255, 0.9);
        color: #ff9090ff;
    }

    .intruder-waiting-modal {
        position: fixed;
        inset: 0;
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .intruder-waiting-modal .iwm-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.95);
        backdrop-filter: blur(4px);
    }

    .intruder-waiting-modal .iwm-content {
        position: relative;
        z-index: 2;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }

    .intruder-waiting-modal .iwm-center {
        pointer-events: auto;
        text-align: center;
        max-width: 900px;
        width: 100%;
        padding: 20px;
        box-sizing: border-box;
    }

    .candle-wrap {
        position: relative;
        width: clamp(180px, 32vw, 420px);
        height: clamp(180px, 32vw, 420px);
        margin: 0 auto 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        overflow: hidden;
    }

    .candle-wrap video,
    .candle-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 50%;
        transform: translateZ(0);
    }

    .candle-glow {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        box-shadow: 0 0 120px 30px rgba(255, 160, 64, 0.12) inset, 0 0 60px 12px rgba(255, 160, 64, 0.06);
        pointer-events: none;
    }

    .iwm-title {
        color: #fff;
        font-size: 1.6rem;
        margin: 8px 0 4px;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
    }

    .iwm-subtitle {
        color: rgba(255, 255, 255, 0.8);
        margin: 0 0 18px;
    }

    .iwm-timer-wrap {
        width: min(720px, 92%);
        margin: 0 auto 16px;
    }

    .iwm-time {
        color: #fff;
        font-weight: 700;
        margin-bottom: 8px;
        font-size: 1.15rem;
        text-shadow: 0 1px 6px rgba(0, 0, 0, 0.6);
    }

    .iwm-progress {
        width: 100%;
        height: 14px;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 999px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.03);
    }

    .iwm-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, rgba(255, 150, 50, 0.95), rgba(255, 100, 80, 0.95));
        width: 100%;
        transition: width 0.4s linear;
    }

    /* claim button */
    .iwm-actions {
        margin-top: 10px;
    }

    .iwm-claim-btn {
        background: linear-gradient(180deg, #ffcf7a, #ff9d3a);
        border: none;
        color: #171717;
        font-weight: 700;
        padding: 12px 22px;
        border-radius: 10px;
        cursor: pointer;
        box-shadow: 0 6px 18px rgba(255, 140, 50, 0.18);
        font-size: 1rem;
    }

    .iwm-claim-btn[disabled] {
        opacity: 0.5;
        cursor: not-allowed;
        filter: grayscale(.2);
    }

    /* message */
    .iwm-message {
        color: #ffd9b3;
        margin-top: 12px;
        min-height: 22px;
    }

    /* Responsive tweaks */
    @media (max-width:640px) {
        .candle-wrap {
            width: 58vw;
            height: 58vw;
        }

        .iwm-title {
            font-size: 1.2rem;
        }

        .iwm-time {
            font-size: 1rem;
        }

        .iwm-claim-btn {
            padding: 10px 16px;
            font-size: 0.95rem;
        }
    }
</style>

<div class="main-room-container" data-match-id="{{ $match['id'] ?? '' }}">
    <!-- Bagian Atas: Info Rumah -->
    <div class="house-info text-center">
        <h1 class="house-name">{{ $match['host_name'] ?? 'Unknown' }} ({{ $match['house_avatar']['name'] ?? 'Unknown' }})</h1>
        <!-- Token Pool -->
        <div class="token-info" data-token-pool="{{ $match['token_pool'] ?? 0 }}" data-total-token-pool="{{ $match['total_token_pool'] ?? 0 }}">
            <span class="token-label font-semibold text-gray-300">Token Pool:</span>
            <div class="token-bar">
                <div id="token-fill" class="token-fill"></div>
            </div>
            <span id="token-count" class="token-count block text-right text-sm mt-1">
            </span>
        </div>
        @if(isset($match['house_avatar']))
        <div id="avatarHouse" class="avatar-card selected" data-tier="{{ $match['house_avatar']['tier'] ?? '0' }}" data-match-id="{{ $match['id'] ?? '' }}">

            <img src="/{{ $match['house_avatar']['image_url'] ?? 'default.png' }}"
                alt="{{ $match['house_avatar']['name'] ?? 'House Avatar' }}"
                class="house-image">
            <button id="closeHouseBtn" class="btn btn-mystic">
                🔒 Tutup Rumah
            </button>
            <div id="closeHouseResponse" class="w-100 text-center pb-2"></div>
        </div>
        @else
        <p class="no-avatar">Tidak ada avatar rumah</p>
        @endif
        <div class="game-details">
            <p>🎯 Difficulty:
                <strong>{{ ucfirst($match['difficulty'] ?? 'normal') }}</strong>
            </p>
            <p>⏳ Waktu menebak:
                <strong>{{ $match['guess_duration_hours'] ?? 0 }} jam</strong>
            </p>
            <p>👥 Max Intruders:
                <strong>{{ $match['intruders_count'] ?? 0 }}/{{ $match['max_intruders'] ?? 0 }}</strong>
            </p>
            <p>📦 Token Tersembunyi:
                <strong>{{ $match['hidden_tokens_count'] ?? 0 }}</strong>
            </p>
            <p>status:
                <strong>{{ $match['status'] ?? null }}</strong>
            </p>
        </div>

        <!-- Bagian Bawah: Informasi Tambahan -->
        <div class="info-section">
            <div class="flex-1 overflow-y-auto p-1">
                <div class="intruders-section" id="matchIntruder">
                    <h3>🐷 Penyusup</h3>
                    <div class="intruders-grid">
                        @php
                        $activeIntruders = collect($match['intruders'] ?? [])->filter(function($i) {
                        return ($i['status'] ?? '') !== 'end';
                        });
                        @endphp
                        @if($activeIntruders->isNotEmpty())
                        @foreach($activeIntruders as $intruder)
                        <div class="intruder-card"
                            data-id="{{ $intruder['id'] }}"
                            data-name="{{ $intruder['intruder_name'] ?? 'Unknown' }}"
                            data-status="{{ $intruder['status'] }}"
                            data-token="{{ $intruder['token_pool'] }}"
                            data-pick="{{ $intruder['is_pick_choice'] ? 1 : 0 }}"
                            data-intruderat="{{ $intruder['intruders_at'] ?? '' }}"
                            data-deadline="{{ $intruder['guess_deadline'] ?? '' }}">

                            <div class="intruder-avatar">
                                <img src="/{{ $intruder['avatar']['image_url'] ?? '/images/default-item.png' }}"
                                    alt="{{ $intruder['avatar']['name'] ?? 'Unknown Avatar' }}">
                            </div>
                            <div class="intruder-info">
                                <h4>{{ $intruder['intruder_name'] ?? 'Unknown' }}</h4>
                                <p>Status: <strong>{{ $intruder['status'] ?? 'unknown' }}</strong></p>
                                <p>Token: <strong>{{ $intruder['token_pool'] ?? 0 }}</strong></p>
                                <p>Bersembunyi : <strong>{{ $intruder['is_pick_choice'] ? '✅ Sudah' : '❌ Belum' }}</strong></p>
                            </div>
                            <small>
                                {{ isset($intruder['created_at']) 
                  ? \Carbon\Carbon::parse($intruder['created_at'])->diffForHumans() 
                  : 'waktu tidak diketahui' }}
                            </small>
                            <div class="intruder-actions">
                                <button class="btnMatchIntruder"
                                    style="display:none;"
                                    data-id="{{ $intruder['id'] }}"
                                    data-ispick="{{ $intruder['is_pick_choice'] ? 1 : 0 }}">
                                    Bermain
                                </button>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <p class="no-intruders">Belum ada Penyusup</p>
                        @endif
                    </div>
                </div>

                <div class="events-log">
                    <h3>📜 Riwayat</h3>
                    <div class="events-list">
                        @if(!empty($match['events']))
                        @foreach(collect($match['events'])->sortByDesc('created_at') as $event)
                        <div class="event-card role-{{ $event['role'] ?? 'unknown' }}">
                            <span>{{ $event['details'] ?? 'Tidak ada detail' }}</span>
                            <small>
                                {{ isset($event['created_at']) 
                                    ? \Carbon\Carbon::parse($event['created_at'])->diffForHumans() 
                                    : 'waktu tidak diketahui' }}
                            </small>
                        </div>
                        @endforeach
                        @else
                        <p class="no-events">Belum ada riwayat</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Mystic Nusa -->
<div class="modal fade" id="mysticModalItemMatch" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:700px; margin:auto;">
        <div class="modal-content m-3" style="background:#111; color:#fff; border:2px solid #e4a5ffff; box-shadow: 0 0 25px #d448ffff;">
            <div class="modal-header">
                <h5 id="mysticModalTitle" class="modal-title">📜 Mystic Nusa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tab Navigation -->
                <div id="itemPreviewContainer" class="item-preview-container d-none">
                    <img id="itemPreviewImage" src="" alt="Preview Item">
                    <div id="itemPreviewName"></div>
                </div>
                @php
                $items = $match['items'] ?? [];
                shuffle($items);
                @endphp
                <div class="items-grid">
                    @if(!empty($items))
                    @foreach($items as $item)
                    <div class="item-card" data-item="{{ $item['name'] ?? '' }}">
                        <img src="{{ $item['image_url'] ?? '/images/items/default-item.jpg' }}"
                            alt="{{ $item['name'] ?? 'Unknown Item' }}" width="100%">
                        <div class="item-name">{{ $item['name'] ?? 'Unknown' }}</div>
                    </div>
                    @endforeach
                    @else
                    <p class="no-items">Belum ada item tersedia</p>
                    @endif
                </div>
                <div class="items-action">
                    <button id="submitChoiceBtn" class="btn btn-primary" style="display:none;width:300px">Pilih Tempat Sembunyi</button>
                    <button id="guessBtn" class="btn btn-warning" style="display:none;width:300px">Tebak Tempat Sembunyi</button>
                    <button id="hideBtn" class="btn btn-warning" style="display:none;width:300px">Sembunyikan Token</button>
                    <button id="GuesshideBtn" class="btn btn-warning" style="display:none;width:300px">Cari Token</button>
                </div>
                <div id="matchitemResponse" class="w-100 text-center pt-3"></div>
            </div>
        </div>
    </div>
</div>

<div id="intruderWaitingModal" aria-hidden="true" class="intruder-waiting-modal" style="display:none;">
    <div class="iwm-backdrop"></div>

    <div class="iwm-content" role="dialog" aria-modal="true" aria-labelledby="iwmTitle">
        <div class="iwm-center">
            <!-- Video lilin (ganti src sesuai file kamu) -->
            <div class="candle-wrap">
                <video id="iwmCandleVideo" autoplay muted loop playsinline>
                    <source src="/videos/candle.mp4" type="video/mp4">
                    <!-- fallback: gunakan animated gif -->
                    <img src="/images/candle.gif" alt="Candle">
                </video>
                <div class="candle-glow"></div>
            </div>

            <h2 id="iwmTitle" class="iwm-title">Menunggu tebakan host...</h2>
            <p id="iwmSubtitle" class="iwm-subtitle">Babi mu telah bersembunyi — tunggu host menebak.</p>
            @php
            $hiddenItems = $match['hidden_items'] ?? [];
            shuffle($hiddenItems);
            @endphp
            <div class="hidden-items-grid">
                @if(!empty($hiddenItems))
                @foreach($hiddenItems as $hitem)
                <div class="hidden-item-card" id="divHiddenItem" data-id="{{ $hitem['id'] ?? '' }}">
                    <img src="/images/asset/games/items/mystery_box.jpg" alt="mystery box" width="100%" class="mystery-box-image">
                </div>
                @endforeach
                @else
                @endif
            </div>
            <!-- Countdown bar -->
            <div class="iwm-timer-wrap">
                <div class="iwm-time" id="iwmTimeText">--:--:--</div>
                <div class="iwm-progress">
                    <div id="iwmProgressBar" class="iwm-progress-bar" style="width:100%"></div>
                </div>
            </div>

            <!-- Claim button -->
            <div class="iwm-actions">
                <button id="iwmClaimBtn" class="iwm-claim-btn" disabled>⏳ Tunggu...</button>
            </div>

            <!-- optional message area -->
            <div id="iwmMessage" class="iwm-message" aria-live="polite"></div>
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

<audio id="bgm-room" loop>
    <source id="bgmSource" src="/sound/bgm/default.mp3" type="audio/mpeg">
</audio>

<audio id="endSound">
    <source src="/sound/sfx/end.mp3" type="audio/mpeg">
</audio>
<audio id="intruderSound">
    <source src="/sound/sfx/Babi-Ngepet-game.mp3" type="audio/mpeg">
</audio>
<audio id="intruderwinSound">
    <source src="/sound/sfx/Babi-Terbang-win.mp3" type="audio/mpeg">
</audio>
<audio id="intruderloseSound">
    <source src="/sound/sfx/Babi-Ngepet-lose.mp3" type="audio/mpeg">
</audio>

@endsection
<script>
    let iwmInterval = null;
    let iwmCurrentIntruder = null;

    function startIwmCountdown(intruder) {
        if (!intruder.deadline || !intruder.intruderAt) return;

        if (iwmInterval) {
            clearInterval(iwmInterval);
        }
        const modal = document.getElementById('intruderWaitingModal');
        const timeTextEl = document.getElementById('iwmTimeText');
        const progressEl = document.getElementById('iwmProgressBar');
        const claimBtn = document.getElementById('iwmClaimBtn');
        iwmCurrentIntruder = intruder.id;
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';

        const startTime = new Date(intruder.intruderAt).getTime();
        const deadlineTime = new Date(intruder.deadline).getTime();

        const totalDurationMs = deadlineTime - startTime;

        if (totalDurationMs <= 0) {
            progressEl.style.width = '100%';
            timeTextEl.innerText = '00:00:00';
            claimBtn.disabled = false;
            claimBtn.innerText = '🏆 Klaim Kemenangan';
            if (typeof window.attemptClaimVictory === 'function') {
                claimBtn.onclick = window.attemptClaimVictory;
            }
            if (typeof window.iwmPreventKeys === 'function') {
                window.removeEventListener('keydown', window.iwmPreventKeys);
            }
            return;
        }

        claimBtn.disabled = true;
        claimBtn.innerText = '⏳ Menunggu...';

        const updateTimer = () => {
            const now = Date.now();
            const remainingMs = deadlineTime - now;
            const elapsedMs = now - startTime;
            if (remainingMs <= 0) {
                clearInterval(iwmInterval);
                iwmInterval = null;

                progressEl.style.width = '100%';
                timeTextEl.innerText = '00:00:00';
                claimBtn.disabled = false;
                claimBtn.innerText = '🏆 Klaim Kemenangan';
                if (typeof window.attemptClaimVictory === 'function') {
                    claimBtn.onclick = window.attemptClaimVictory;
                }
                if (typeof window.iwmPreventKeys === 'function') {
                    window.removeEventListener('keydown', window.iwmPreventKeys);
                }
                return;
            }

            const totalSeconds = Math.floor(remainingMs / 1000);
            const h = Math.floor(totalSeconds / 3600);
            const m = Math.floor((totalSeconds % 3600) / 60);
            const s = totalSeconds % 60;
            timeTextEl.innerText = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;

            const percentage = (elapsedMs / totalDurationMs) * 100;

            progressEl.style.width = `${Math.max(0, Math.min(100, percentage))}%`;
        };

        updateTimer();
        iwmInterval = setInterval(updateTimer, 1000);

        if (typeof window.iwmPreventKeys === 'function') {
            window.addEventListener('keydown', window.iwmPreventKeys);
        }
    }

    function attemptClaimVictory() {
        const claimBtn = document.getElementById('iwmClaimBtn');
        const messageEl = document.getElementById('iwmMessage');

        claimBtn.disabled = true;
        claimBtn.innerText = '⏳ Memproses klaim...';
        messageEl.innerText = '';

        const token = localStorage.getItem('token') || '';
        if (!iwmCurrentIntruder) {
            messageEl.innerText = 'Intruder tidak diketahui.';
            claimBtn.disabled = false;
            claimBtn.innerText = '🏆 Klaim Kemenangan';
            return;
        }
        if (!token) {
            messageEl.innerText = 'Token otentikasi tidak ditemukan.';
            claimBtn.disabled = false;
            claimBtn.innerText = '🏆 Klaim Kemenangan';
            return;
        }
        fetch(`/api/ngepet/match/claim-victory`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    match_intruder_id: iwmCurrentIntruder
                })
            })
            .then(r => r.json())
            .then(json => {
                if (json?.status === 'success') {
                    messageEl.innerText = '✅ ' + (json.message || '');
                    localStorage.removeItem("matchRole");
                    currentIntruderMatchId = null;
                    currentHiddenItemId = null;
                    intruderwinSound.currentTime = 0; // reset biar bisa main ulang cepat
                    intruderwinSound.play();
                    setTimeout(() => {
                        closeIntruderWaitingModal();
                        location.reload();
                    }, 5000);
                    return;
                }
                // show error
                const errMsg = json?.error ?? json?.message ?? 'Gagal klaim';
                messageEl.innerText = `❌ ${errMsg}`;
                claimBtn.disabled = false;
                claimBtn.innerText = '🏆 Klaim Kemenangan';
                intruderloseSound.currentTime = 0; // reset biar bisa main ulang cepat
                intruderloseSound.play();
                setTimeout(() => {
                    closeIntruderWaitingModal();
                    location.reload();
                }, 5000);
            })
            .catch(err => {
                console.error('claim error', err);
                messageEl.innerText = '❌ Terjadi kesalahan jaringan saat klaim.';
                claimBtn.disabled = false;
                claimBtn.innerText = '🏆 Klaim Kemenangan';
            });
    }

    // close modal (internal usage after successful claim)
    function closeIntruderWaitingModal() {
        const modal = document.getElementById('intruderWaitingModal');
        if (!modal) return;
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        // remove overflow lock
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
        // cleanup
        clearInterval(iwmInterval);
        iwmInterval = null;
        iwmCurrentIntruder = null;
        const container = document.getElementById('intruderWaitingModal');
        if (container && container.dataset) {
            delete container.dataset.iwmInitial;
        }
        window.removeEventListener('keydown', iwmPreventKeys);
    }

    // prevent ESC or Ctrl+W closing (best-effort)
    function iwmPreventKeys(e) {
        // prevent ESC (27) from closing if accidentally bound
        if (e.key === 'Escape' || e.key === 'Esc') {
            e.preventDefault();
            e.stopPropagation();
        }
    }

    // optional helper to manually force close if needed (use carefully)
    function forceCloseIntruderWaitingModal() {
        closeIntruderWaitingModal();
    }
</script>

<script>
    // Mapping tier -> file BGM
    const bgmMap = {
        common: "/sound/bgm/bgm-common.mp3",
        uncommon: "/sound/bgm/bgm-uncommon.mp3",
        rare: "/sound/bgm/bgm-rare.mp3",
        mythical: "/sound/bgm/bgm-mythical.mp3",
        legendary: "/sound/bgm/bgm-legendary.mp3",
    };

    // Fungsi untuk set BGM sesuai tier
    function setBgmByTier(tier) {
        const bgmroom = document.getElementById("bgm-room");
        const bgmSource = document.getElementById("bgmSource");

        const file = bgmMap[tier] || bgmMap["common"];
        if (bgmSource.src.endsWith(file)) return;

        bgmroom.pause();
        bgmSource.src = file;
        bgmroom.load();
        bgmroom.volume = 0.1;
        bgmroom.play().catch(() => {
            console.log("Autoplay dicegah browser, user harus berinteraksi dulu.");
        });
    }

    let currentintruderMatchId = null;
    let currentHiddenItemId = null;


    function waitForMatchData(callback) {
        let tries = 0;
        const interval = setInterval(() => {
            const matchId = localStorage.getItem("matchId");
            const matchRole = localStorage.getItem("matchRole");
            const avatarHouseTier = document.getElementById('avatarHouse');

            setBgmByTier(avatarHouseTier?.dataset?.tier || 'common');
            renderToken();
            if (matchId && matchRole) {
                clearInterval(interval);
                callback(matchId, matchRole);
            }

            // batas max percobaan biar gak infinite loop
            if (++tries > 10) { // misal max 20x (2 detik kalau interval 100ms)
                clearInterval(interval);
                console.warn("Match data tidak ditemukan di localStorage");
            }
        }, 200); // cek tiap 100ms
    }

    function renderToken() {
        // Dapatkan elemen-elemen HTML
        const tokenInfoEl = document.querySelector('.token-info');
        const tokenFillEl = document.getElementById('token-fill');
        const tokenCountEl = document.getElementById('token-count');

        // Ambil data dari atribut data HTML
        const tokenPool = parseFloat(tokenInfoEl.dataset.tokenPool);
        const totalTokenPool = parseFloat(tokenInfoEl.dataset.totalTokenPool);
        // Hitung persentase
        const tokenPercentage = (totalTokenPool > 0) ?
            (tokenPool / totalTokenPool) * 100 :
            0;

        // Atur lebar bar dan teks
        tokenFillEl.style.width = `${tokenPercentage}%`;
        tokenCountEl.textContent = `${tokenPool} / ${totalTokenPool}`;

    }

    function updateTokenPool(newPool, newTotal) {
        tokenInfoEl.dataset.tokenPool = newPool;
        tokenInfoEl.dataset.totalTokenPool = newTotal;
        const newPercentage = (newTotal > 0) ?
            (newPool / newTotal) * 100 :
            0;
        tokenFillEl.style.width = `${newPercentage}%`;
        tokenCountEl.textContent = `${newPool} / ${newTotal}`;
    }

    function isMobile() {
        return window.innerWidth <= 576;
    }
    document.addEventListener("DOMContentLoaded", () => {
        waitForMatchData((matchId, role) => {

            const container = document.querySelector(".main-room-container");
            const itemCards = document.querySelectorAll(".item-card");
            const submitChoiceBtn = document.getElementById("submitChoiceBtn");
            const guessBtn = document.getElementById("guessBtn");
            const hideBtn = document.getElementById("hideBtn");
            const GuesshideBtn = document.getElementById("GuesshideBtn");
            const closeHouseBtn = document.getElementById("closeHouseBtn");
            const modalmatchitem = new bootstrap.Modal(document.getElementById("mysticModalItemMatch"));
            const responseMIDiv = document.getElementById("matchitemResponse");
            const endSound = document.getElementById("endSound");
            const intruderSound = document.getElementById("intruderSound");
            const intruderwinSound = document.getElementById("intruderwinSound");
            const intruderloseSound = document.getElementById("intruderloseSound");
            const token = localStorage.getItem("token");
            const intrudermatchId = localStorage.getItem("intrudermatchId");
            const pageMatchId = container.dataset.matchId; // match id dari halaman
            const previewContainer = document.getElementById("itemPreviewContainer");
            const previewImage = document.getElementById("itemPreviewImage");
            const previewName = document.getElementById("itemPreviewName");
            document.getElementById("avatarHouse").addEventListener("click", function(e) {
                if (e.target.classList.contains("house-image")) {
                    previewContainer.classList.add("d-none");
                    responseMIDiv.innerHTML = "";
                    if (role === "intruder") {
                        guessBtn.style.display = "none";
                        hideBtn.style.display = "none";
                        GuesshideBtn.style.display = "none";
                    }
                    if (role === "host") {
                        submitChoiceBtn.style.display = "none";
                        hideBtn.style.display = "inline-block";
                        guessBtn.style.display = "none";
                        GuesshideBtn.style.display = "none";
                        if (matchId != pageMatchId) {
                            hideBtn.style.display = "none";
                        }
                    }
                    modalmatchitem.show()
                }
            });

            document.querySelectorAll(".btnMatchIntruder").forEach(btn => {
                // 🔥 ambil intruder info langsung dari parent card
                const card = btn.closest(".intruder-card");
                const intruder = {
                    id: card.dataset.id,
                    name: card.dataset.name,
                    status: card.dataset.status,
                    token: card.dataset.token,
                    intruderAt: card.dataset.intruderat,
                    deadline: card.dataset.deadline,
                    ispick: card.dataset.pick
                };

                if (intruder.id == intrudermatchId && role === "intruder" && intruder.ispick === "0" && intruder.status == "join") {
                    btn.style.display = "inline-block";
                }
                if (intruder.id == intrudermatchId && role === "intruder" && intruder.ispick === "1" && intruder.status == "wait") {
                    startIwmCountdown(intruder);
                }

                if (matchId == pageMatchId && role === "host" && intruder.ispick === "1" && intruder.status == "wait") {
                    btn.style.display = "inline-block";
                }
            });
            document.getElementById("matchIntruder").addEventListener("click", function(e) {
                if (e.target.classList.contains("btnMatchIntruder")) {
                    const btn = e.target;
                    currentintruderMatchId = btn.dataset.id;
                    if (role === "intruder") {
                        guessBtn.style.display = "none";
                        hideBtn.style.display = "none";
                    }
                    if (role === "host") {
                        submitChoiceBtn.style.display = "none";
                        hideBtn.style.display = "none";
                        guessBtn.style.display = "inline-block";
                    }
                    responseMIDiv.innerHTML = "";
                    modalmatchitem.show()
                }
            });

            document.getElementById("closeHouseBtn").addEventListener("click", async () => {
                const responseCHDiv = document.getElementById("closeHouseResponse");
                const apiUrl = `/api/ngepet/match/${matchId}/close`;
                try {
                    const res = await fetch(apiUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Authorization": `Bearer ${token}`

                        },
                    });
                    const data = await res.json();

                    if (data.status === "match_closed") {
                        responseCHDiv.innerHTML = `<span class="text-success">✅ ${data.message || "Rumah berhasil ditutup. Aura mistis kembali tenang."}</span>`;
                        setTimeout(() => {
                            responseCHDiv.innerHTML = "";
                        }, 3000);
                        setTimeout(() => {
                            localStorage.removeItem("matchRole");
                            location.reload();
                        }, 3000);
                    } else {
                        responseCHDiv.innerHTML = `<span class="text-warning">❌ ${data.error || "Gagal menutup rumah, energi gaib menolak."}</span>`;
                        setTimeout(() => {
                            closeHouseBtn.style.display = "none";
                            responseCHDiv.innerHTML = "";
                        }, 3000);
                    }
                } catch (err) {
                    responseCHDiv.innerHTML = `<span class="text-danger">❌ ${data.error || "Terjadi kesalahan, koneksi ke dunia lain terputus."}</span>`;
                    setTimeout(() => {
                        responseCHDiv.innerHTML = "";
                    }, 3000);
                }
            });
            // === NEW CODE: klik setiap hidden item ===
            const hiddenItems = document.querySelectorAll(".hidden-item-card");

            hiddenItems.forEach(item => {
                item.addEventListener("click", function() {
                    const itemId = this.dataset.id;
                    currentHiddenItemId = itemId;

                    // Tampilkan tombol sesuai role
                    if (role === "intruder") {
                        submitChoiceBtn.style.display = "none";
                        guessBtn.style.display = "none";
                        hideBtn.style.display = "none";
                        GuesshideBtn.style.display = "inline-block";
                    }

                    if (role === "host") {
                        submitChoiceBtn.style.display = "none";
                        hideBtn.style.display = "none";
                        GuesshideBtn.style.display = "none";
                    }

                    // Kosongkan respon sebelumnya
                    responseMIDiv.innerHTML = "";

                    // Tampilkan modal item match
                    modalmatchitem.show();
                });
            });
            //end test fungsi
            let selectedItem = null;

            // Role Intruder → Pilih tempat sembunyi
            if (role === "intruder") {
                submitChoiceBtn.style.display = "inline-block";
                itemCards.forEach(card => {
                    card.addEventListener("click", () => {
                        itemCards.forEach(c => c.classList.remove("selected"));
                        card.classList.add("selected");
                        selectedItem = card.dataset.item;
                        if (isMobile()) {
                            const img = card.querySelector("img").src;
                            const name = card.getAttribute("data-item") || "Unknown";

                            previewContainer.classList.remove("d-none");
                            previewImage.src = img;
                            previewName.textContent = name;
                        }
                    });
                });

                submitChoiceBtn.addEventListener("click", async () => {
                    if (!selectedItem) return responseMIDiv.innerHTML = `<span class="text-warning">⚠️ Pilih salah barang untuk bersembunyi!</span>`;
                    submitChoiceBtn.disabled = true;
                    submitChoiceBtn.innerHTML = "⏳ memproses...";
                    const res = await fetch(`/api/ngepet/match/${matchId}/submit-choice`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Authorization": `Bearer ${token}`

                        },
                        body: JSON.stringify({
                            item_name: selectedItem
                        })
                    });
                    const data = await res.json();

                    if (data) {
                        if (data.error) {
                            responseMIDiv.innerHTML = `<span class="text-danger">❌ ${data.error}</span>`;
                            setTimeout(() => {
                                responseMIDiv.innerHTML = "";
                            }, 3000);
                            if (data.error == "Kamu bukan bagian dari match ini") {
                                responseMIDiv.innerHTML = `<span class="text-danger">❌ ${data.error}</span>`;
                                setTimeout(() => {
                                    location.reload();
                                }, 5000);
                            }
                            submitChoiceBtn.innerHTML = "Plih Tempat Sembunyi";
                            return;
                        }
                        if (data.success) {
                            responseMIDiv.innerHTML = `<span class="text-success">✅ ${data.success || "Berhasil bersembunyi!"}</span>`;
                            endSound.currentTime = 0; // reset biar bisa main ulang cepat
                            endSound.play();
                            setTimeout(() => {
                                responseMIDiv.innerHTML = "";
                                modalmatchitem.hide();
                                location.reload();
                            }, 3000);
                            submitChoiceBtn.innerHTML = "Pilih Tempat Sembunyi";
                            return;
                        } else {
                            responseMIDiv.innerHTML = `<span class="text-warning">⚠️ ${data || "Gagal bersembunyi!"}</span>`;
                            submitChoiceBtn.disabled = false;
                            submitChoiceBtn.innerHTML = "Pilih Tempat Sembunyi";
                        }
                    };
                });
                GuesshideBtn.addEventListener("click", async () => {
                    if (!currentHiddenItemId) return responseMIDiv.innerHTML = `<span class="text-warning">⚠️ Pilih salah token dahulu!</span>`;
                    GuesshideBtn.disabled = true;
                    GuesshideBtn.innerHTML = "⏳ memproses...";
                    currentintruderMatchId = localStorage.getItem("intrudermatchId");
                    const res = await fetch(`/api/ngepet/match/${matchId}/hidden-guess`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Authorization": `Bearer ${token}`
                        },
                        body: JSON.stringify({
                            hidden_item_id: currentHiddenItemId,
                            match_intruder_id: currentintruderMatchId,
                            item_name: selectedItem
                        })
                    });
                    const data = await res.json();

                    if (data) {
                        if (data.error) {
                            responseMIDiv.innerHTML = `<span class="text-danger">❌ ${data.error}</span>`;
                            setTimeout(() => {
                                responseMIDiv.innerHTML = "";
                            }, 3000);
                            if (data.error === "Sudah pernah menebak barang ini") {
                                // mark item yg dipilih jadi greyed
                                const selectedCard = document.querySelector(`.item-card[data-item="${selectedItem}"]`);
                                if (selectedCard) {
                                    selectedCard.classList.add("greyed");
                                    selectedCard.classList.remove("selected");
                                    selectedItem = null;
                                }
                                GuesshideBtn.disabled = false;
                                GuesshideBtn.innerHTML = "Cari Token";
                            }
                            if (data.error === "Waktu menebak sudah habis") {
                                responseMIDiv.innerHTML = `<span class="text-danger">❌ ${data.error}</span>`;
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                                GuesshideBtn.disabled = true;
                                GuesshideBtn.innerHTML = "Cari Token";
                            }
                            GuesshideBtn.disabled = false;
                            GuesshideBtn.innerHTML = "Cari Token";
                            return;
                        }
                        if (data.status === "guess_submitted") {
                            // mark item yg dipilih jadi greyed
                            const selectedCard = document.querySelector(`.item-card[data-item="${selectedItem}"]`);
                            if (selectedCard) {
                                selectedCard.classList.add("greyed");
                                selectedCard.classList.remove("selected");
                            }
                            if (data.is_correct === false) {
                                shakeModal();
                                GuesshideBtn.disabled = false;
                                GuesshideBtn.innerHTML = "Cari Token";
                                intruderSound.currentTime = 0; // reset biar bisa main ulang cepat
                                intruderSound.play();
                            }
                            if (data.is_correct === true) {
                                GuesshideBtn.innerHTML = "Berhasil menemukan Token!";
                                selectedCard.classList.remove("greyed");
                                selectedCard.classList.add("selected");
                                intruderwinSound.currentTime = 0; // reset biar bisa main ulang cepat
                                intruderwinSound.play();
                                setTimeout(() => {
                                    modalmatchitem.hide();
                                    location.reload();
                                }, 5000);
                            }
                            responseMIDiv.innerHTML = `<span class="text-warning">🤔 ${data.is_correct ? "Ketemu!" : "Tidak ada."}</span>`;
                            setTimeout(() => {
                                responseMIDiv.innerHTML = "";
                            }, 3000);

                            // reset pilihan
                            selectedItem = null;

                            // kalau game belum end, jangan reload
                            if (data.is_end && data.is_correct === false) {
                                const answerCard = document.querySelector(`.item-card[data-item="${data.answer_item}"]`);
                                answerCard.classList.add("selected");
                                GuesshideBtn.disabled = true;
                                GuesshideBtn.innerHTML = "Anda ketahuan penjaga!";
                                intruderloseSound.currentTime = 0; // reset biar bisa main ulang cepat
                                intruderloseSound.play();
                                setTimeout(() => {
                                    modalmatchitem.hide();
                                    location.reload();
                                }, 5000);
                            }
                            return;
                        } else {
                            responseMIDiv.innerHTML = `<span class="text-warning">⚠️ ${data || "Gagal menebak!"}</span>`;
                            GuesshideBtn.disabled = false;
                            GuesshideBtn.innerHTML = "Cari Token";
                        }
                    };
                });
            };

            // Role Host → Tebak tempat sembunyi
            if (role === "host") {
                if (container && closeHouseBtn && matchId) {
                    if (matchId == pageMatchId) {
                        closeHouseBtn.style.display = "inline-block";
                    }
                }
                guessBtn.style.display = "inline-block";
                itemCards.forEach(card => {
                    card.addEventListener("click", () => {
                        // abaikan klik kalau item sudah greyed
                        if (card.classList.contains("greyed")) return;

                        itemCards.forEach(c => c.classList.remove("selected"));
                        card.classList.add("selected");
                        selectedItem = card.dataset.item;
                        if (isMobile()) {
                            const img = card.querySelector("img").src;
                            const name = card.getAttribute("data-item") || "Unknown";

                            previewContainer.classList.remove("d-none");
                            previewImage.src = img;
                            previewName.textContent = name;
                        }
                    });
                });
                hideBtn.addEventListener("click", async () => {
                    if (!selectedItem) return responseMIDiv.innerHTML = `<span class="text-warning">⚠️ Pilih salah barang dahulu!</span>`;
                    hideBtn.disabled = true;
                    hideBtn.innerHTML = "⏳ memproses...";
                    const res = await fetch(`/api/ngepet/match/${matchId}/hidden-item`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Authorization": `Bearer ${token}`
                        },
                        body: JSON.stringify({
                            item_name: selectedItem
                        })
                    });
                    const data = await res.json();

                    if (data) {
                        if (data.status === "error") {
                            responseMIDiv.innerHTML = `<span class="text-danger">❌ ${data.message}</span>`;
                            setTimeout(() => {
                                responseMIDiv.innerHTML = "";
                            }, 3000);
                            hideBtn.disabled = false;
                            hideBtn.innerHTML = "Sembunyikan Token";
                            selectedItem = null;
                            if (data.message === "Kamu sudah banyak menyembunyikan token ke sebuah barang.") {
                                responseMIDiv.innerHTML = `<span class="text-danger">❌ ${data.message}</span>`;
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            }
                            return;
                        }
                        if (data.status === "success") {
                            responseMIDiv.innerHTML = `<span class="text-success">✅ ${data.message || "Berhasil menyembunyikan token!"}</span>`;
                            setTimeout(() => {
                                hideBtn.disabled = false;
                                responseMIDiv.innerHTML = "";
                            }, 3000);
                            hideBtn.innerHTML = "Sembunyikan Token";
                            selectedItem = null;
                            return;
                        } else {
                            responseMIDiv.innerHTML = `<span class="text-warning">⚠️ ${data.message || "Gagal menyembunyikan token!"}</span>`;
                            hideBtn.disabled = false;
                            hideBtn.innerHTML = "Sembunyikan Token";
                            selectedItem = null;
                        }
                    };
                });

                guessBtn.addEventListener("click", async () => {
                    if (!selectedItem) return responseMIDiv.innerHTML = `<span class="text-warning">⚠️ Pilih salah barang dahulu!</span>`;
                    guessBtn.disabled = true;
                    guessBtn.innerHTML = "⏳ memproses...";
                    const res = await fetch(`/api/ngepet/match/${matchId}/guess`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Authorization": `Bearer ${token}`
                        },
                        body: JSON.stringify({
                            match_intruder_id: currentintruderMatchId,
                            item_name: selectedItem
                        })
                    });
                    const data = await res.json();

                    if (data) {
                        if (data.error) {
                            responseMIDiv.innerHTML = `<span class="text-danger">❌ ${data.error}</span>`;
                            setTimeout(() => {
                                responseMIDiv.innerHTML = "";
                            }, 3000);
                            if (data.error === "Sudah pernah menebak barang ini") {
                                // mark item yg dipilih jadi greyed
                                const selectedCard = document.querySelector(`.item-card[data-item="${selectedItem}"]`);
                                if (selectedCard) {
                                    selectedCard.classList.add("greyed");
                                    selectedCard.classList.remove("selected");
                                    selectedItem = null;
                                }
                                guessBtn.disabled = false;
                                guessBtn.innerHTML = "Tebak Tempat Sembunyi";
                            }
                            if (data.error === "Waktu menebak sudah habis. Babi berhasil mencuri token.") {
                                responseMIDiv.innerHTML = `<span class="text-danger">❌ ${data.error}</span>`;
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            }
                            guessBtn.disabled = true;
                            guessBtn.innerHTML = "Tebak Tempat Sembunyi";
                            return;
                        }
                        if (data.status === "guess_submitted") {
                            // mark item yg dipilih jadi greyed
                            const selectedCard = document.querySelector(`.item-card[data-item="${selectedItem}"]`);
                            if (selectedCard) {
                                selectedCard.classList.add("greyed");
                                selectedCard.classList.remove("selected");
                            }
                            if (data.is_correct === false) {
                                shakeModal();
                                guessBtn.disabled = false;
                                guessBtn.innerHTML = "Tebak Tempat Sembunyi";
                                intruderSound.currentTime = 0; // reset biar bisa main ulang cepat
                                intruderSound.play();
                            }
                            if (data.is_correct === true) {
                                guessBtn.innerHTML = "Babi Berhasil Ditangkap!";
                                selectedCard.classList.remove("greyed");
                                selectedCard.classList.add("selected");
                                intruderloseSound.currentTime = 0; // reset biar bisa main ulang cepat
                                intruderloseSound.play();
                                setTimeout(() => {
                                    modalmatchitem.hide();
                                    location.reload();
                                }, 5000);
                            }
                            responseMIDiv.innerHTML = `<span class="text-warning">🤔 Tebakan ${data.is_correct ? "Benar!" : "Salah."}</span>`;
                            setTimeout(() => {
                                responseMIDiv.innerHTML = "";
                            }, 3000);

                            // reset pilihan
                            selectedItem = null;

                            // kalau game belum end, jangan reload
                            if (data.is_end && data.is_correct === false) {
                                const answerCard = document.querySelector(`.item-card[data-item="${data.answer_item}"]`);
                                answerCard.classList.add("selected");
                                guessBtn.disabled = true;
                                guessBtn.innerHTML = "Babi berhasil kabur!";
                                intruderwinSound.currentTime = 0; // reset biar bisa main ulang cepat
                                intruderwinSound.play();
                                setTimeout(() => {
                                    modalmatchitem.hide();
                                    location.reload();
                                }, 5000);
                            }
                            return;
                        } else {
                            responseMIDiv.innerHTML = `<span class="text-warning">⚠️ ${data || "Gagal menebak!"}</span>`;
                            guessBtn.disabled = false;
                            guessBtn.innerHTML = "Tebak Tempat Sembunyi";
                        }
                    };
                });
            }
        });

    });

    // Fungsi yang Anda minta untuk mengaktifkan efek getar
    function shakeModal() {
        const modalContent = document.getElementById('mysticModalItemMatch').querySelector('.modal-content');

        // Tambahkan kelas CSS yang memicu animasi getar
        modalContent.classList.add('shake-effect');

        // Hapus kelas setelah animasi selesai, agar bisa dipanggil lagi
        modalContent.addEventListener('animationend', () => {
            modalContent.classList.remove('shake-effect');
        }, {
            once: true
        }); // { once: true } memastikan event listener hanya berjalan sekali
    }
</script>