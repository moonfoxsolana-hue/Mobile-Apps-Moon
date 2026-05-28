@extends('layouts.app')

@section('content')
<style>
    /* 🌌 Mystic Nusa Elite Dark Style (Refined) */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    /* Global Reset/Enhancements */
    body {
        font-family: 'Inter', sans-serif;
        background-color: #050508;
        /* Darker overall background */
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
    }

    .config-container {
        max-width: 800px;
        /* Lebihkan lebar container */
        margin: 50px auto;
        /* Lebih banyak margin vertikal */
        background: rgba(10, 10, 18, 0.9);
        /* Sedikit transparan/lebih gelap */
        padding: 40px;
        /* Padding lebih besar */
        border-radius: 20px;
        /* Sudut lebih membulat */
        /* Meningkatkan bayangan untuk kesan 'melayang' premium */
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(85, 85, 120, 0.15);
        border: none;
        /* Hapus border solid */
        backdrop-filter: blur(5px);
        /* Efek blur pada latar belakang (jika ada) */
        width: 100%;
        /* Memastikan lebar maksimum bekerja */
    }

    .config-header {
        text-align: center;
        margin-bottom: 35px;
        /* Margin lebih besar */
    }

    .config-header h2 {
        color: #e0e0f0;
        /* Warna sedikit lebih terang */
        font-size: 36px;
        /* Font lebih besar */
        font-weight: 700;
        /* Mengubah warna shadow menjadi warna aksen utama yang lebih jernih */
        text-shadow: 0 0 20px rgba(140, 100, 255, 0.6);
        letter-spacing: 1px;
    }

    /* Gaya untuk ikon di header */
    .header-icon {
        font-size: 40px;
        color: #8f6bff;
        margin-right: 10px;
    }

    /* Garis pemisah untuk pemisahan elegan */
    .section-divider {
        border: 0;
        height: 1px;
        background: linear-gradient(to right, rgba(0, 0, 0, 0), #38384a, rgba(0, 0, 0, 0));
        margin: 30px 0;
    }

    .config-section {
        margin-bottom: 25px;
        /* Margin lebih besar */
    }

    .config-section label {
        display: block;
        color: #b0b0cc;
        /* Warna lebih lembut */
        font-weight: 500;
        margin-bottom: 8px;
        /* Margin lebih besar */
        font-size: 15px;
        /* Font lebih besar */
        letter-spacing: 0.2px;
    }

    .config-section input,
    .config-section textarea,
    .config-section select {
        width: 100%;
        /* Mengubah max-width menjadi 100% untuk kolom */
        background: #14141c;
        /* Lebih gelap dari sebelumnya */
        border: 1px solid #28283a;
        /* Border lebih lembut */
        padding: 14px 16px;
        /* Padding lebih besar */
        font-size: 15px;
        color: #f0f0ff;
        border-radius: 12px;
        /* Sudut lebih membulat */
        transition: all .3s cubic-bezier(0.25, 0.8, 0.25, 1);
        /* Transisi lebih halus */
        box-sizing: border-box;
        /* Memastikan padding tidak menambah lebar */
    }

    .config-section input:focus,
    .config-section textarea:focus,
    .config-section select:focus {
        border-color: #8f6bff;
        /* Aksen warna ungu yang lebih hidup */
        /* Bayangan fokus yang lebih halus dan lebih terang */
        box-shadow: 0 0 0 4px rgba(143, 107, 255, 0.2), 0 0 15px rgba(143, 107, 255, 0.4);
        background-color: #1a1a25;
        /* Sedikit terang saat fokus */
        outline: none;
    }

    textarea {
        min-height: 120px;
        /* Ketinggian yang lebih besar */
        resize: vertical;
    }

    /* Penyesuaian judul bagian (h3) */
    .config-container h3 {
        color: #c8c8ff;
        /* Warna aksen yang menonjol */
        font-size: 22px;
        /* Ukuran lebih besar */
        font-weight: 600;
        margin-bottom: 15px !important;
        margin-top: 35px !important;
        padding-left: 15px;
        /* Padding lebih besar */
        border-left: 4px solid #8f6bff;
        /* Aksen garis vertikal */
    }

    /* Peningkatan pada status-pill */
    .header-pills {
        display: flex;
        justify-content: center;
        gap: 15px;
        /* Jarak antar pill */
        flex-wrap: wrap;
        margin-bottom: 15px;
    }

    .status-pill,
    .header-pills a.status-pill {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 14px;
        color: #fff;
        font-weight: 600;
        margin-top: 5px;
        /* Dihilangkan dari margin-top asli */
        /* Tambahkan sedikit bayangan pada pill */
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        text-decoration: none;
        /* Hilangkan garis bawah pada link */
        transition: background 0.3s, transform 0.2s;
        border: none;
        /* Hilangkan border button default */
        cursor: pointer;
    }

    /* Mengatur style link di dalam button */
    .header-pills .status-pill a {
        color: inherit;
        text-decoration: none;
    }

    .status-pill:hover {
        transform: translateY(-1px);
    }

    /* Penggunaan baru untuk pill link */
    .pill-link {
        background: #252538;
        /* Warna latar belakang yang lembut */
        color: #a0a0c0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3), inset 0 0 0 1px #38384a;
    }

    .pill-link:hover {
        background: #38384a;
        color: #c8c8ff;
    }


    /* Pill status utama */
    #status-pill-box .status-pill {
        padding: 10px 20px;
        font-size: 16px;
    }

    .pill-active {
        background: #3cb371;
    }

    /* Warna hijau yang lebih kaya */
    .pill-inactive {
        background: #e64e62;
    }

    /* Warna merah yang lebih kaya */

    .submit-btn {
        width: 100%;
        margin-top: 30px;
        /* Margin lebih besar */
        padding: 16px;
        border: none;
        border-radius: 14px;
        /* Sudut lebih membulat */
        /* Gradient yang lebih dalam dan kaya */
        background: linear-gradient(135deg, #7059ff, #5d44ff);
        color: #fff;
        font-size: 18px;
        /* Font lebih besar */
        font-weight: 700;
        cursor: pointer;
        letter-spacing: 1px;
        /* Letter spacing lebih besar */
        text-transform: uppercase;
        /* Transisi yang lebih halus */
        transition: all .3s cubic-bezier(0.25, 0.8, 0.25, 1);
        /* Bayangan tombol yang lebih menonjol */
        box-shadow: 0 8px 25px rgba(110, 80, 255, 0.55);
        /* Bayangan lebih intens */
    }

    .submit-btn:hover {
        /* Gradient yang sedikit lebih terang saat hover */
        background: linear-gradient(135deg, #8f6bff, #7059ff);
        /* Bayangan yang lebih jelas saat hover */
        box-shadow: 0 12px 35px rgba(120, 90, 255, 0.8);
        transform: translateY(-3px);
        /* Efek mengangkat lebih kuat */
    }

    .two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        /* Jarak antar kolom lebih besar */
    }

    /* Penempatan tombol di tengah */
    .config-section-centered {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
    }

    @media(max-width: 900px) {
        .config-container {
            margin: 20px;
            padding: 30px;
        }

        .two-col {
            grid-template-columns: 1fr;
            gap: 0;
            /* Hapus gap di mobile */
        }

        .config-header h2 {
            font-size: 28px;
        }
    }

    /* --- Tambahan CSS untuk Tombol "Hubungkan Akun" Elegance --- */

    #yt-auth-btn {
        margin-top: 25px;
        /* Memberikan sedikit jarak dari pill */
        margin-bottom: 25px;
        display: flex;
        justify-content: center;
    }

    .connect-btn {
        /* Gaya Dasar */
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 30px;
        /* Padding lebih besar */
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-transform: capitalize;
        letter-spacing: 0.8px;

        /* Warna & Aksen Premium */
        background: linear-gradient(90deg, #5d44ff, #8f6bff);
        /* Gradient ungu */
        color: #fff;

        /* Efek & Transisi */
        transition: all .3s cubic-bezier(0.25, 0.8, 0.25, 1);
        box-shadow: 0 6px 20px rgba(110, 80, 255, 0.45);
        /* Bayangan halus */
    }

    .connect-btn:hover {
        background: linear-gradient(90deg, #7059ff, #a28cff);
        /* Gradient lebih terang saat hover */
        box-shadow: 0 8px 25px rgba(120, 90, 255, 0.6);
        transform: translateY(-2px);
        /* Efek mengangkat halus */
    }

    /* Icon (Opsional, jika Anda menggunakan ikon) */
    .connect-btn svg {
        margin-right: 10px;
        height: 20px;
        width: 20px;
        fill: currentColor;
    }

    /* Style untuk alert sukses */
    .alert-success-style {
        background: #0d5b3f;
        color: white;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        font-weight: 500;
        text-align: left;
    }

    /* Contoh CSS sederhana untuk tampilan tag */
    .tag-input-wrapper {
        border: 1px solid #ccc;
        padding: 5px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 5px;
        /* Jarak antar tag */
        min-height: 40px;
    }

    #tag-input-field {
        border: none;
        outline: none;
        flex-grow: 1;
        padding: 5px 0;
    }

    #tag-pills-list {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        order: -1;
        /* Memastikan tag pills muncul di sebelah kiri input */
    }

    .tag-pill {
        background-color: #e0e0e0;
        color: #333;
        padding: 5px 10px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        font-size: 0.9em;
    }

    .tag-pill .remove-tag {
        margin-left: 8px;
        cursor: pointer;
        font-weight: bold;
        color: #888;
    }

    .tag-pill .remove-tag:hover {
        color: #000;
    }

    /* Gaya untuk membungkus input dan tombol */
    .password-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    /* Gaya untuk input agar tombol mata berada di sebelah kanan */
    .password-input-wrapper input[type="password"],
    .password-input-wrapper input[type="text"] {
        padding-right: 40px;
        /* Memberikan ruang untuk tombol */
        width: 100%;
        box-sizing: border-box;
    }

    /* Gaya untuk tombol toggle */
    .toggle-password-btn {
        position: absolute;
        right: 5px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        color: #6c757d;
        /* Warna ikon */
        font-size: 1.1em;
    }

    .toggle-password-btn:focus {
        outline: none;
    }

    /* --- CSS untuk Modal Pop-up --- */
    .modal-overlay {
        display: none;
        /* Sembunyikan secara default */
        position: fixed;
        z-index: 1000;
        /* Pastikan di atas semua elemen lain */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.8);
        /* Latar belakang gelap transparan */
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background-color: #050508;
        /* Background gelap seperti container */
        margin: 5% auto;
        /* 5% dari atas dan di tengah secara horizontal */
        padding: 30px;
        border: none;
        width: 90%;
        max-width: 700px;
        /* Lebih kecil dari container utama */
        border-radius: 20px;
        position: relative;
        box-shadow: 0 10px 50px rgba(140, 100, 255, 0.4);
        animation: fadeIn 0.3s ease-out;
        /* Animasi masuk */
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .modal-header h2 {
        color: #e0e0f0;
        font-size: 28px;
        font-weight: 700;
    }

    .close-modal-btn {
        color: #b0b0cc;
        font-size: 36px;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.2s;
    }

    .close-modal-btn:hover,
    .close-modal-btn:focus {
        color: #8f6bff;
    }

    /* Animasi Fade In */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Tambahkan style untuk tombol pemicu modal di dalam container */
    #open-modal-btn {
        width: 100%;
        margin-top: 15px;
        padding: 12px;
        border: 1px solid #8f6bff;
        border-radius: 12px;
        background: transparent;
        color: #8f6bff;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    #open-modal-btn:hover {
        background: rgba(143, 107, 255, 0.1);
        box-shadow: 0 0 10px rgba(143, 107, 255, 0.5);
    }

    /* Layout dasar untuk config-section */
    .config-section {
        margin-bottom: 20px;
    }

    /* Container untuk label dan toggle */
    .toggle-container {
        display: flex;
        flex-direction: column;
    }

    .switch-label {
        font-weight: bold;
        margin-bottom: 8px;
    }

    /* Dasar Switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    /* Sembunyikan checkbox asli */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* Background Slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
    }

    /* Lingkaran Slider */
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
    }

    /* Warna saat ON (1) */
    input:checked+.slider {
        background-color: #2196F3;
    }

    /* Gerakan saat ON (1) */
    input:checked+.slider:before {
        transform: translateX(26px);
    }

    /* Slider Bulat */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="config-container">

    <div class="config-header">
        <h2><span class="header-icon"><i class="fab fa-youtube"></i></span> YouTube Configuration</h2>

        <div class="header-pills">
            <a href="/youtube-list-themes" class="status-pill pill-link">List Tema</a>
            <a href="/youtube-content" class="status-pill pill-link">List Content</a>
        </div>

        <div id="status-pill-box">
            <div class="status-pill">Status: <span id="status"></span></div>
        </div>

        <div id="yt-auth-btn">
            <button class="connect-btn">
                <Span>Harap Tunggu...</Span>
            </button>
        </div>

        @if(session('success'))
        <div id="alert-success" class="alert-success-style">
            {{ session('success') }}
        </div>

        <script>
            setTimeout(() => {
                const el = document.getElementById('alert-success');
                if (el) {
                    el.style.transition = "opacity 0.5s ease";
                    el.style.opacity = 0;

                    setTimeout(() => el.remove(), 500); // hapus setelah fade-out
                }
            }, 3000); // 3 detik

            // Perbarui status di sini (Contoh: setelah sukses terhubung)
            document.getElementById('status').innerText = 'CONNECTED';
            document.getElementById('status-pill-box').querySelector('.status-pill').classList.remove('pill-inactive');
            document.getElementById('status-pill-box').querySelector('.status-pill').classList.add('pill-active');
        </script>
        @else
        <script>
            document.getElementById('status').innerText = '';
        </script>
        @endif

    </div>

    <form id="config-form">

        <h3><i class="fas fa-id-card"></i> Channel Information</h3>

        <div class="two-col">
            <div class="config-section">
                <label for="channel_name">Channel Name (Wajib)</label>
                <input type="text" id="channel_name" name="channel_name" placeholder="E.g., Mystic Nusa">
            </div>

            <div class="config-section">
                <label for="channel_niche">Channel Niche</label>
                <input type="text" id="channel_niche" name="channel_niche" placeholder="E.g., History, Culture, Tech">
            </div>
        </div>

        <div class="config-section">
            <label for="channel_description">Channel Description (Wajib)</label>
            <textarea id="channel_description" name="channel_description" placeholder="A brief description of your channel's content..."></textarea>
        </div>

        <div class="two-col">
            <div class="config-section">
                <label for="channel_category">Channel Category (Wajib)</label>
                <select id="channel_category" name="channel_category">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="2">Autos & Vehicles</option>
                    <option value="23">Comedy</option>
                    <option value="27">Education</option>
                    <option value="24">Entertainment</option>
                    <option value="1">Film & Animation</option>
                    <option value="28">Howto & Style</option>
                    <option value="10">Music</option>
                    <option value="19">Travel & Events</option>
                    <option value="22">People & Blogs</option>
                    <option value="17">Sports</option>
                    <option value="20">Gaming</option>
                </select>
            </div>

            <div class="config-section">
                <label for="channel_tag">Channel Tags (Wajib - Max 20 Tags)</label>

                <div id="tags-container" class="tag-input-wrapper">
                    <input type="text" id="tag-input-field" placeholder="Tambahkan tag dan pisahkan dengan Koma">

                    <div id="tag-pills-list"></div>
                </div>

                <textarea id="channel_tag" name="channel_tag" style="display:none;"></textarea>

                <small>Tags akan disimpan sebagai: 'tag1', 'tag2', ... (Dipisahkan koma dan diapit kutip tunggal).</small>
            </div>
        </div>

        <div class="config-section">
            <label for="channel_status">Channel Status (Wajib)</label>
            <select id="channel_status" name="channel_status">
                <option value="">-- Pilih Status Privasi --</option>
                <option value="public">Public</option>
                <option value="private">Private</option>
            </select>
        </div>

        <hr class="section-divider">

        <div class="config-section">
            <label for="time_upload">Time Upload</label>

            <select id="time_upload" name="time_upload">
                <option value="">-- Pilih Waktu Upload --</option>
            </select>
        </div>
        <div class="config-section">
            <label for="status">Status Auto Generate (Wajib)</label>
            <select id="status" name="status">
                <option value="">-- Pilih Status--</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>

        <div id="msg-box"></div>
        <button type="button" id="open-modal-btn">
            <i class="fas fa-cog"></i> Konfigurasi API & Prompts
        </button>

        <div id="msg-box"></div>

        <button class="submit-btn" type="submit">
            <i class="fas fa-save"></i> Save Configuration
        </button>

    </form>
