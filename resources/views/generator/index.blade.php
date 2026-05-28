@extends('layouts.app')

@section('content')
<style>
    body {
        margin: 0;
        background: radial-gradient(circle at center, #0b0011 0%, #000 80%);
        color: #eee;
        overflow-x: hidden;
    }

    .mystic-bg {
        position: relative;
        min-height: 100vh;
        overflow: hidden;
    }


    .content {
        position: relative;
        z-index: 1;
        text-align: center;
        padding: 60px 20px;
    }

    h1 {
        font-size: clamp(1.5em, 4vw, 3em);
        color: #c89bff;
        text-shadow: 0 0 15px rgba(180, 0, 255, 0.6);
        margin-bottom: 10px;
        animation: fadeDown 1.5s ease;
    }

    @keyframes fadeDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    p.subtitle {
        color: #aaa;
        max-width: 600px;
        margin: 0 auto 10px;
        font-size: clamp(0.8em, 2.5vw, 1.2em);
        line-height: 1.6em;
        animation: fadeUp 2s ease;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .tab-container {
        width: 90%;
        max-width: 700px;
        margin: 50px auto;
        text-align: center;
        position: relative;
    }

    .tabs {
        display: flex;
        justify-content: center;
        border-bottom: 1px solid rgba(180, 60, 255, 0.4);
        margin-bottom: 30px;
    }

    .tab {
        padding: 12px 30px;
        cursor: pointer;
        color: #b783ff;
        font-weight: 500;
        position: relative;
        transition: all 0.3s ease;
    }

    .tab:hover {
        color: #fff;
    }

    .tab.active {
        color: #fff;
    }

    .tab.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 50%;
        transform: translateX(-50%);
        width: 60%;
        height: 2px;
        background: linear-gradient(to right, #a855f7, #ec4899, #9333ea);
        box-shadow: 0 0 8px #b968ff;
    }

    /* --- Form Section --- */
    .tab-content {
        display: none;
        animation: fadeIn 0.6s ease forwards;
    }

    .tab-content.active {
        display: block;
    }

    form {
        max-width: 600px;
        margin: 0 auto;
        background: rgba(20, 0, 30, 0.6);
        border: 1px solid rgba(150, 0, 200, 0.4);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 0 25px rgba(100, 0, 150, 0.3);
        animation: fadeUp 1.5s ease;
    }

    textarea {
        width: 90%;
        background: rgba(10, 0, 15, 0.8);
        border: 1px solid #4b006e;
        border-radius: 10px;
        padding: 15px;
        color: #eee;
        font-size: 1em;
        resize: none;
        outline: none;
        transition: border 0.3s ease, box-shadow 0.3s ease;
    }

    textarea:focus {
        border-color: #d673ff;
        box-shadow: 0 0 10px rgba(200, 100, 255, 0.4);
    }

    select {
        width: 100%;
        background: rgba(20, 0, 40, 0.7);
        border: 1px solid rgba(160, 60, 255, 0.4);
        border-radius: 8px;
        padding: 10px 12px;
        color: #c084fc;
        font-size: 14px;
        appearance: none;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    select:hover {
        background: rgba(40, 0, 60, 0.8);
    }

    select:focus {
        outline: none;
        box-shadow: 0 0 10px rgba(180, 60, 255, 0.4);
    }

    /* Arrow icon for select */
    select::-ms-expand {
        display: none;
    }

    .ai-generate-btn {
        margin-top: 15px;
        width: 100%;
        background: linear-gradient(90deg, #6a00ff, #c300ff, #6a00ff);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-size: 1.1em;
        font-weight: bold;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        background-size: 200% auto;
        box-shadow: 0 0 20px rgba(140, 0, 255, 0.3);
        animation: glowPulse 4s infinite alternate;
    }

    .ai-generate-btn:hover {
        transform: scale(1.05);
        background-position: right center;
    }

    @keyframes glowPulse {
        0% {
            box-shadow: 0 0 15px rgba(200, 100, 255, 0.4);
        }

        100% {
            box-shadow: 0 0 25px rgba(255, 0, 255, 0.7);
        }
    }

    .gallery {
        max-width: 1100px;
        margin: 80px auto;
        animation: fadeIn 2s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .gallery h2 {
        font-size: 2em;
        color: #d9b3ff;
        margin-bottom: 30px;
        text-shadow: 0 0 10px rgba(180, 0, 255, 0.4);
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        align-items: start;
    }

    .card {
        position: relative;
        overflow: hidden;
        border-radius: 15px;
        box-shadow: 0 0 25px rgba(100, 0, 200, 0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card img {
        width: 100%;
        /* aspect-ratio: 4 / 3; */
        display: block;
        border-radius: 15px;
        transition: transform 0.4s ease;
    }

    .card:hover img {
        transform: scale(1.05);
    }

    .card:hover {
        box-shadow: 0 0 35px rgba(200, 0, 255, 0.4);
        transform: translateY(-5px);
    }

    .card .overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        opacity: 0;
        transition: opacity 0.4s ease;
        padding: 10px;
    }

    .card:hover .overlay {
        opacity: 1;
    }

    .floating-buttons {
        position: absolute;
        bottom: 10px;
        right: 10px;
        display: flex;
        gap: 8px;
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.4s ease;
        z-index: 1;
    }

    /* Saat hover card, tombol muncul */
    .card:hover .floating-buttons {
        opacity: 1;
        transform: translateY(0);
    }

    /* Gaya tombol melayang */
    .floating-buttons a,
    .floating-buttons button {
        background: rgba(40, 0, 60, 0.8);
        color: #d9b3ff;
        border: 1px solid rgba(180, 0, 255, 0.5);
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: 0 0 10px rgba(160, 0, 255, 0.3);
    }

    /* Hover effect: aura mistis */
    .floating-buttons a:hover,
    .floating-buttons button:hover {
        background: rgba(100, 0, 180, 0.9);
        color: #fff;
        box-shadow: 0 0 20px rgba(200, 0, 255, 0.6);
        transform: scale(1.1);
    }

    /* Efek “denyut” halus pada tombol suka */
    .like-btn:active {
        transform: scale(1.2);
        color: #ff4f9d;
        box-shadow: 0 0 20px rgba(255, 0, 150, 0.5);
    }

    /* Saat di mobile, overlay hanya muncul saat aktif */
    @media (hover: none) {
        .card .overlay {
            opacity: 0;
            pointer-events: none;
        }

        .card .overlay.active {
            opacity: 1;
            pointer-events: auto;
            transition: opacity 0.3s ease;
        }
    }

    /* 🌑 Modal Mystic Nusa */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s ease;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background-color: #111;
        border: 1px solid #333;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 0 30px rgba(130, 0, 255, 0.2);
        animation: popIn 0.25s ease;
    }

    .modal-content img {
        max-width: 100%;
        border-radius: 10px;
        margin-bottom: 15px;
        box-shadow: 0 0 15px rgba(150, 100, 255, 0.3);
    }

    .modal-content p {
        color: #ddd;
        font-size: 14px;
        margin-bottom: 15px;
    }

    .modal-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .btn {
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .btn-primary {
        background: linear-gradient(90deg, #6c3bff, #a167ff);
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #7b4fff, #b37fff);
        transform: scale(1.05);
    }

    .btn-secondary {
        background-color: #333;
        color: #ccc;
    }

    .btn-secondary:hover {
        background-color: #444;
        transform: scale(1.05);
    }

    /* ✨ Animasi */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes popIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10, 0, 25, 0.8);
        backdrop-filter: blur(6px);
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal-content {
        background: radial-gradient(circle at top, rgba(40, 0, 80, 0.9), rgba(10, 0, 40, 0.95));
        border: 1px solid rgba(180, 140, 255, 0.3);
        box-shadow: 0 0 40px rgba(120, 80, 255, 0.4);
        border-radius: 18px;
        padding: 25px 35px;
        text-align: center;
        color: #eee;
        max-width: 380px;
        animation: fadeIn 0.3s ease;
    }

    .modal-content h3 {
        font-size: 1.3em;
        color: #e5c8ff;
        margin-bottom: 10px;
    }

    .modal-content p {
        font-size: 0.95em;
        color: #dcd3ff;
        margin-bottom: 20px;
    }

    .modal-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .modal-buttons .btn-yes,
    .modal-buttons .btn-no {
        width: 100px;
        padding: 8px 18px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-yes {
        background: linear-gradient(90deg, #6c3bff, #a167ff);
        color: white;
        box-shadow: 0 0 10px rgba(255, 200, 100, 0.3);
    }

    .btn-no {
        background: rgba(255, 255, 255, 0.1);
        color: #ddd;
    }

    .btn-yes:hover {
        transform: scale(1.1);
        box-shadow: 0 0 20px rgba(255, 200, 100, 0.6);
    }

    .btn-no:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: scale(1.05);
    }
</style>

<div class="mystic-bg">
    <div class="content">
        <h1>🔮 Mystic Nusa Image Generator</h1>
        <p class="subtitle">Biarkan kata-katamu memanggil energi visual dari dunia tak kasat mata.</p>
        <div class="tabs">
            <div class="tab active" data-tab="instant">Instant Generate</div>
            <div class="tab" data-tab="generate">Deep Generate</div>
        </div>
        <div id="instant" class="tab-content active">
            <form id="instantForm">
                <textarea name="prompt" rows="4" placeholder="Tuliskan deskripsi mistis..." required></textarea>
                <div class="mb-4">
                    <div style="margin-top: 10px;">
                        <label>Image Ratio</label>
                        <select name="image_ratio" class="bg-gray-900 text-purple-300 rounded p-2 w-full">
                            <option value="square_1_1">Square (1:1)</option>
                            <option value="classic_4_3">Classic (4:3)</option>
                            <option value="traditional_3_4">Portrait (9:16)</option>
                            <option value="widescreen_16_9">Landscape (16:9)</option>
                            <option value="social_story_9_16">Social Story (9:16)</option>
                            <option value="standard_3_2">Standard (3:2)</option>
                            <option value="portrait_2_3">Portrait (2:3)</option>
                            <option value="horizontal_2_1">Horizontal (2:1)</option>
                            <option value="vertical_1_2">Vertical (1:2)</option>
                            <option value="social_post_4_5">Social Post (4:5)</option>
                        </select>
                    </div>
                    <div style="margin-top: 10px;">
                        <label>Style</label>
                        <select name="style" class="bg-gray-900 text-purple-300 rounded p-2 w-full">
                            <option value="photo">Photo</option>
                            <option value="digital-art">Digital Art</option>
                            <option value="3d">3D</option>
                            <option value="painting">Painting</option>
                            <option value="low-poly">Low Poly</option>
                            <option value="pixel-art">Pixel Art</option>
                            <option value="anime">Anime</option>
                            <option value="cyberpunk">Cyberpunk</option>
                            <option value="comic">Comic</option>
                            <option value="vintage">Vintage</option>
                            <option value="cartoon">Cartoon</option>
                            <option value="vector">Vector</option>
                            <option value="studio-shot">Studio Shot</option>
                            <option value="dark">Dark</option>
                            <option value="sketch">Sketch</option>
                            <option value="mockup">Mockup</option>
                            <option value="2000s-pone">2000s-Pone</option>
                            <option value="70s-vibe">70s-Vibe</option>
                            <option value="watercolor">Watercolor</option>
                            <option value="art-nouveau">Art Nouveau</option>
                            <option value="origami">Origami</option>
                            <option value="surreal">Surreal</option>
                            <option value="fantasy">Fantasy</option>
                            <option value="traditional-japan">Traditional Japan</option>
                        </select>
                    </div>
                    <div style="margin-top: 10px;">
                        <label>Color</label>
                        <select name="effects[color]" class="bg-gray-900 text-purple-300 rounded p-2 w-full">
                            <option value=" b&w">Black & White</option>
                            <option value="pastel">Pastel</option>
                            <option value="sepia">Sepia</option>
                            <option value="dramatic">Dramatic</option>
                            <option value="vibrant">Vibrant</option>
                            <option value="orange&teal">Orange & Teal</option>
                            <option value="film-filter">Film Filter</option>
                            <option value="split">Split</option>
                            <option value="electric">Electric</option>
                            <option value="pastel-pink">Pastel Pink</option>
                            <option value="gold-glow">Gold Glow</option>
                            <option value="autumn">Autumn</option>
                            <option value="muted-green">Muted Green</option>
                            <option value="deep-teal">Deep Teal</option>
                            <option value="duotone">Duotone</option>
                            <option value="terracotta&teal">Terracotta & Teal</option>
                            <option value="red&blue">Red & Blue</option>
                            <option value="cold-neon">Cold Neon</option>
                            <option value="burgundy&blue">Burgundy & Blue</option>
                        </select>
                    </div>
                    <div style="margin-top: 10px;">
                        <label>Lightning</label>
                        <select name="effects[lightning]" class="bg-gray-900 text-purple-300 rounded p-2 w-full">
                            <option value="studio">Studio</option>
                            <option value="warm">Warm</option>
                            <option value="cinematic">Cinematic</option>
                            <option value="volumetric">Volumetric</option>
                            <option value="golden-hour">Golden Hour</option>
                            <option value="long-exposure">Long Exposure</option>
                            <option value="cold">Cold</option>
                            <option value="iridescent">Iridescent</option>
                            <option value="dramatic">Dramatic</option>
                            <option value="hardlight">Hardlight</option>
                            <option value="redscale">Redscale</option>
                            <option value="indoor-light">Indoor Light</option>
                        </select>
                    </div>
                    <div style="margin-top: 10px;">
                        <label>Frame</label>
                        <select name="effects[framing]" class="bg-gray-900 text-purple-300 rounded p-2 w-full">
                            <option value="portrait">Portrait</option>
                            <option value="macro">Macro</option>
                            <option value="panoramic">Panoramic</option>
                            <option value="aerial-view">Aerial View</option>
                            <option value="close-up">Close Up</option>
                            <option value="cinematic">Cinematic</option>
                            <option value="high-angle">High Angle</option>
                            <option value="low-angle">Low Angle</option>
                            <option value="symmetry">Symmetry</option>
                            <option value="fish-eye">Fish Eye</option>
                            <option value="first-person">First Person</option>
                        </select>
                    </div>
                </div>
                <button class="ai-generate-btn" type="submit">✨ Panggil Visualisasi</button>
                <div id="instantStatus" style="margin-top: 15px; color: #ffb3ff;"></div>
            </form>
        </div>

        <div id="generate" class="tab-content">
            <form id="generateForm">
                <textarea name="prompt" rows="4" placeholder="Tuliskan deskripsi mistis..." required></textarea>
                <div class="mb-4">
                    <div style="margin-top: 10px;">
                        <label>Aspect Ratio</label>
                        <select name="aspect_ratio">
                            <option value="square_1_1">Square (1:1)</option>
                            <option value="classic_4_3">Classic (4:3)</option>
                            <option value="traditional_3_4">Portrait (9:16)</option>
                            <option value="widescreen_16_9">Landscape (16:9)</option>
                            <option value="social_story_9_16">Social Story (9:16)</option>
                            <option value="standard_3_2">Standard (3:2)</option>
                            <option value="portrait_2_3">Portrait (2:3)</option>
                            <option value="horizontal_2_1">Horizontal (2:1)</option>
                            <option value="vertical_1_2">Vertical (1:2)</option>
                            <option value="social_post_4_5">Social Post (4:5)</option>
                        </select>
                    </div>

                    <div style="margin-top: 10px;">
                        <label>Color Effect</label>
                        <select name="effects[color]">
                            <option value="softhue">Soft Hue</option>
                            <option value="b&w">Black & White</option>
                            <option value="goldglow">Gold Glow</option>
                            <option value="vibrant">Vibrant</option>
                            <option value="coldneon">Cold Neon</option>
                        </select>
                    </div>

                    <div style="margin-top: 10px;">
                        <label>Framing</label>
                        <select name="effects[framing]">
                            <option value="portrait">Portrait</option>
                            <option value="lowangle">Low Angle</option>
                            <option value="midshot">Mid Shot</option>
                            <option value="wideshot">Wide Shot</option>
                            <option value="tiltshot">Tilt Shot</option>
                            <option value="aerial">Aerial</option>
                        </select>
                    </div>

                    <div style="margin-top: 10px;">
                        <label>Lightning</label>
                        <select name="effects[lightning]">
                            <option value="iridescent">Iridescent</option>
                            <option value="dramatic">Dramatic</option>
                            <option value="goldenhour">Golden Hour</option>
                            <option value="longexposure">Long Exposure</option>
                            <option value="indorlight">Indoor Light</option>
                            <option value="flash">Flash</option>
                            <option value="neon">Neon</option>
                        </select>
                    </div>
                </div>
                <button class="ai-generate-btn" type="submit">✨ Panggil Visualisasi</button>
                <div id="generateStatus" style="margin-top: 15px; color: #ffb3ff;"></div>
            </form>
        </div>



        @if($latest->count())
        <div class="gallery">
            <h2>✨ Karya Mistis Terbaru ✨</h2>
            <div class="grid">
                @foreach($latest as $img)
                <div class="card" onclick="toggleOverlay(this)">
                    <img src="{{ asset($img->image_url) }}" alt="Generated Image">
                    <div class="overlay">
                        <p>{{ Str::limit($img->prompt, 60) }}</p>
                        <div class="floating-buttons">
                            <a href="{{ asset($img->image_url) }}" download title="Download">
                                <button class="icon svg-icon" title="Download">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <polyline points="7 10 12 15 17 10" />
                                        <line x1="12" y1="15" x2="12" y2="3" />
                                    </svg>
                                </button>
                            </a>
                            <!-- <button class="icon svg-icon love" title="Sukai">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1 7.8 7.8 7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z" />
                                </svg>
                            </button> -->
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(asset($img->image_url)) }}" target="_blank"><img src="/images/x.png" alt="Twitter" style="width: 24px; height: 24px;"></a>
                            <a href="https://www.tiktok.com/upload?url={{ urlencode(asset($img->image_url)) }}" target="_blank"><img src="/images/tiktok.png" alt="TikTok" style="width: 24px; height: 24px;"></a>
                            <a href="https://www.instagram.com/?url={{ urlencode(asset($img->image_url)) }}" target="_blank"><img src="/images/instagram.png" alt="Instagram" style="width: 24px; height: 24px;"></a>

                        </div>
                    </div>
                </div>

                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
<!-- Modal Result Gambar -->
<!-- Modal Hasil Gambar -->
<div id="resultModal" class="modal">
    <div class="modal-content">
        <img id="resultImage" src="" alt="Generated Image">
        <p id="resultText">Gambar berhasil dibuat ✨</p>
        <div class="modal-buttons">
            <button id="closeModal" class="btn btn-secondary">Tutup</button>
            <button id="viewResult" class="btn btn-primary">Lihat Detail</button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi -->
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <h3 id="confirmTitle">Konfirmasi</h3>
        <p id="confirmMessage">Apakah kamu yakin ingin melanjutkan?</p>
        <div class="modal-buttons">
            <button id="confirmYes" class="btn-yes">Ya</button>
            <button id="confirmNo" class="btn-no">Tidak</button>
        </div>
    </div>
</div>


<script>
    const tabs = document.querySelectorAll('.tab');
    const contents = document.querySelectorAll('.tab-content');


    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const target = tab.getAttribute('data-tab');
            contents.forEach(c => {
                c.classList.remove('active');
                if (c.id === target) c.classList.add('active');
            });
        });
    });

    function toggleOverlay(card) {
        const overlay = card.querySelector('.overlay');
        // tutup overlay lain agar tidak numpuk
        document.querySelectorAll('.overlay.active').forEach(o => {
            if (o !== overlay) o.classList.remove('active');
        });
        overlay.classList.toggle('active');
    }

    function showConfirmationModal(title, message) {
        return new Promise((resolve) => {
            const modal = document.getElementById('confirmModal');
            const titleEl = document.getElementById('confirmTitle');
            const messageEl = document.getElementById('confirmMessage');
            const yesBtn = document.getElementById('confirmYes');
            const noBtn = document.getElementById('confirmNo');

            titleEl.textContent = title;
            messageEl.textContent = message;
            modal.style.display = 'flex';

            const cleanup = () => {
                modal.style.display = 'none';
                yesBtn.onclick = null;
                noBtn.onclick = null;
            };

            yesBtn.onclick = () => {
                cleanup();
                resolve(true);
            };
            noBtn.onclick = () => {
                cleanup();
                resolve(false);
            };
        });
    }

    async function handleFormSubmit(formId, statusId, endpoint) {
        const form = document.getElementById(formId);
        const statusDiv = document.getElementById(statusId);

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Tentukan pesan modal berdasarkan form
            const isInstant = formId === 'instantForm';
            const confirmTitle = isInstant ? '✨ Panggil Energi Instan?' : '🔮 Mulai Ritual Visualisasi?';
            const confirmMessage = isInstant ?
                'Dibutuhkan 1.000 $MYNU energi mistis untuk panggilan instan. Lanjutkan?' :
                'Dibutuhkan 10.000 $MYNU energi mistis untuk ritual visualisasi. Lanjutkan?';

            // Tampilkan modal dan tunggu hasilnya
            const confirmed = await showConfirmationModal(confirmTitle, confirmMessage);
            if (!confirmed) return; // batal

            // Jika pengguna menekan YA, lanjutkan submit
            const formData = new FormData(this);
            const data = {};

            statusDiv.textContent = 'Membuat tugas pembuatan gambar... ✨';

            formData.forEach((value, key) => {
                if (key.startsWith('effects[')) {
                    const innerKey = key.match(/effects\[(.*?)\]/)[1];
                    data.effects = data.effects || {};
                    data.effects[innerKey] = value;
                } else {
                    data[key] = value;
                }
            });

            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                if (res.ok && result.status === 'success') {
                    const data = result.data;

                    if (formId === 'generateForm') {
                        statusDiv.textContent += ' Gambar akan muncul setelah proses selesai.';
                        window.location.href = `/ai-generator/result/${result.task_id}`;
                        setTimeout(() => (statusDiv.textContent = ''), 5000);
                        return;
                    }

                    statusDiv.textContent = '✨ Gambar berhasil dibuat!';
                    setTimeout(() => (statusDiv.textContent = ''), 5000);

                    const modal = document.getElementById('resultModal');
                    const img = document.getElementById('resultImage');
                    const text = document.getElementById('resultText');
                    const closeBtn = document.getElementById('closeModal');
                    const viewBtn = document.getElementById('viewResult');

                    img.src = '/' + data.image_url;
                    text.textContent = result.message || 'Gambar berhasil dibuat ✨';
                    closeBtn.onclick = () => modal.classList.remove('active');
                    viewBtn.onclick = () => window.location.href = `/ai-generator/result/${data.task_id}`;
                    modal.classList.add('active');
                } else {
                    statusDiv.textContent = `⚠️ Gagal: ${result.message || 'Terjadi kesalahan tak terduga.'}`;
                    console.warn('⚠️ Error Response:', result);
                }
            } catch (error) {
                console.error('❌ Fetch Error:', error);
                statusDiv.textContent = '❌ Gagal membuat tugas: Kesalahan koneksi.';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        handleFormSubmit('instantForm', 'instantStatus', '/api/ai-generator/instant');
        handleFormSubmit('generateForm', 'generateStatus', '/api/ai-generator/task');
    });
</script>
@endsection