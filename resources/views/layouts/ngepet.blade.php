<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mystic Nusa - Token Mistis Indonesia</title>
  <meta name="description" content="Token kripto bertema budaya dan spiritual Indonesia. Mainkan permainan Ngepet Online dan jelajahi dunia mistis di Mystic Nusa.">
  <meta name="robots" content="index, follow">
  <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Metal+Mania&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Braah+One&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Honk&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Bungee&display=swap" rel="stylesheet"> -->
  <link href="https://fonts.googleapis.com/css2?family=Calistoga&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Bootstrap 5 CSS (sudah ada biasanya) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap 5 JS (WAJIB supaya bootstrap.Modal jalan) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    body {
      margin: 0;
      background: linear-gradient(to top, rgb(4, 15, 19), rgb(9, 24, 28), rgb(5, 17, 21));
      color: white;
      font-family: 'Unbounded', sans-serif;
      cursor: url('/images/asset/cursor.png') 8 8, auto;
    }

    button,
    a {
      cursor: url('/images/asset/pointer.png') 8 2, pointer;
    }

    i.fa-solid {
      font-family: "Font Awesome 6 Free";
      font-weight: 900;
    }

    .hero-bg-wrapper {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100vh;
      background: url('/images/asset/games/background/ngepet-background.jpg') no-repeat center center fixed;
      background-size: cover;
      z-index: -1;
      will-change: filter;
    }

    video#fogVideo {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      object-fit: cover;
      z-index: -0.5;
      opacity: 0.1;
      pointer-events: none;
      filter: brightness(2.2) contrast(2.1);
    }

    .video-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.1);
      /* atur transparansi */
      z-index: -0.8;
      /* di atas video, tapi di bawah semua elemen */
    }

    .mystic-token-container {
      position: fixed;
      top: 15px;
      left: 15px;
      display: flex;
      align-items: center;
      gap: 8px;
      z-index: 1000;
    }

    /* Animasi & Utilitas */
    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    @keyframes fadeIn {
      from {
        transform: scale(0.95);
        opacity: 0;
      }

      to {
        transform: scale(1);
        opacity: 1;
      }
    }

    .modal-backdrop {
      z-index: 1040 !important;
    }

    /* Container utama tab */
    .mystic-tabs {
      border: none;
      display: flex;
      gap: 20px;
      padding-bottom: 10px;
      justify-content: flex-start;
    }

    /* Hilangkan style bawaan Bootstrap */
    .mystic-tabs .nav-link {
      all: unset;
      /* Reset semua bawaan button/a */
      cursor: pointer;
      font-weight: 600;
      font-size: 1.1rem;
      padding: 8px 16px;
      color: #ffe100ff;
      border-radius: 6px;
      position: relative;
      transition: all 0.3s ease-in-out;
    }

    /* Garis bawah glowing */
    .mystic-tabs .nav-link::after {
      content: "";
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0%;
      height: 3px;
      background: #ffe100ff;
      box-shadow: 0 0 10px #cbff10ff, 0 0 20px #d9ff00ff;
      transition: width 0.3s ease-in-out;
    }

    /* Hover efek: garis bawah glowing */
    .mystic-tabs .nav-link:hover::after {
      width: 100%;
    }

    /* Tab aktif lebih menonjol */
    .mystic-tabs .nav-link.active {
      color: #fffb00ff;
      text-shadow: 0 0 6px #f4ff25ff, 0 0 12px #ffdf2cff;
      background-color: transparent !important;
    }

    /* Tab aktif: garis bawah penuh */
    .mystic-tabs .nav-link.active::after {
      width: 100%;
    }

    /* Style tombol menu biar seragam */
    /* Container utama */
    .menu-container {
      position: fixed;
      bottom: 15px;
      right: 15px;
      display: flex;
      align-items: center;
      gap: 8px;
      z-index: 1000;
    }

    /* Bar ikon menu */
    .menu-icons {
      display: flex;
      flex-direction: row;
      background: rgba(20, 20, 20, 0.6);
      padding: clamp(0.1rem, 0.8rem, 0.2rem);
      border-radius: 12px;
      backdrop-filter: blur(6px);
      gap: clamp(0.1rem, 0.8rem, 0.2rem);
      transition: transform 0.3s ease, opacity 0.3s ease;
    }

    /* Tombol menu ikon */
    .menu-btn {
      position: relative;
      background-color: transparent;
      border: none;
      color: white;
      font-size: clamp(0.8rem, 2vw, 1.8rem);
      cursor: pointer;
      padding: clamp(0.2rem, 0.8rem, 0.4rem);
      transition: transform 0.2s ease, box-shadow 0.3s ease;
    }

    .menu-btn:hover {
      transform: scale(1.4);
    }

    /* Tooltip menu */
    .menu-btn::after {
      content: attr(data-tooltip);
      position: absolute;
      bottom: 42px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(30, 30, 30, 0.85);
      color: white;
      font-size: 12px;
      padding: 4px 8px;
      border-radius: 6px;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.2s ease;
      white-space: nowrap;
    }

    .menu-btn:hover::after {
      opacity: 1;
    }

    /* Collapse state */
    .menu-container.collapsed .menu-icons {
      transform: translateX(90%);
      opacity: 100;
    }

    .exit-menu-container {
      position: fixed;
      bottom: 15px;
      left: 15px;
      display: flex;
      align-items: center;
      gap: 8px;
      z-index: 1000;
    }

    /* Animasi Fade In Modal */
    @keyframes fade-in {
      from {
        opacity: 0;
        transform: scale(0.9);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .animate-fade-in {
      animation: fade-in 0.3s ease-out;
    }

    .card {
      background: transparent !important;
    }

    /* Hilangkan background putih PNG */
    .avatar-img {
      margin: auto;
    }

    /* Style dasar kartu avatar */
    .avatar-card {
      border: 2px solid transparent;
      cursor: pointer;
      transition: transform 0.25s ease, box-shadow 0.3s ease;
    }

    .avatar-card img {
      display: block;
      margin: 0 auto;
      width: 100%;
      height: auto;
      transition: transform 0.3s ease;
    }

    /* Zoom sedikit saat dipilih */
    .avatar-card.selected {
      transform: scale(1.05);
    }

    /* Glow berdasarkan tier */
    .avatar-card.selected[data-tier="common"] img {
      animation: glowCommon 2s ease-in-out infinite;

    }

    .avatar-card.selected[data-tier="uncommon"] img {
      animation: glowUncommon 2s ease-in-out infinite;
    }

    .avatar-card.selected[data-tier="rare"] img {
      animation: glowRare 2s ease-in-out infinite;
    }

    .avatar-card.selected[data-tier="mythical"] img {
      animation: glowMythical 3s ease-in-out infinite;
    }

    .avatar-card.selected[data-tier="legendary"] img {
      animation: glowLegendary 3s ease-in-out infinite;
    }

    /* Animasi breathing glow */
    @keyframes glowCommon {
      0% {
        filter: drop-shadow(0 0 3px rgba(87, 241, 255, 0.3)) drop-shadow(0 0 6px rgba(87, 241, 255, 0.5));
      }

      50% {
        filter: drop-shadow(0 0 10px rgba(87, 241, 255, 0.95)) drop-shadow(0 0 18px rgba(87, 241, 255, 0.8));
      }

      100% {
        filter: drop-shadow(0 0 3px rgba(87, 241, 255, 0.3)) drop-shadow(0 0 6px rgba(87, 241, 255, 0.5));
      }
    }

    @keyframes glowUncommon {
      0% {
        filter: drop-shadow(0 0 4px rgba(0, 255, 128, 0.4)) drop-shadow(0 0 8px rgba(0, 255, 128, 0.6));
      }

      50% {
        filter: drop-shadow(0 0 12px rgba(0, 255, 128, 1)) drop-shadow(0 0 20px rgba(0, 255, 128, 0.8));
      }

      100% {
        filter: drop-shadow(0 0 4px rgba(0, 255, 128, 0.4)) drop-shadow(0 0 8px rgba(0, 255, 128, 0.6));
      }
    }

    @keyframes glowRare {
      0% {
        filter: drop-shadow(0 0 5px rgba(0, 153, 255, 0.4)) drop-shadow(0 0 10px rgba(0, 153, 255, 0.6));
      }

      50% {
        filter: drop-shadow(0 0 15px rgba(0, 153, 255, 1)) drop-shadow(0 0 25px rgba(0, 153, 255, 0.9));
      }

      100% {
        filter: drop-shadow(0 0 5px rgba(0, 153, 255, 0.4)) drop-shadow(0 0 10px rgba(0, 153, 255, 0.6));
      }
    }

    @keyframes glowMythical {
      0% {
        filter: drop-shadow(0 0 6px rgba(186, 85, 211, 0.5)) drop-shadow(0 0 12px rgba(186, 85, 211, 0.7));
      }

      50% {
        filter: drop-shadow(0 0 18px rgba(186, 85, 211, 1)) drop-shadow(0 0 28px rgba(186, 85, 211, 0.9));
      }

      100% {
        filter: drop-shadow(0 0 6px rgba(186, 85, 211, 0.5)) drop-shadow(0 0 12px rgba(186, 85, 211, 0.7));
      }
    }

    @keyframes glowLegendary {
      0% {
        filter: drop-shadow(0 0 8px rgba(239, 139, 68, 0.5)) drop-shadow(0 0 15px rgba(239, 134, 68, 0.7));
      }

      50% {
        filter: drop-shadow(0 0 20px rgba(239, 145, 68, 1)) drop-shadow(0 0 35px rgba(239, 128, 68, 0.9));
      }

      100% {
        filter: drop-shadow(0 0 8px rgba(239, 128, 68, 0.5)) drop-shadow(0 0 15px rgba(239, 134, 68, 0.7));
      }
    }

    .avs-avatar-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.5);
    }

    .inv-avatar-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.5);
    }

    .modal-body-scrollable {
      max-height: 70vh;
      /* Batasi tinggi modal-body menjadi 70% dari tinggi viewport */
      overflow-y: auto;
      /* Tambahkan scrollbar vertikal jika konten melebihi max-height */
      padding: 1rem;
    }

    .btn-tab {
      background: none !important;
      /* hilangkan background */
      border: none !important;
      /* hilangkan border */
      color: #aaa;
      /* warna default */
      font-weight: bold;
      /* tulisan tebal */
      padding: 4px 8px;
      transition: color 0.2s ease;
    }

    .btn-tab .active {
      color: #fff;
      /* warna ketika aktif */
      border-bottom: 2px solid #0d6efd;
      /* garis bawah ala tab */
    }

    strong {
      font-size: clamp(0.8rem, 2vw, 1.2rem);
      text-shadow: 0 0 8px #facc15;
      color: rgb(240, 205, 65);
      font-weight: bold;
    }
  </style>