</div>


<div id="api-prompt-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2>API & Prompt Settings</h2>
            <span class="close-modal-btn">&times;</span>
        </div>

        <form id="modal-form">

            <h3><i class="fas fa-key"></i> API Keys</h3>

            <div class="two-col">
                <div class="config-section">
                    <label for="api_key_gemini">Gemini API Key (Wajib)</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="api_key_gemini_modal" name="api_key_gemini" placeholder="Enter your key" class="password-field">

                        <button type="button" class="toggle-password-btn toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="config-section">
                    <label for="api_key_groq">Groq API Key (Wajib)</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="api_key_groq_modal" name="api_key_groq" placeholder="Enter your key" class="password-field">

                        <button type="button" class="toggle-password-btn toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="two-col">
                <div class="config-section">
                    <label for="api_key_murfai">MurfAI API Key</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="api_key_murfai_modal" name="api_key_murfai" placeholder="Enter your key" class="password-field">

                        <button type="button" class="toggle-password-btn toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="config-section">
                    <label for="api_key_minimax">Minimax API Key</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="api_key_minimax_modal" name="api_key_minimax" placeholder="Enter your key" class="password-field">

                        <button type="button" class="toggle-password-btn toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="two-col">
                <div class="config-section">
                    <label for="api_key_assemblyai">AssemblyAI API Key (Wajib)</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="api_key_assemblyai_modal" name="api_key_assemblyai" placeholder="Enter your key" class="password-field">

                        <button type="button" class="toggle-password-btn toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="config-section">
                    <label for="api_key_freepik">Freepik API Key (Wajib)</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="api_key_freepik_modal" name="api_key_freepik" placeholder="Enter your key" class="password-field">

                        <button type="button" class="toggle-password-btn toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="config-section">
                <label for="api_key_deapi">DeAPI Key (Wajib)</label>
                <div class="password-input-wrapper">
                    <input type="password" id="api_key_deapi_modal" name="api_key_deapi" placeholder="Enter your key" class="password-field">

                    <button type="button" class="toggle-password-btn toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <hr class="section-divider">

            <h3><i class="fas fa-magic"></i> Prompt Configuration</h3>

            <div class="config-section">
                <label for="prompt_story">Story Prompt (Wajib)</label>
                <textarea id="prompt_story_modal" name="prompt_story" placeholder="Instructions for generating the story/script..."></textarea>
            </div>

            <div class="config-section">
                <label for="prompt_visual">Visual Prompt (Wajib)</label>
                <textarea id="prompt_visual_modal" name="prompt_visual" placeholder="Instructions for generating visual elements..."></textarea>
            </div>

            <div class="config-section">
                <label for="prompt_audio">Audio Prompt</label>
                <textarea id="prompt_audio_modal" name="prompt_audio" placeholder="Instructions for selecting/generating audio..."></textarea>
            </div>
            <div class="config-section">
                <label for="gemini_voice_id">Gemini Voice ID (Wajib)</label>
                <select id="gemini_voice_id_modal" name="gemini_voice_id">
                    <option value="">-- Pilih Voice ID --</option>
                    <option value="Zephyr">Zephyr -- Bright</option>
                    <option value="Puck">Puck -- Upbeat</option>
                    <option value="Charon">Charon -- Informative</option>
                    <option value="Kore">Kore -- Firm</option>
                    <option value="Fenrir">Fenrir -- Excitable</option>
                    <option value="Leda">Leda -- Youthful</option>
                    <option value="Orus">Orus -- Firm</option>
                    <option value="Aoede">Aoede -- Breezy</option>
                    <option value="Callirrhoe">Callirrhoe -- Easy-going</option>
                    <option value="Autonoe">Autonoe -- Bright</option>
                    <option value="Enceladus">Enceladus -- Breathy</option>
                    <option value="Iapetus">Iapetus -- Clear</option>
                    <option value="Umbriel">Umbriel -- Easy-going</option>
                    <option value="Algieba">Algieba -- Smooth</option>
                    <option value="Despina">Despina -- Smooth</option>
                    <option value="Erinome">Erinome -- Clear</option>
                    <option value="Algenib">Algenib -- Gravelly</option>
                    <option value="Rasalgethi">Rasalgethi -- Informative</option>
                    <option value="Laomedeia">Laomedeia -- Upbeat</option>
                    <option value="Achernar">Achernar -- Soft</option>
                    <option value="Alnilam">Alnilam -- Firm</option>
                    <option value="Schedar">Schedar -- Even</option>
                    <option value="Gacrux">Gacrux -- Mature</option>
                    <option value="Pulcherrima">Pulcherrima -- Forward</option>
                    <option value="Achird">Achird -- Friendly</option>
                    <option value="Zubenelgenubi">Zubenelgenubi -- Casual</option>
                    <option value="Vindemiatrix">Vindemiatrix -- Gentle</option>
                    <option value="Sadachbia">Sadachbia -- Lively</option>
                    <option value="Sadaltager">Sadaltager -- Knowledgeable</option>
                    <option value="Sulafat">Sulafat -- Warm</option>

                </select>
            </div>
            <div class="config-section">
                <label for="backsound_audio">Backsound Audio</label>
                <textarea id="backsound_audio_modal" name="backsound_audio" placeholder="Path public for backsound audio..."></textarea>
            </div>
            <div class="config-section">
                <label for="prompt_video">Video Prompt</label>
                <textarea id="prompt_video_modal" name="prompt_video" placeholder="Instructions for compiling the final video..."></textarea>
            </div>
            <div class="config-section toggle-container">
                <label class="switch">
                    <input type="hidden" name="is_video_with_image_only" value="0">
                    <input type="checkbox" id="is_video_with_image_only" name="is_video_with_image_only" value="1">
                    <span class="slider round"></span>
                </label>
                <small style="display: block; color: #666; margin-top: 5px;">
                    On: Generate video dari gambar saja (Hemat credit).<br>
                    Off: Generate video menggunakan AI Video (DeAPI/Veo).
                </small>
            </div>
            <button type="button" class="submit-btn" onclick="document.getElementById('api-prompt-modal').style.display='none';">
                Tutup Modal & Lanjutkan
            </button>
        </form>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- Setup Awal ---
        const token = localStorage.getItem("token");
        if (!token) {
            alert("Token tidak ditemukan, silakan login ulang.");
            return;
        }

        // --- DOM Elements ---
        const configForm = document.getElementById("config-form");
        const tagInputField = document.getElementById('tag-input-field');
        const tagPillsList = document.getElementById('tag-pills-list');
        const hiddenTagTextarea = document.getElementById('channel_tag');
        const msgbox = document.getElementById("msg-box");
        const modal = document.getElementById("api-prompt-modal");
        const closeModalBtn = document.querySelector(".close-modal-btn");
        const modalTriggerBtn = document.getElementById("open-modal-btn");
        const MAX_TAGS = 20;
        let tags = [];
        // ----------------------------------------------------------------------
        // 1. PERBAIKAN BUG TAGS & LOGIKA
        // ----------------------------------------------------------------------

        function updateTagsUI() {
            tagPillsList.innerHTML = '';
            tags.forEach((tag, index) => {
                const pill = document.createElement('span');
                // Mengganti data-index agar lebih aman, menggunakan event delegation
                pill.className = 'tag-pill';
                // Gunakan backticks untuk HTML multi-line
                pill.innerHTML = `${tag} <span class="remove-tag" data-tag-value="${tag}">&times;</span>`;
                tagPillsList.appendChild(pill);
            });

            // Perbaikan Bug: Pastikan format kutip tunggal ('tag1', 'tag2') untuk backend Laravel
            hiddenTagTextarea.value = tags.map(tag => `'${tag}'`).join(', ');

            // Perbaikan Bug: Logika display input
            if (tags.length >= MAX_TAGS) {
                tagInputField.style.display = 'none';
                tagInputField.placeholder = 'Batas maksimum tag (20) tercapai.';
            } else {
                tagInputField.style.display = 'block';
                tagInputField.placeholder = 'Tambahkan tag dan pisah dengan Koma';
            }
        }

        function addTag(inputTag) {
            let tag = inputTag.trim();
            if (tag.length > 0) {
                // Bersihkan kutip tunggal/ganda yang mungkin terbawa
                tag = tag.replace(/['"]/g, '');
            }

            // Cek jika tag valid, batas belum tercapai, dan belum ada (case-insensitive)
            if (tag.length > 0 && tags.length < MAX_TAGS && !tags.some(t => t.toLowerCase() === tag.toLowerCase())) {
                tags.push(tag);
                updateTagsUI();
            }
        }

        // Ambil input dari field, proses multi-tag, dan bersihkan input
        function processTagInput() {
            // Ambil value, hapus koma trailing jika ada
            let tagValue = tagInputField.value.replace(/,$/, '');

            if (tagValue) {
                // Mendukung input multi-tag dipisahkan koma
                tagValue.split(',').forEach(t => addTag(t.trim())); // Pastikan setiap tag di trim
                tagInputField.value = ''; // Bersihkan input
            }
        }

        // Event listener untuk input field (ketika user menekan Enter atau Koma)
        tagInputField.addEventListener('keyup', function(e) {
            if (tags.length >= MAX_TAGS) return;

            // Cek jika Enter (kode 13) atau Koma (kode 188) ditekan
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                processTagInput();
            }
        });

        // Event listener untuk menghapus tag ketika tombol 'x' diklik
        tagPillsList.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-tag')) {
                const tagValueToRemove = e.target.dataset.tagValue;
                // Hapus tag berdasarkan nilai (memastikan menghapus yang benar)
                tags = tags.filter(t => t !== tagValueToRemove);
                updateTagsUI();
            }
        });

        // Load initial tags dari hidden field saat config dimuat (Perbaikan Bug Loading Awal)
        function loadInitialTags(initialTagsString) {
            if (!initialTagsString) return;

            tags = initialTagsString
                .replace(/['"]/g, '') // Hapus kutip tunggal/ganda
                .split(',')
                .map(tag => tag.trim())
                .filter(tag => tag.length > 0)
                .slice(0, MAX_TAGS);

            updateTagsUI();
        }

        // ----------------------------------------------------------------------
        // 2. LOGIKA MODAL POP-UP
        // ----------------------------------------------------------------------

        modalTriggerBtn.addEventListener('click', () => {
            modal.style.display = 'block';
        });

        closeModalBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }

        // ----------------------------------------------------------------------
        // 3. LOAD & SUBMIT DATA
        // ----------------------------------------------------------------------

        // Load config
        fetch("/api/youtube/index", {
                headers: {
                    "Authorization": "Bearer " + token
                }
            })
            .then(res => res.json())
            .then(config => {
                // Fill form values & Modal values
                Object.keys(config).forEach(key => {
                    // Cari input di form utama ATAU di modal
                    const input = document.querySelector(`#config-form [name="${key}"], #api-prompt-modal [name="${key}"]`);
                    if (input) input.value = config[key] || ''; // Gunakan || '' untuk menghindari undefined

                    // Khusus untuk API Keys, set value yang 'terlihat' (jika tidak kosong)
                    if (key.startsWith('api_key_') && config[key] && config[key].length > 0) {
                        // Set nilai input ke '********' untuk keamanan visual, 
                        // TAPI nilai asli tetap tersimpan jika tidak diubah
                        input.dataset.originalValue = config[key]; // Simpan nilai asli
                        input.value = '********';
                    }
                });

                // Panggil fungsi load tag setelah data konfigurasi masuk
                const initialTags = config.channel_tag || '';
                loadInitialTags(initialTags);


                // Logic YouTube link button & status
                document.getElementById("yt-auth-btn").style.display = 'none';
                if (!config.is_linked) {
                    document.getElementById("yt-auth-btn").innerHTML = `
                <a href="/youtube/auth?token=${token}" class="connect-btn" style="background: linear-gradient(135deg,#ff3c3c,#ff6b6b); box-shadow:0 0 15px rgba(255,80,80,0.4);">
                    <i class="fas fa-link" style="margin-right: 8px;"></i> Link Ke YouTube
                </a>
                `;
                    document.getElementById("yt-auth-btn").style.display = 'block';
                }
                // Update Status Pill
                const statusPill = document.getElementById("status-pill-box").querySelector('.status-pill');
                statusPill.classList.remove('pill-active', 'pill-inactive');
                statusPill.classList.add(config.is_linked === 1 ? "pill-active" : "pill-inactive");
                statusPill.innerHTML = `Status: ${config.is_linked === 1 ? "Terhubung" : "Belum Terhubung"}`;
            });

        // Submit update
        configForm.addEventListener("submit", function(e) {
            e.preventDefault();

            // Kumpulkan data dari form utama
            const formData = new FormData(configForm);

            // Kumpulkan data dari form modal
            const modalForm = document.getElementById("modal-form");
            const modalFormData = new FormData(modalForm);

            // Gabungkan kedua data ke dalam satu objek JSON
            let jsonData = Object.fromEntries(formData.entries());
            let modalJsonData = Object.fromEntries(modalFormData.entries());

            const toggleEl = document.getElementById('is_video_with_image_only');
            if (toggleEl) {
                modalJsonData['is_video_with_image_only'] = toggleEl.checked ? "1" : "0";
            }

            // Gabungkan data
            jsonData = {
                ...jsonData,
                ...modalJsonData
            };

            console.log("Sekarang pasti benar:", jsonData['is_video_with_image_only']);
            // Logika Keamanan API Key: Jika value-nya '********', gunakan nilai asli yang tersimpan di data-original-value
            document.querySelectorAll('#api-prompt-modal .password-field').forEach(input => {
                if (input.value === '********' && input.dataset.originalValue) {
                    jsonData[input.name] = input.dataset.originalValue;
                }
            });

            // Hapus field yang tidak diperlukan (opsional, tergantung backend)
            delete jsonData['tag-input-field'];

            msgbox.innerHTML = `<div class="alert-success-style" style="background:#5d44ff; text-align:center;">Menyimpan konfigurasi...</div>`;

            fetch("/api/youtube/update", {
                    method: "POST",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status == 'success') {
                        msgbox.innerHTML = `<div class="alert-success-style" style="background:#0d5b3f;">${data.message || 'Configuration saved successfully!'}</div>`;
                    } else {
                        msgbox.innerHTML = `<div class="alert-success-style" style="background:#e64e62;">${data.message || 'Failed to save configuration.'}</div>`;
                    }

                    setTimeout(() => {
                        msgbox.innerHTML = "";
                    }, 4000);
                })
                .catch(error => {
                    msgbox.innerHTML = `<div class="alert-success-style" style="background:#e64e62;">An error occurred: ${error.message}</div>`;
                });
        });

        // ... (Logika Toggle Password dan Generate Time Upload tetap sama) ...
        const toggleButtons = document.querySelectorAll('.toggle-password');
        toggleButtons.forEach(toggleButton => {
            toggleButton.addEventListener('click', function() {
                const wrapper = this.closest('.password-input-wrapper');
                const passwordInput = wrapper.querySelector('.password-field');
                const icon = this.querySelector('i');
                if (!passwordInput) return;
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                if (type === 'text') {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Generate Time Upload Options
        const timeSelect = document.getElementById('time_upload');
        const interval = 10;

        function formatTime(hour, minute) {
            const h = String(hour).padStart(2, '0');
            const m = String(minute).padStart(2, '0');
            return `${h}:${m}`;
        }
        for (let hour = 6; hour < 23; hour++) {
            for (let minute = 0; minute < 60; minute += interval) {
                if (hour === 23 && minute > 50) break;
                const timeValue = formatTime(hour, minute);
                const option = document.createElement('option');
                option.value = timeValue;
                option.textContent = timeValue;
                timeSelect.appendChild(option);
            }
        }
    });
</script>
<!-- 
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const token = localStorage.getItem("token");

        if (!token) {
            alert("Token tidak ditemukan, silakan login ulang.");
            return;
        }

        // Load config
        fetch("/api/youtube/index", {
                headers: {
                    "Authorization": "Bearer " + token
                }
            })
            .then(res => res.json())
            .then(config => {
                // Fill form values
                Object.keys(config).forEach(key => {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input) input.value = config[key];
                });
                document.getElementById("yt-auth-btn").style.display = 'none';
                // YouTube link button
                if (!config.is_linked) {
                    document.getElementById("yt-auth-btn").innerHTML = `
                <a href="/youtube/auth?token=${token}"
                    style="
                        display:inline-block;
                        padding:12px 22px;
                        background:linear-gradient(135deg,#ff3c3c,#ff6b6b);
                        color:#fff;
                        font-weight:700;
                        border-radius:12px;
                        text-decoration:none;
                        box-shadow:0 0 15px rgba(255,80,80,0.4);
                        transition:0.25s;
                    ">
                    🔗 Link To YouTube
                </a>
            `;
                    document.getElementById("yt-auth-btn").style.display = 'block';
                }
                document.getElementById("status").innerHTML = `
            <div class="status-pill ${config.is_linked === 1 ? "pill-active" : "pill-inactive"}">
                ${config.is_linked === 1 ? "Terhubung" : "Belum Terhubung"}
            </div>
        `;
            });

        // Submit update
        document.getElementById("config-form").addEventListener("submit", function(e) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const jsonData = Object.fromEntries(formData.entries());
            const msgbox = document.getElementById("msg-box");
            msgbox.innerHTML = "Saving...";
            fetch("/api/youtube/update", {
                    method: "POST",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(res => res.json())
                .then(data => {
                    msgbox.innerHTML = `<div style="padding:10px;background:#0a0f0a;border:1px solid #00ff88;border-radius:8px;margin-top:15px;">Configuration saved successfully!</div>`;
                    setTimeout(() => {
                        msgbox.innerHTML = "";
                    }, 4000);
                });
        });

        const tagInputField = document.getElementById('tag-input-field');
        const tagPillsList = document.getElementById('tag-pills-list');
        const hiddenTagTextarea = document.getElementById('channel_tag');
        const MAX_TAGS = 20;

        let tags = []; // Array untuk menyimpan state tag

        // ----------------------------------------------------------------------
        // FUNGSI UTAMA: UPDATE UI DAN HIDDEN FIELD
        // ----------------------------------------------------------------------

        function updateTagsUI() {
            tagPillsList.innerHTML = ''; // Kosongkan tampilan lama

            tags.forEach((tag, index) => {
                const pill = document.createElement('span');
                pill.className = 'tag-pill';
                pill.innerHTML = `${tag} <span class="remove-tag" data-index="${index}">&times;</span>`;
                tagPillsList.appendChild(pill);
            });

            // Update value di textarea tersembunyi dengan format yang diminta: 'tag1', 'tag2', ...
            hiddenTagTextarea.value = tags.map(tag => `'${tag}'`).join(', ');

            // Sembunyikan input field jika batas tag tercapai
            if (tags.length >= MAX_TAGS) {
                tagInputField.style.display = 'none';
                tagInputField.placeholder = 'Batas maksimum tag (20) tercapai.';
            } else {
                tagInputField.style.display = 'block';
                tagInputField.placeholder = 'Tambahkan tag dan pisah dengan Koma';
            }
        }

        // ----------------------------------------------------------------------
        // FUNGSI MENAMBAH TAG
        // ----------------------------------------------------------------------

        function addTag(inputTag) {
            let tag = inputTag.trim();

            // Cek jika tag valid dan belum ada (case-insensitive)
            if (tag.length > 0 && tags.length < MAX_TAGS && !tags.some(t => t.toLowerCase() === tag.toLowerCase())) {
                tags.push(tag);
                updateTagsUI();
            }
        }

        // ----------------------------------------------------------------------
        // EVENT LISTENERS
        // ----------------------------------------------------------------------

        // Event listener untuk input field (ketika user menekan Enter atau Koma)
        tagInputField.addEventListener('keyup', function(e) {
            if (tags.length >= MAX_TAGS) return;

            // Cek jika Enter (kode 13) atau Koma (kode 188) ditekan
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();

                // Ambil value, hapus koma trailing jika ada
                let tagValue = tagInputField.value.replace(/,$/, '');

                if (tagValue) {
                    // Mendukung input multi-tag dipisahkan koma (Contoh: tag1,tag2,tag3)
                    tagValue.split(',').forEach(t => addTag(t));
                    tagInputField.value = ''; // Bersihkan input
                }
            }
        });

        // Event listener untuk menghapus tag ketika tombol 'x' diklik
        tagPillsList.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-tag')) {
                const index = parseInt(e.target.dataset.index);
                tags.splice(index, 1); // Hapus elemen dari array
                updateTagsUI();
            }
        });

        // Opsional: Jika Anda memiliki data tag yang dimuat dari server saat halaman dibuka
        function loadInitialTags(initialTagsString) {
            // Asumsikan initialTagsString adalah "tag1, tag2, 'tag3'"
            tags = initialTagsString.replace(/['"]/g, '') // Hapus kutip
                .split(',')
                .map(tag => tag.trim())
                .filter(tag => tag.length > 0)
                .slice(0, MAX_TAGS);
            updateTagsUI();
        }

        loadInitialTags(hiddenTagTextarea.value);
        const toggleButtons = document.querySelectorAll('.toggle-password');

        toggleButtons.forEach(toggleButton => {
            // Untuk setiap tombol, tambahkan event listener
            toggleButton.addEventListener('click', function() {
                // Temukan elemen induk (password-input-wrapper)
                const wrapper = this.closest('.password-input-wrapper');

                // Temukan input field di dalam wrapper yang sama (menggunakan class 'password-field')
                const passwordInput = wrapper.querySelector('.password-field');

                // Temukan ikon di dalam tombol
                const icon = this.querySelector('i');

                if (!passwordInput) return; // Keluar jika input tidak ditemukan

                // Logika Toggle:
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';

                // 1. Ubah tipe input
                passwordInput.setAttribute('type', type);

                // 2. Ubah ikon
                if (type === 'text') {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
        const timeSelect = document.getElementById('time_upload');
        const interval = 10; // Interval dalam menit

        function formatTime(hour, minute) {
            // Fungsi helper untuk memastikan format HH:MM (misal: 09:05)
            const h = String(hour).padStart(2, '0');
            const m = String(minute).padStart(2, '0');
            return `${h}:${m}`;
        }

        // Loop dari jam 0 (00:00) hingga jam 23
        for (let hour = 0; hour < 24; hour++) {
            // Loop untuk menit (0, 10, 20, 30, 40, 50)
            for (let minute = 0; minute < 60; minute += interval) {

                // Cek apakah sudah melebihi 23:50
                if (hour === 23 && minute > 50) {
                    break;
                }

                const timeValue = formatTime(hour, minute);

                // Buat elemen <option>
                const option = document.createElement('option');
                option.value = timeValue;
                option.textContent = timeValue;

                timeSelect.appendChild(option);
            }
        }
    });
</script> -->

@endsection