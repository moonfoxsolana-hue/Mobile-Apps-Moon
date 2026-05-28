<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mystic Nusa - Token Mistis Indonesia</title>
  <meta name="description" content="Token kripto bertema budaya dan spiritual Indonesia. Mainkan permainan Tarot dan jelajahi dunia mistis di Mystic Nusa.">
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
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <style>
    body {
      font-family: 'Calistoga', sans-serif;
      background: radial-gradient(circle at center, #0b0b0c 20%, #000000 100%);
      color: #eee;
      min-height: 100vh;
      overflow-x: hidden;
      position: relative;
    }

    @keyframes mistMove {
      from {
        background-position: 0 0;
      }

      to {
        background-position: 1000px 0;
      }
    }

    .hero-bg-wrapper {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100vh;
      background: url('/images/asset/games/background/tarot-background.jpg') no-repeat center center fixed;
      background-size: cover;
      z-index: -1;
      will-change: filter;
    }

    .game-header {
      text-align: center;
      padding: 30px 0;
      position: relative;
      z-index: 2;
      background: rgba(20, 20, 20, 0.8);
    }

    .game-header h1 {
      font-size: 2.2rem;
      color: #37bad4ff;
      text-shadow: 0 0 10px #00acb5ff;
      letter-spacing: 2px;
    }

    .game-buttons {
      display: flex;
      justify-content: center;
      gap: 16px;
      margin-top: 20px;
    }

    .mystic-btn {
      background: rgba(20, 20, 20, 0.8);
      border: 1px solid #d4aa37ff;
      color: #d4ba37ff;
      padding: 10px 20px;
      border-radius: 12px;
      font-weight: 500;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .mystic-btn:hover {
      transform: translateY(-2px);
    }

    /* Modal Styles */
    .mystic-modal {
      display: none;
      position: fixed;
      z-index: 50;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.75);
      justify-content: center;
      align-items: center;
      animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(1.2);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .mystic-modal-content {
      background: rgba(15, 15, 15, 0.95);
      border: 1px solid #d4a537ff;
      border-radius: 16px;
      padding: 24px;
      width: 90%;
      max-width: 600px;
      box-shadow: 0 0 25px rgba(55, 212, 209, 0.3);
      position: relative;
    }

    .mystic-modal h2 {
      font-family: 'Cinzel', serif;
      color: #d4af37ff;
      margin-bottom: 12px;
      text-align: center;
    }

    .close-modal {
      position: absolute;
      top: 10px;
      right: 16px;
      color: #999;
      cursor: pointer;
    }

    .close-modal:hover {
      color: #37d4d4ff;
    }

    .leaderboard-table,
    .stats-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 12px;
    }

    .leaderboard-table th,
    .leaderboard-table td,
    .stats-table th,
    .stats-table td {
      border: 1px solid rgba(212, 175, 55, 0.2);
      padding: 8px;
      text-align: center;
    }

    .leaderboard-table th,
    .stats-table th {
      background: rgba(212, 175, 55, 0.1);
      color: #d4a037ff;
      font-weight: 600;
    }

    .menu-btn {
      position: relative;
      background: rgba(20, 20, 20, 0.6);
      border-radius: 12px;
      backdrop-filter: blur(6px);
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

    .menu-container {
      position: fixed;
      bottom: 15px;
      right: 15px;
      display: flex;
      align-items: center;
      gap: 8px;
      z-index: 1000;
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

    @media (max-width: 768px) {
      .hero-bg-wrapper {
        background: url('/images/asset/games/background/tarot-background-mobile.jpg') no-repeat center center fixed;
        background-size: cover;
        filter: brightness(0.9) saturate(1.2);
      }
    }
  </style>
</head>

<body>
  <div class="hero-bg-wrapper" id="heroBackground"></div>
  <div class="game-header" style="display: none;">
    <h1>Tarot of Mystic Nusa</h1>
    <div class="game-buttons">
      <button class="mystic-btn" id="btnLeaderboard"><i class="fas fa-trophy"></i> Leaderboard</button>
      <button class="mystic-btn" id="btnStats"><i class="fas fa-user"></i> Statistik Saya</button>
    </div>
  </div>

  <main class="relative z-10">
    @yield('content')
  </main>

  {{-- Leaderboard Modal --}}
  <div id="leaderboardModal" class="mystic-modal">
    <div class="mystic-modal-content">
      <span class="close-modal" onclick="closeModal('leaderboardModal')">&times;</span>
      <h2>🏆 Leaderboard</h2>
      <table class="leaderboard-table" id="leaderboardTable">
        <thead>
          <tr>
            <th>Rank</th>
            <th>Nama</th>
            <th>Score</th>
            <th>Streak</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  {{-- Statistik Modal --}}
  <div id="statsModal" class="mystic-modal">
    <div class="mystic-modal-content">
      <span class="close-modal" onclick="closeModal('statsModal')">&times;</span>
      <h2>📊 Statistik Pribadi</h2>
      <table class="stats-table" id="statsTable">
        <thead>
          <tr>
            <th>Permainan</th>
            <th>Benar</th>
            <th>Salah</th>
            <th>Skor Tertinggi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <div id="gameMenu" class="menu-container">
    <button id="btnSound" class="menu-btn" data-tooltip="Sound">
      🔊
    </button>
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
  <div class="modal fade" id="mysticModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content" style="background:#111; color:#fff; border:2px solid #adfffeff; box-shadow: 0 0 25px #f8bbffff;">
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

        <div class="modal-footer" style="border-top:1px solid #bbf7ffff;justify-content: center;">
          <div id="mysticmodalResponse" class="w-80 text-center"></div>
        </div>
      </div>
    </div>
  </div>
  <audio id="bgm" loop>
    <source src="/sound/bgm/bgm-tarot.mp3" type="audio/mpeg">
  </audio>
  <audio id="clickSound">
    <source src="/sound/sfx/click2.wav" type="audio/mpeg">
  </audio>
  <audio id="hoverSound">
    <source src="/sound/sfx/hover3.mp3" type="audio/mpeg">
  </audio>
  <script>
    const token = localStorage.getItem('token');
    document.addEventListener("DOMContentLoaded", async () => {
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
    });
    const bgm = document.getElementById("bgm");
    let isMuted = false;
    const btnSound = document.getElementById("btnSound");
    btnSound.addEventListener("click", () => {
      if (isMuted) {
        bgm.muted = false;
        btnSound.innerText = "🔊";
      } else {
        bgm.muted = true;
        btnSound.innerText = "🔇";
      }
      isMuted = !isMuted;
    });

    function attachHoverSound() {
      const hoverSound = document.getElementById("hoverSound");

      document.querySelectorAll(".mystic-btn, .mystic-normal-btn, .tarot-element, .tarot-card").forEach(item => {
        item.addEventListener("mouseenter", () => {
          hoverSound.currentTime = 0; // reset agar suara bisa diputar berulang
          hoverSound.play();
        });
      });
    }
    window.addEventListener("load", () => {
      bgm.volume = 0.1;
      bgm.play().catch(() => {
        console.log("User harus interaksi dulu sebelum audio jalan (aturan browser)");
      });
      attachHoverSound();
    });

    document.addEventListener("click", function(e) {
      if (
        e.target.matches("button, .mystic-normal-btn,.tarot-element, .tarot-card, .close-modal")
      ) {
        const clickSound = document.getElementById("clickSound");
        if (clickSound) {
          clickSound.currentTime = 0;
          clickSound.play().catch(() => {});
        }
      }
    });

    function openModal(id) {
      const modal = document.getElementById(id);
      modal.style.display = 'flex';

      // Tutup modal saat klik di luar konten
      window.onclick = function(event) {
        if (event.target === modal) {
          closeModal(id);
        }
      };

      // Tutup modal saat tekan ESC
      document.onkeydown = function(event) {
        if (event.key === 'Escape') {
          closeModal(id);
        }
      };
    }

    function closeModal(id) {
      const modal = document.getElementById(id);
      modal.style.display = 'none';

      // Hapus listener setelah modal ditutup (agar tidak leak)
      window.onclick = null;
      document.onkeydown = null;
    }

    document.getElementById('btnLeaderboard').addEventListener('click', async () => {
      openModal('leaderboardModal');
      const res = await fetch('/api/tarot/leaderboard', {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      const data = await res.json();
      const tbody = document.querySelector('#leaderboardTable tbody');
      tbody.innerHTML = '';
      if (!data) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${data.message || 'Belum ada data leaderboard'}</td></tr>`;
        return;
      }
      data.forEach((p, i) => {
        tbody.innerHTML += `<tr>
                    <td>${i + 1}</td>
                    <td>${p.name}</td>
                    <td>${p.highest_score}</td>
                    <td>${p.highest_streak}</td>
                </tr>`;
      });
    });

    document.getElementById('btnStats').addEventListener('click', async () => {
      openModal('statsModal');

      try {
        const res = await fetch('/api/tarot/statistics', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });

        const data = await res.json();

        const tbody = document.querySelector('#statsTable tbody');
        tbody.innerHTML = '';
        if (data.status == 'error') {
          tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${data.message || 'Gagal memuat statistik'}</td></tr>`;
          return;
        }

        tbody.innerHTML = `
      <tr>
        <td>${data.total_played ?? 0}</td>
        <td>${data.total_correct ?? '-'}</td>
        <td>${data.total_wrong ?? '-'}</td>
        <td>${data.highest_score ?? '-'}</td>
      </tr>
    `;
      } catch (error) {
        console.error(error);
        const tbody = document.querySelector('#statsTable tbody');
        tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Terjadi kesalahan koneksi</td></tr>`;
      }
    });

    document.getElementById("btnExit").addEventListener("click", exitmatch);

    function exitmatch() {
      window.location.href = "/games/";
    }
  </script>
</body>

</html>