</head>

<body>
  <video autoplay muted loop id="fogVideo">
    <source src="{{ asset('videos/fog.webm') }}" type="video/webm">
  </video>
  <div class="hero-bg-wrapper" id="heroBackground"></div>

  <div id="gameMenu" class="menu-container">
    <!-- Tombol Toggle -->

    <!-- Menu Ikon -->
    <div id="menuButtons" class="menu-icons">
      <button id="toggleMenu" class="menu-btn" data-tooltip="Show / Hide"> ⏩
      </button>
      <button id="btnInventory" class="menu-btn" data-tooltip="Inventory">🎒</button>
      <button id="btnAvatarshop" class="menu-btn" data-tooltip="Avatar Shop">🛒</button>
      <!-- <button id="btnItemshop" class="menu-btn" data-tooltip="Item Shop">👕</button> -->
      <button id="btnLeaderboard" class="menu-btn" data-tooltip="Leaderboard">🏆</button>
      <button id="btnHistory" class="menu-btn" data-tooltip="History">📜</button>
      <!-- <button class="menu-btn" data-tooltip="Profile">👤</button>
      <button class="menu-btn" data-tooltip="Settings">⚙️</button> -->
      <button id="btnRules" class="menu-btn" data-tooltip="Rules">
        📋
      </button>
      <button id="btnSound" class="menu-btn" data-tooltip="Sound">
        🔊
      </button>
    </div>
  </div>



  <div class="modal fade" id="rulesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content" style="background:#111; color:#fff; border:2px solid #ffadffff; box-shadow: 0 0 25px #f8bbffff;">
        <div class="modal-header">
          <h5 class="modal-title">📜 Rules Ngepet Online</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- Tab Navigation -->
          <ul class="nav nav-tabs mystic-tabs" id="rulesTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <a
                class="nav-link active"
                id="host-tab"
                data-bs-toggle="tab"
                href="#host-rules"
                role="tab"
                aria-controls="host-rules"
                aria-selected="true">
                🏠 Rules Pembuka Rumah
              </a>
            </li>
            <li class="nav-item" role="presentation">
              <a
                class="nav-link"
                id="intruder-tab"
                data-bs-toggle="tab"
                href="#intruder-rules"
                role="tab"
                aria-controls="intruder-rules"
                aria-selected="false">
                🐖 Rules Babi Ngepet
              </a>
            </li>
          </ul>



          <!-- Tab Content -->
          <div class="tab-content mt-3" id="rulesTabsContent">
            <!-- Host Rules -->
            <div class="tab-pane fade show active" id="host-rules" role="tabpanel" aria-labelledby="host-tab">
              <ul style="list-style:none; padding-left:0;">
                <li>🏠 <b>Buat Rumah:</b> Host membuat rumah dengan mengisi nama host.</li>
                <li>🏰 <b>Avatar Rumah:</b> Gunakan avatar rumah yang dimiliki.</li>
                <li>💎 <b>Token Pancingan:</b> Isi jumlah token untuk memancing babi ngepet.</li>
                <li>⭐ <b>Tingkat Kesulitan:</b> Pilih tingkat kesulitan menebak:
                  <ul style="list-style:none; padding-left:1rem;">
                    <li>🟢 Easy → 5x percobaan</li>
                    <li>🟡 Medium → 4x percobaan</li>
                    <li>🔴 Hard → 3x percobaan</li>
                  </ul>
                </li>
                <li>⏱️ <b>Durasi Waktu:</b> Tentukan waktu untuk babi bersembunyi dan host menebak (durasi sama).</li>
                <li>⚔️ <b>Batas Token Intruders:</b> Atur minimal dan maksimal token untuk babi ngepet masuk.</li>
                <li>👥 <b>Batas Penyusup:</b> Atur jumlah maksimum Penyusup yang bisa masuk.</li>
                <li>💰 <b>Menang:</b> Jika tebakanmu benar, token si babi jadi milik host.</li>
                <li>📜 <b>Aturan Final:</b> Semua hasil permainan tidak bisa diganggu gugat.</li>
              </ul>

            </div>

            <!-- Intruder Rules -->
            <div class="tab-pane fade" id="intruder-rules" role="tabpanel" aria-labelledby="intruder-tab">
              <ul style="list-style:none; padding-left:0;">
                <li>🐖 <b>Pilih Rumah:</b> Pilih rumah yang tersedia selama slot penyusup masih ada.</li>
                <li>⭐ <b>Tingkat Kesulitan:</b> Selalu lihat tingkat kesulitan rumah:
                  <ul style="list-style:none; padding-left:1rem;">
                    <li>🟢 Easy → Host 5x menebak (berbahaya untuk penyusup)</li>
                    <li>🟡 Medium → Host 4x menebak (hati-hati untuk penyusup)</li>
                    <li>🔴 Hard → Host 3x menebak (terlihat mudah untuk penyusup)</li>
                  </ul>
                </li>
                <li>💎 <b>Tumbal Token:</b> Tumbalkan sejumlah token untuk menjadi babi ngepet.</li>
                <li>📦 <b>Pilih Tempat Sembunyi:</b> Setelah join, pilih 1 (satu) barang untuk bersembunyi.</li>
                <li>🙅‍♂️ <b>Jika Host Salah:</b> Jika host salah semua tebakan, babi ngepet menang.</li>
                <li>⏳ <b>Jika Waktu Habis:</b> Jika host tidak menebak dalam waktu yang ditentukan, babi ngepet menang.</li>
                <li>🎯 <b>Kalah Jika Ketahuan:</b> Jika host menebak barang tempat bersembunyi, babi ngepet kalah.</li>
                <li>💰 <b>Hadiah Kemenangan:</b> Jika menang, dapatkan token 2x lipat dari jumlah token tumbal.</li>
                <li>📜 <b>Aturan Final:</b> Semua hasil permainan tidak bisa diganggu gugat.</li>
              </ul>

            </div>
          </div>
        </div>

        <div class="modal-footer" style="border-top:1px solid #f8bbffff;">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
            ❌ Tutup
          </button>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="mysticModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content" style="background:#111; color:#fff; border:2px solid #ffadffff; box-shadow: 0 0 25px #f8bbffff;">
        <div class="modal-header">
          <h5 id="mysticModalTitle" class="modal-title">📜 Mystic Nusa</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- Tab Navigation -->
          <div id="mysticModalContent" class="px-6 text-white-300 leading-relaxed space-y-3">
            <!-- Dinamis lewat JS -->
          </div>
        </div>

        <div class="modal-footer" style="border-top:1px solid #f8bbffff;justify-content: center;">
          <div id="mysticmodalResponse" class="w-80 text-center"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Mystic Nusa -->
  <div class="modal fade" id="mysticModalMatch" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:420px; margin:auto;">
      <div class="modal-content" style="background:#111; color:#fff; border:2px solid #ffadffff; box-shadow: 0 0 25px #f8bbffff;">
        <div class="modal-body">
          <!-- Tab Navigation -->
          <div id="mysticModalMatchContent">
            <!-- Dinamis lewat JS -->
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="mystictoken" class="mystic-token-container">
    <button id="btnmynu" class="menu-btn">
      <img src="/images/asset/mystic-nusa-token.png" alt="Mystic Token" style="width:24px; height:24px;" /><strong id="mysticTokenAmount" style="margin-left:6px; font-size:16px;">0</strong>
    </button>
  </div>

  <audio id="bgm" loop>
    <source src="/sound/bgm/bgm.mp3" type="audio/mpeg">
  </audio>

  <audio id="clickSound">
    <source src="/sound/sfx/click2.wav" type="audio/mpeg">
  </audio>
  <audio id="hoverSound">
    <source src="/sound/sfx/hover3.mp3" type="audio/mpeg">
  </audio>
  <audio id="successSound">
    <source src="/sound/sfx/success.ogg" type="audio/mpeg">
  </audio>

  @yield('content')
  <script>
    const MasterModal = document.getElementById("mysticModal");
    const MasterTitleModal = document.getElementById("mysticModalTitle");
    const MasterContentModal = document.getElementById("mysticModalContent");

    async function fetchAPI(url, method = 'GET', body = null) {
      try {
        const response = await fetch(url, {
          method,
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "Authorization": `Bearer ${token}`
          },
          body: body ? JSON.stringify(body) : null
        });
        return await response.json();
      } catch (error) {
        console.error("API Error:", error);
        return {
          status: "error",
          message: "Gagal memuat data."
        };
      }
    }

    // === [RENDER INVENTORY] ===
    async function loadInventory() {
      MasterTitleModal.textContent = "🎒 Inventory";
      MasterContentModal.innerHTML = `
      <div class="tab-content mt-3" id="invTabsContent">
        <div class="tab-pane fade show active" id="inv-avatars" role="tabpanel" aria-labelledby="inv-avatars-tab">
          <div id="invAvatarList" class="d-flex flex-wrap gap-3 justify-content-start"></div>
        </div>
        <div class="tab-pane fade" id="inv-items" role="tabpanel" aria-labelledby="inv-items-tab">
          <p class="text-center text-white mt-3">🚧 Fitur item sedang dikembangkan 🚧</p>
        </div>
      </div>
    `;

      // Ambil data avatar dari API
      const invApiRes = await fetchAPI("/api/ngepet/avatar/own");

      const invListEl = document.getElementById("invAvatarList");
      invListEl.innerHTML = "";
      if (invApiRes?.status === "success" && Array.isArray(invApiRes.data) && invApiRes.data.length > 0) {
        invApiRes.data.forEach(invEntry => {
          const av = invEntry.avatar;
          const invIsEquipped = Number(invEntry.is_equipped) === 1;

          const invCard = document.createElement("div");
          invCard.className = "inv-avatar-card text-center p-2";
          invCard.style = `
          width: 120px;
          border: 2px solid ${invIsEquipped ? `${getTierColor(av.tier)}` : "#444"};
          border-radius: 10px;
          background: rgba(20, 20, 20, 0.85);
          box-shadow: ${invIsEquipped ? `0 0 15px ${getTierColor(av.tier)}` : "0 0 8px #222"};
          transition: 0.3s ease;
        `;

          invCard.innerHTML = `
          <img src="/${av.image_url}" alt="${av.name}" class="img-fluid rounded mb-2" style="border-radius: 8px;">
          <p class="fw-bold mb-1" style="font-size:14px; min-height: 40px; display: flex; align-items: center; justify-content: center;">
            ${av.name}
          </p>
          <span class="badge mb-2" style="background:${getTierColor(av.tier)}; display: inline-block;">
            ${av.tier}
          </span>
          <div class="mt-2" style="min-height: 32px;">
            <button id="inv-equip-btn-${av.id}" type="button"
                    class="btn btn-sm w-100 ${invIsEquipped ? 'btn-secondary' : 'btn-info'} inv-equip-btn"
                    ${invIsEquipped ? 'disabled' : ''}
                    data-avatar-id="${av.id}" data-avatar-tier="${av.tier}">
              ${invIsEquipped ? '✅ Dipakai' : 'Gunakan'}
            </button>
          </div>
        `;

          invListEl.appendChild(invCard);
        });
      } else {
        invListEl.innerHTML = `<p class="text-center w-100 mt-3"> Kamu belum memiliki avatar</p>`;
      }

      const modalmenu = new bootstrap.Modal(document.getElementById("mysticModal"));
      modalmenu.show();
      attachHoverSoundmenu();
    }

    document.addEventListener("click", function(ev) {
      const invTargetBtn = ev.target.closest(".inv-equip-btn");
      if (!invTargetBtn) return;

      const invId = invTargetBtn.getAttribute("data-avatar-id");
      const invtier = invTargetBtn.getAttribute("data-avatar-tier");
      if (invId) equipAvatarNamespaced(invId, invTargetBtn, invtier);
    });

    // === [EQUIP AVATAR] ===
    async function equipAvatarNamespaced(invAvatarId, invBtnEl, invtier) {
      try {
        const btn = document.getElementById(`inv-equip-btn-${invAvatarId}`);
        btn.disabled = true;
        btn.innerHTML = "⏳ Proses";
        const equipRes = await fetchAPI(`/api/ngepet/avatar/${invAvatarId}/equip`, "POST");

        if (equipRes?.status === "success") {
          // Reset semua kartu & tombol (pakai class unik supaya gak bentrok)
          document.querySelectorAll(".inv-avatar-card").forEach(invCardEl => {
            invCardEl.style.border = "2px solid #444";
            invCardEl.style.boxShadow = "0 0 8px #222";

            const invBtn = invCardEl.querySelector(".inv-equip-btn");
            if (invBtn) {
              invBtn.classList.remove("btn-secondary");
              invBtn.classList.add("btn-info");
              invBtn.removeAttribute("disabled");
              invBtn.textContent = "Gunakan";
              attachsuccessSound();
            }
          });

          // Aktifkan kartu terpilih
          const invSelectedCard = invBtnEl.closest(".inv-avatar-card");
          if (invSelectedCard) {
            invSelectedCard.style.border = `2px solid ${getTierColor(invtier)}`;
            invSelectedCard.style.boxShadow = `0 0 15px ${getTierColor(invtier)}`;
          }

          // Ubah tombol jadi Dipakai
          invBtnEl.classList.remove("btn-info");
          invBtnEl.classList.add("btn-secondary");
          invBtnEl.setAttribute("disabled", true);
          invBtnEl.textContent = "✅ Dipakai";
        } else {
          alert(result.message || "Gagal mengganti avatar.");
          btn.disabled = false;
          btn.innerHTML = "Gunakan";
        }
      } catch (error) {
        console.error(error);
        alert("Terjadi kesalahan saat mengatur avatar.");
      }

    }

    // === [TIER COLOR HELPER] ===
    function getTierColor(tier) {
      switch (tier) {
        case "common":
          return "#9e9e9e";
        case "uncommon":
          return "#4caf50";
        case "rare":
          return "#2196f3";
        case "mythical":
          return "#9c27b0";
        case "legendary":
          return "#ff9800";
        default:
          return "#999";
      }
    }

    // === [EVENT HANDLER BUTTON] ===
    document.getElementById("btnInventory").addEventListener("click", loadInventory);

    // =============== [RENDER Avatar Shop] ===============
    async function loadAvatarshop() {
      MasterTitleModal.textContent = "🎒 Avatar Shop";
      MasterContentModal.innerHTML = `
      <div class="tab-content mt-3" id="avsTabsContent">
        <div class="tab-pane fade show active" id="avs-avatars" role="tabpanel" aria-labelledby="avs-avatars-tab">
          <div id="avsAvatarList" class="d-flex flex-wrap gap-3 justify-content-start"></div>
        </div>
        <div class="tab-pane fade" id="avs-items" role="tabpanel" aria-labelledby="avs-items-tab">
          <p class="text-center text-white mt-3">🚧 Fitur item sedang dikembangkan 🚧</p>
        </div>
      </div>
    `;

      // Ambil data avatar dari API
      const avsApiRes = await fetchAPI("/api/ngepet/avatar");
      const avsListEl = document.getElementById("avsAvatarList");
      avsListEl.innerHTML = "";

      if (avsApiRes?.status === "success" && Array.isArray(avsApiRes.data) && avsApiRes.data.length > 0) {
        avsApiRes.data.forEach(avsEntry => {
          const av = avsEntry;
          const avsIssold = Number(av.stock) === 0;
          const avsOwned = Number(av.own) === 1;

          const avsCard = document.createElement("div");
          avsCard.className = "avs-avatar-card text-center p-2";
          avsCard.style = `
      width: 90%;
      max-width: 120px;
      border: 2px solid ${getTierColor(av.tier)};
      border-radius: 10px;
      transition: 0.3s ease;
    `;

          // Tentukan status tombol
          let buttonClass = "btn-info";
          let buttonText = "Beli";
          let buttonDisabled = "";

          if (avsOwned) {
            buttonClass = "btn-success";
            buttonText = "✅ Dimiliki";
            buttonDisabled = "disabled";
          } else if (avsIssold) {
            buttonClass = "btn-secondary";
            buttonText = "❌ Habis";
            buttonDisabled = "disabled";
          }

          avsCard.innerHTML = `
      <img src="/${av.image_url}" alt="${av.name}" class="img-fluid rounded mb-2" 
           style="border-radius: 8px; width:90%; max-width: 100px; height:auto;">
      <p class="fw-bold mb-1" 
         style="font-size:14px; min-height: 40px; display: flex; align-items: center; justify-content: center;">
        ${av.name}
      </p>
      <span class="badge mb-2" 
            style="background:${getTierColor(av.tier)}; display: inline-block;">
        ${av.tier}
      </span>
      <div class="mt-2" style="min-height: 32px;">
        <button id="avs-buy-btn-${av.id}" type="button"
                class="btn btn-sm w-100 ${buttonClass} avs-buy-btn"
                ${buttonDisabled}
                data-avatar-id="${av.id}">
          ${buttonText}
        </button>
      </div>
    `;

          avsListEl.appendChild(avsCard);
        });
      } else {
        avsListEl.innerHTML = `<p class="text-center w-100 mt-3">Belum ada avatar dijual</p>`;
      }

      const modalmenu = new bootstrap.Modal(document.getElementById("mysticModal"));
      modalmenu.show();
      attachHoverSoundmenu();
    }

    document.addEventListener("click", function(ev) {
      const avsTargetBtn = ev.target.closest(".avs-buy-btn");
      if (!avsTargetBtn) return;

      const avsId = avsTargetBtn.getAttribute("data-avatar-id");
      if (avsId) buyAvatarNamespaced(avsId);
    });

    // === [BUY AVATAR] ===
    async function buyAvatarNamespaced(avsAvatarId) {
      const mmresponseDiv = document.getElementById("mysticmodalResponse");
      try {
        const btn = document.getElementById(`avs-buy-btn-${avsAvatarId}`);
        btn.disabled = true;
        btn.innerHTML = "⏳ Proses";
        const buyRes = await fetchAPI(`/api/ngepet/avatar/${avsAvatarId}/buy`, "POST");
        data = buyRes;
        if (buyRes.status == "success") {
          mmresponseDiv.innerHTML = `<span class="text-success">✅ ${data.message}</span>`;
          setTimeout(() => {
            mmresponseDiv.innerHTML = "";
          }, 3000);
          attachsuccessSound();
          btn.innerHTML = "Terbeli";
        } else {
          mmresponseDiv.innerHTML = `<span class="text-danger">❌ ${data.message}</span>`;
          setTimeout(() => {
            mmresponseDiv.innerHTML = "";
          }, 3000);
          if (buyRes.status == "error" && data.message == "Kamu sudah memiliki avatar ini.") {
            btn.innerHTML = "Dimiliki";
            btn.disabled = true;
            return;
          }
          btn.disabled = false;
          btn.innerHTML = "Beli";
        }
      } catch (error) {
        console.error(error);
        mmresponseDiv.innerHTML = `<span class="text-danger">❌ ${data.message || "Terjadi kesalahan."}</span>`;
        setTimeout(() => {
          mmresponseDiv.innerHTML = "";
        }, 3000);
        btn.disabled = false;
        btn.innerHTML = "Beli";
      }
    }

    // === [EVENT HANDLER BUTTON] ===
    document.getElementById("btnAvatarshop").addEventListener("click", loadAvatarshop);

    //Leaderboard

    leaderboardModal = new bootstrap.Modal(document.getElementById("mysticModal"));

    async function loadLeaderboard(activeTab = "intruders", openModal = true) {
      console.log(document.querySelectorAll('.modal-backdrop').length);
      MasterTitleModal.textContent = "🏆 Leaderboard";
      MasterContentModal.innerHTML = `
      <div class="d-flex justify-content-around mb-3">
       <button id="tabIntruders"  class="btn-tab ${activeTab === "intruders" ? "active" : ""}"><strong>Intruders</strong></button>
       <button id="tabHost" class="btn-tab ${activeTab === "host" ? "active" : ""}"><strong>Host</strong></button>
      </div>
      <ul id="leaderboardList" class="list-group list-group-flush"></ul>
  `;

      const listEl = document.getElementById("leaderboardList");
      const apiUrl = activeTab === "intruders" ?
        "/api/ngepet/leaderboard/intruders" :
        "/api/ngepet/leaderboard/host";

      const leadApiRes = await fetchAPI(apiUrl);
      listEl.innerHTML = "";

      if (leadApiRes?.status === "success" && Array.isArray(leadApiRes.data) && leadApiRes.data.length > 0) {
        leadApiRes.data.forEach((entry, index) => {
          const rank = index + 1;
          const li = document.createElement("li");
          li.className = "list-group-item d-flex justify-content-between align-items-center";
          li.style = `
        border: none;
        border-left: 4px solid ${rank === 1 ? "gold" : rank === 2 ? "silver" : rank === 3 ? "peru" : "#9becfcff"} !important;
        background-color: ${rank === 1 ? "rgba(255, 215, 0, 0.1)" : rank === 2 ? "rgba(192, 192, 192, 0.1)" : rank === 3 ? "rgba(205, 127, 50, 0.1)" : "transparent"};
        color: ${rank === 1 ? "gold" : rank === 2 ? "silver" : rank === 3 ? "peru" : "#99eeffff"};
        font-size: 12px;
      `;

          li.innerHTML = activeTab === "intruders" ?
            `<span>${rank}. ${entry.intruder_name}</span>
           <span>${entry.total_wins} / ${entry.total_games} (${entry.win_rate}%)</span>` :
            `<span>${rank}. ${entry.host_name}</span>
           <span>${entry.total_wins} / ${entry.total_intruder_games} (${entry.winrate_percentage}%)</span>`;

          listEl.appendChild(li);
        });
      } else {
        listEl.innerHTML = `<p class="text-center w-100 mt-3">Belum ada data leaderboard</p>`;
      }

      // event listener tab
      document.getElementById("tabIntruders").addEventListener("click", () => loadLeaderboard("intruders", false));
      document.getElementById("tabHost").addEventListener("click", () => loadLeaderboard("host", false));

      // hanya show modal jika benar-benar buka pertama kali
      if (openModal) {
        leaderboardModal.show();
      }

      attachHoverSoundmenu();
    }


    document.getElementById("btnLeaderboard").addEventListener("click", () => loadLeaderboard("intruders", true));
    //History
    async function loadHistory() {
      MasterTitleModal.textContent = "📜 Riwayat Permainan";
      MasterContentModal.innerHTML = `
    <div class="modal-body modal-body-scrollable">
      <ul id="historyList" class="list-group list-group-flush"></ul>
    </div>
  `;

      const historyApiRes = await fetchAPI("/api/ngepet/match/history");
      const listEl = document.getElementById("historyList");

      listEl.innerHTML = "";

      if (historyApiRes?.status === "success" && Array.isArray(historyApiRes.data) && historyApiRes.data.length > 0) {
        historyApiRes.data.forEach((entry) => {
          const li = document.createElement("li");
          const resultColor = entry.match_result === "win" ? "text-success" : "text-danger";

          li.className = "list-group-item d-flex justify-content-between align-items-center";
          li.style = `
          border-radius: 8px;
        background-color: ${entry.match_result === "win" ? "rgba(4, 255, 0, 0.10)" :  "rgba(255, 0, 0, 0.10)"};
      `;

          li.innerHTML = `
        <a href="/games/ngepet/match/${entry.match_id}" class="d-flex justify-content-between align-items-center text-decoration-none w-100">
          <span>
            <strong>${entry.host_name} </strong> 
            <small style="color:white;">(${entry.role})</small><br>
            <small style="color:white;">${new Date(entry.created_at).toLocaleString()}</small>
          </span>
          <span class="${resultColor} fw-bold text-uppercase">${entry.match_result}</span>
        </a>
      `;

          listEl.appendChild(li);
        });
      } else {
        listEl.innerHTML = `<p class="text-center w-100 mt-3">Belum ada riwayat permainan</p>`;
      }
      const modalmenu = new bootstrap.Modal(document.getElementById("mysticModal"));
      modalmenu.show();
      attachHoverSoundmenu();
    }

    document.getElementById("btnHistory").addEventListener("click", loadHistory);

    // Fungsi Tutup Modal
    function closeMysticModal() {
      mysticModal.classList.add("hidden");
      document.body.classList.remove("overflow-hidden");
    }
    // Tutup modal jika klik area gelap
    mysticModal.addEventListener("click", function(e) {
      if (e.target === mysticModal) {
        closeMysticModal();
      }
    });

    // Tutup modal dengan tombol ESC
    document.addEventListener("keydown", function(e) {
      if (e.key === "Escape" && !mysticModal.classList.contains("hidden")) {
        closeMysticModal();
      }
    });
  </script>

  <script>
    // === [RENDER Match Found] ===
    const MMContent = document.getElementById("mysticModalMatchContent");
    async function loadmatchfound(matchId, matchRole) {
      MMContent.innerHTML = `
  <div style="text-align:center; padding:1px; color:#fff;">
  <h3 style="color:#cc9bff;">🔮  Match ditemukan</h3>
    <p style="font-size:14px; font-weight:bold; color:#ffea9e; margin:5px 0;">
      apa yang sudah dimulai, harus di akhiri!
    </p>
    <p style="margin:3px 0;">Role : <span style="color:#ffb84d;">${matchRole}</span></p>
    <div style="margin-top:15px;">
      <a href="/games/ngepet/match/${matchId}" style="
        padding:10px 18px;
        background:#b91c1c;
        color:#fff;
        border-radius:8px;
        font-weight:bold;
        text-decoration:none;
        box-shadow:0 0 10px rgba(255,0,0,0.5);
      ">
        Lanjutkan Permainan
      </a>
    </div>
  </div>
`;
      const modalmatch = new bootstrap.Modal(document.getElementById("mysticModalMatch"));
      modalmatch.show();
    }

    const token = localStorage.getItem("token");
    setTimeout(() => {
      mysticTokenAmount.innerText = localStorage.getItem("total_token") || "0";
    }, 1000);
    const matchID = localStorage.getItem("matchId");

    document.addEventListener("DOMContentLoaded", async () => {
      const modal = document.getElementById("matchModal");
      const continueBtn = document.getElementById("continueMatchBtn");
      const apiUrl = "/api/ngepet/match/active";
      if (!token) {
        mysticModalTitle.innerHTML = "<h1>Perhatian</h1>";
        mysticModalContent.innerHTML = "<h4>harap Login terlebih dahulu!</h4>";
        const modal = new bootstrap.Modal(document.getElementById("mysticModal"));
        modal.show();
        document.body.classList.add("overflow-hidden");
        setTimeout(() => {
          window.location.href = `/`;
        }, 5000);
      }

      try {
        const response = await fetch("/api/ngepet/match/active", {
          method: "GET",
          headers: {
            "Accept": "application/json",
            "Authorization": `Bearer ${token}`
          }
        });

        const result = await response.json();

        if (result.status === "success" && result.data) {
          const matchId = result.data.match_id;
          const matchRole = result.data.role;
          const currentPath = window.location.pathname;
          if (matchRole == "intruder") {
            const intrudermatchId = result.data.intruder_match_id;
            localStorage.setItem("intrudermatchId", intrudermatchId);
          }
          localStorage.setItem("matchId", matchId);
          localStorage.setItem("matchRole", matchRole);
          if (!currentPath.includes(`/games/ngepet/match/`)) {
            loadmatchfound(matchId, matchRole);
          }
        } else {
          localStorage.removeItem("matchId");
          localStorage.removeItem("matchRole");
          localStorage.removeItem("intrudermatchId");
        }
        const totalToken = result.token;
        localStorage.setItem("total_token", totalToken);
      } catch (error) {
        console.error("Gagal fetch API match aktif:", error);
      }
    });
  </script>

  <script>
    const bgm = document.getElementById("bgm");
    const btnSound = document.getElementById("btnSound");
    let isMuted = false;

    const sfx = document.getElementById("clickSound");
    const currentPath = window.location.pathname;

    btnSound.addEventListener("click", () => {
      if (currentPath.includes(`/games/ngepet/match/`)) {
        const bgmroom = document.getElementById("bgm-room");
        if (isMuted) {
          bgmroom.muted = false;
          btnSound.innerText = "🔊";
        } else {
          bgmroom.muted = true;
          btnSound.innerText = "🔇";
        }
        isMuted = !isMuted;
      } else {
        if (isMuted) {
          bgm.muted = false;
          btnSound.innerText = "🔊";
        } else {
          bgm.muted = true;
          btnSound.innerText = "🔇";
        }
        isMuted = !isMuted;
      }
    });
    // Play BGM saat halaman load
    window.addEventListener("load", () => {
      const bgm = document.getElementById("bgm");
      bgm.volume = 0.1;
      if (currentPath.includes(`/games/ngepet/match/`)) {
        bgm.pause();
        return;
      }
      bgm.play().catch(() => {
        console.log("User harus interaksi dulu sebelum audio jalan (aturan browser)");
      });
    });

    // Tambahkan event ke semua tombol
    document.querySelectorAll("button, .btn-detail, .btn-intruder, .mystic-tabs, .nav-link").forEach(btn => {
      btn.addEventListener("click", () => {
        const clickSound = document.getElementById("clickSound");
        clickSound.currentTime = 0; // reset biar bisa main ulang cepat
        clickSound.play();
      });
    });

    if (!currentPath.includes(`/games/ngepet/match/`)) {
      document.getElementById("matchList").addEventListener("click", (e) => {
        if (e.target.classList.contains("btn-detail")) {
          sfx.currentTime = 0;
          sfx.play();
        }
      });
    }

    function attachHoverSound() {
      const hoverSound = document.getElementById("hoverSound");

      document.querySelectorAll(".match-item").forEach(item => {
        item.addEventListener("mouseenter", () => {
          hoverSound.currentTime = 0; // reset agar suara bisa diputar berulang
          hoverSound.play();
        });
      });
    }

    function attachHoverSoundmenu() {
      const hoverSoundmenu = document.getElementById("hoverSound");

      document.querySelectorAll(".avs-avatar-card, .inv-avatar-card, .btn-tab, .d-flex").forEach(item => {
        item.addEventListener("mouseenter", () => {
          hoverSoundmenu.currentTime = 0; // reset agar suara bisa diputar berulang
          hoverSoundmenu.play();
        });
      });
      document.querySelectorAll(".btn-sm, .tab-btn, .d-flex").forEach(btn => {
        btn.addEventListener("click", () => {
          const clickSound = document.getElementById("clickSound");
          clickSound.currentTime = 0; // reset biar bisa main ulang cepat
          clickSound.play();
        });
      });
    }

    function attachsuccessSound() {
      const successSound = document.getElementById("successSound");
      successSound.currentTime = 0; // reset agar suara bisa diputar berulang
      successSound.play();
    }
    const btnRules = document.getElementById("btnRules");
    btnRules.addEventListener("click", () => {
      const modal = new bootstrap.Modal(document.getElementById("rulesModal"));
      modal.show();
    });
    const toggleMenuBtn = document.getElementById("toggleMenu");
    const gameMenu = document.getElementById("gameMenu");

    toggleMenuBtn.addEventListener("click", () => {
      gameMenu.classList.toggle("collapsed");
      toggleMenuBtn.textContent = gameMenu.classList.contains("collapsed") ? "⏪" : "⏩";
    });

    async function exitmatch() {
      matchlobby = null;
      const currentPath = window.location.pathname;
      if (currentPath.includes(`/games/ngepet/match/`)) {
        if (!matchID) {
          window.location.href = `/games/ngepet/`;
        } else {
          MMContent.innerHTML = `
  <div style="text-align:center; padding:15px; color:#fff;">
  <h3 style="color:#cc9bff;">Keluar dari permainan?</h3>
    <div style="margin-top:15px;margin-left:15px;margin-right:15px;gap:10px;display:flex;flex-direction:row;justify-content:center;">
          <a href="/games/ngepet/" style="
        padding:10px 18px;
        background:#b91c1c;
        color:#fff;
        border-radius:8px;
        font-weight:bold;
        text-decoration:none;      ">
        Keluar
      </a>
      <a data-bs-dismiss="modal" style="
        padding:10px 18px;
        background:rgba(30, 255, 0, 0.88);
        color:#fff;
        border-radius:8px;
        font-weight:bold;
        text-decoration:none;
        cursor:pointer;      ">
        Lanjutkan Permainan
      </a>
    </div>
  </div>
`;
          const modalmatch = new bootstrap.Modal(document.getElementById("mysticModalMatch"));
          modalmatch.show();
        }

      } else {
        if (!matchID) {
          window.location.href = `/games/`;
        } else {
          MMContent.innerHTML = `
  <div style="text-align:center; padding:15px; color:#fff;">
  <h3 style="color:#cc9bff;">Keluar dari permainan?</h3>
    <div style="margin-top:15px;margin-left:15px;margin-right:15px;gap:10px;display:flex;flex-direction:row;justify-content:center;">
          <a href="/games/" style="
        padding:10px 18px;
        background:#b91c1c;
        color:#fff;
        border-radius:8px;
        font-weight:bold;
        text-decoration:none;      ">
        Keluar
      </a>
      <a data-bs-dismiss="modal" style="
        padding:10px 18px;
        background:rgba(30, 255, 0, 0.88);
        color:#fff;
        border-radius:8px;
        font-weight:bold;
        text-decoration:none;
        cursor:pointer;      ">
        Lanjutkan Permainan
      </a>
    </div>
  </div>
`;
          const modalmatch = new bootstrap.Modal(document.getElementById("mysticModalMatch"));
          modalmatch.show();
        }

      }
    }
    document.getElementById("btnExit").addEventListener("click", exitmatch);
  </script>
</body>

</html>