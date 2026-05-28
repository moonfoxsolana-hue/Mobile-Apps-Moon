@extends('layouts.app')

@section('content')
<style>
    body {
        padding: 2rem;
        padding-top: 5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mystic-container {
        position: relative;
        box-sizing: border-box;
        padding: 5vh 2vw;
        display: flex;
        flex-direction: column;
        align-items: center;
        border-radius: 2rem;
        justify-content: center;
        width: 800px;
        min-width: 300px;
        max-width: 900px;
        top: 10%;
        min-height: 50vh;
        background-color: rgba(30, 30, 48, 0.75);
        background-size: cover;
        box-shadow: 0 0 25px rgba(255, 255, 255, 0.84);
    }

    /* Gambar Banner */
    .airdrop-banner {
        width: 90%;
        max-width: 500px;
        height: auto;
        border-radius: 1rem;
        transition: all 0.3s ease-in-out;
        opacity: 0.7;
    }

    /* Responsif: jadi portrait di mobile */
    @media (max-width: 576px) {
        .airdrop-banner {
            width: 90%;
            object-fit: cover;
        }
    }

    .tab-container {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .tab-button {
        padding: 10px 20px;
        background-color: rgb(183, 155, 12);
        border: 1px solid #444;
        color: #000;
        cursor: pointer;
        border-radius: 5px;
    }

    .tab-button:hover {
        background: rgb(255, 226, 61);
    }

    .tab-button.active {
        background-color: rgb(255, 201, 4);
        /* Mistis Ungu */
        font-weight: bold;
    }


    .claim-container {
        max-width: 800px;
        margin: 0 auto;
        border-radius: 1rem;
        padding: 2rem;
    }

    .claim-container h2 {
        text-align: center;
        color: #d4af37;
        margin-bottom: 1rem;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: .5rem;
        color: rgb(222, 211, 255);
    }

    input[type="text"] {
        width: 100%;
        padding: .75rem;
        margin-bottom: 1rem;
        border-radius: 8px;
        border: 1px solid #5b3d9d;
        background-color: #1e1036;
        color: #fff;
        box-sizing: border-box;
    }

    button {
        width: 100%;
        background: #8f6eff;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    button:disabled {
        opacity: 0.9;
        cursor: not-allowed;
        background-color: #bfa133;
    }

    #claimBtn:hover,
    #claimCodeBtn:hover {
        background-color: rgb(255, 217, 80);
    }

    #claimStatus {
        margin-top: 1rem;
        font-size: 0.95rem;
        text-align: center;
    }

    #claimCodeStatus {
        margin-top: 1rem;
        font-size: 0.95rem;
        text-align: center;
    }


    @media (max-width: 576px) {
        .claim-container {
            padding: 1.25rem;
        }
    }

    .spinner-claim {
        border: 3px solid rgba(255, 255, 255, 0.2);
        border-top: 3px solid #fff;
        border-radius: 50%;
        width: 0.95rem;
        height: 0.95rem;
        animation: spin 1s linear infinite;
        margin-right: 10px;
        display: none;
    }

    .koin-mynu1 {
        position: absolute;
        top: 30%;
        left: 40%;
        transform: translateX(-50%);
        width: 50px;
        animation: floaty 3s ease-in-out infinite;
        z-index: -0.5;
    }

    .koin-mynu2 {
        position: absolute;
        top: 20%;
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        animation: floaty 3s ease-in-out infinite;
        z-index: -0.5;
    }

    .koin-mynu3 {
        position: absolute;
        top: 20%;
        left: 60%;
        transform: translateX(-250%);
        width: 50px;
        animation: floaty 3s ease-in-out infinite;
        z-index: -0.5;
    }

    @keyframes floaty {

        0%,
        100% {
            transform: translate(-50%, 0);
        }

        50% {
            transform: translate(-50%, -20px);
        }
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .airdrop-success {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 200px;
        height: 200px;
        margin: auto;
        display: none;
    }

    .fade-image {
        position: absolute;
        width: 100%;
        height: auto;
        top: 0;
        left: 0;
        opacity: 0;
    }

    #img1 {
        animation: fadeInOnly 1s ease forwards;
    }

    #img2 {
        animation: fadeOnce 2s ease 1s forwards;
    }

    @keyframes fadeInOnly {
        0% {
            opacity: 0;
            transform: scale(0.5);
        }

        50% {
            opacity: 1;
            transform: scale(1);
        }

        75% {
            opacity: 0.5;
            transform: scale(1);
        }

        100% {
            opacity: 0;
            transform: scale(1);
        }
    }

    @keyframes fadeOnce {
        0% {
            opacity: 0;
            transform: scale(1);
        }

        50% {
            opacity: 1;
            transform: scale(1);
        }

        100% {
            opacity: 0;
            transform: scale(1.35);
        }
    }

    .airdrop-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 1rem;
    }

    .airdrop-card {
        background-color: rgba(35, 35, 55, 0.95);
        border-radius: 16px;
        padding: 2rem;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 0 20px rgba(212, 175, 55, 0.4);
        text-align: center;
    }

    .airdrop-card h2 {
        color: #d4af37;
        margin-bottom: 1rem;
    }

    .airdrop-card p {
        color: #ccc;
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }

    .airdrop-btn {
        background-color: #d4af37;
        color: #1a1a1a;
        padding: 0.75rem 1.5rem;
        font-size: 1.1rem;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .airdrop-btn:hover {
        background-color: #bfa133;
    }

    @media (max-width: 480px) {
        .airdrop-card {
            padding: 1.5rem;
        }

        .airdrop-btn {
            width: 100%;
        }
    }

    .banner-image {
        width: 100%;
        height: auto;
        animation: glow 2s ease-in-out infinite;
        border-radius: 16px;
        transition: filter 0.3s ease, transform 0.3s ease;

    }
</style>

<div class="mystic-container">

    <img src="images/banner-airdrop.jpg" class="airdrop-banner" />

    <div class="claim-container">
        <div class="tab-container">
            <button class="tab-button active" onclick="showTab('first')">Klaim Pertama</button>
            <button class="tab-button" onclick="showTab('code')">Klaim dengan Kode</button>
        </div>
        <div id="tab-first" class="tab-content active">
            <h2>🎁 Klaim Airdrop Pertama</h2>
            <form id="claimForm">
                <label for="wallet_address">Wallet Address (Solana)</label>
                <input type="text" id="wallet_address" name="wallet_address" placeholder="Contoh: 9xy...Abc" required>

                <button type="submit" id="claimBtn" class="airdrop-btn">
                    <span class="spinner-claim"></span>
                    <span class="btn-claim">Klaim Sekarang</span>
                </button>

                <div id="claimStatus"></div>
            </form>
        </div>
        <div id="tab-code" class="tab-content">
            <h2>✨ Klaim Harian via Kode</h2>
            <form id="claimCodeForm">
                <label for="claim_code">Kode Klaim</label>
                <input type="text" id="claim_code" name="claim_code" placeholder="Contoh: PARTNER2025" required>

                <button type="submit" id="claimCodeBtn" class="airdrop-btn">
                    <span class="spinner-claim"></span>
                    <span class="btn-claim">Gunakan Kode</span>
                </button>

                <div id="claimCodeStatus"></div>
            </form>
        </div>
    </div>
    <div class="airdrop-success" id="airdropSuccess">
        <img src="images/peti.png" class="fade-image" id="img1" />
        <img src="images/peti-2.png" class="fade-image" id="img2" />
        <audio id="claimSound" src="sound/coin.mp3" preload="auto"></audio>
    </div>
</div>


<script>
    function showTab(tab) {
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));

        document.querySelector(`[onclick="showTab('${tab}')"]`).classList.add('active');
        document.getElementById(`tab-${tab}`).classList.add('active');
    }
    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('token');
        if (!token) {
            // 🔐 Belum login, panggil modal login
            openLoginModal();
        }
    });

    document.getElementById('claimCodeForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const code = document.getElementById('claim_code').value;
        const btn = document.getElementById('claimCodeBtn');
        const status = document.getElementById('claimCodeStatus');
        const token = localStorage.getItem('token');
        if (!token) {
            alert('⚠️ Kamu harus login terlebih dahulu sebelum klaim.');
            openLoginModal();
            return;
        }

        btn.disabled = true;
        btn.querySelector('.spinner-claim').style.display = 'inline-block';
        btn.querySelector('.btn-claim').textContent = 'Sedang Memproses...';

        try {
            const res = await fetch('/api/airdrop/claim-with-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({
                    code
                })
            });

            const result = await res.json();
            console.log('Success:', result);
            if (res.ok) {
                status.innerHTML = `✅ ${result.message} <br> (+${result.amount} MYNU)`;
                playAirdropAnimation();
            } else {
                status.innerHTML = `❌ ${result.message}`;
            }
        } catch (err) {
            status.innerHTML = `⚠️ Gagal klaim: ${err.message}`;
        }

        btn.disabled = false;
        btn.querySelector('.spinner-claim').style.display = 'none';
        btn.querySelector('.btn-claim').textContent = 'Gunakan Kode';
    });

    document.getElementById('claimForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const wallet = document.getElementById('wallet_address').value;
        const token = localStorage.getItem('token');
        if (!token) {
            alert('⚠️ Kamu harus login terlebih dahulu sebelum klaim.');
            openLoginModal();
            return;
        }
        const btnc = document.getElementById('claimBtn');
        const spinnerclaim = btnc.querySelector('.spinner-claim');
        const btnClaim = btnc.querySelector('.btn-claim');
        const status = document.getElementById('claimStatus');

        btnc.disabled = true;
        btnClaim.textContent = 'Sedang Memproses...';
        spinnerclaim.style.display = 'inline-block';
        status.textContent = '';

        try {
            const res = await fetch('/api/airdrop/claim', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({
                    wallet_address: wallet
                })
            });

            const contentType = res.headers.get("content-type");
            console.log("Content-Type Response:", contentType);

            // cek apakah response json
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("Server tidak mengembalikan data JSON. Mungkin kamu belum login.");
            }

            const result = await res.json();

            if (res.ok) {
                status.innerHTML = `✅ ${result.message} <br> (+${result.amount} MYNU)`;
                playAirdropAnimation();
            } else {
                // ✨ Tambahkan detail error dari Laravel jika validasi gagal
                if (result.errors && result.errors.wallet_address) {
                    status.innerHTML = `❌ ${result.errors.wallet_address[0]}`;
                } else {
                    status.innerHTML = `❌ ${result.message}`;
                }
            }
        } catch (err) {
            status.innerHTML = '⚠️ Gagal mengklaim: ' + err.message;
        }


        btnc.disabled = false;
        btnClaim.textContent = 'Klaim Sekarang';
        spinnerclaim.style.display = 'none';
    });

    function playAirdropAnimation() {
        const wrapper = document.getElementById("airdropSuccess");
        const el1 = document.getElementById("img1");
        const el2 = document.getElementById("img2");
        const sound = document.getElementById("claimSound");
        // Tampilkan elemen
        wrapper.style.display = "block";
        // Restart animation
        el1.style.animation = "none";
        el2.style.animation = "none";
        void el1.offsetWidth; // force reflow
        void el2.offsetWidth;
        el1.style.animation = "fadeInOnly 2s ease forwards";
        el2.style.animation = "fadeOnce 2s ease 1s forwards";



        // Play sound
        setTimeout(() => {
            sound.currentTime = 0;
            sound.play();
        }, 1000);
        // Play sound
        setTimeout(() => {
            wrapper.style.display = "none";
        }, 3000);
    }
</script>

@endsection