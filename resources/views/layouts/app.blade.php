<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mystic Nusa - Token Mistis Indonesia</title>
  <meta name="description" content="Token kripto bertema budaya dan spiritual Indonesia. Dapatkan airdrop, staking, dan jelajahi dunia mistis di Mystic Nusa.">
  <meta name="robots" content="index, follow">
  <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Unbounded&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      margin: 0;
      background: linear-gradient(to top, rgb(4, 15, 19), rgb(9, 24, 28), rgb(5, 17, 21));
      color: white;
      font-family: 'Unbounded', sans-serif;
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
      background: url('images/hero-bg.jpg') no-repeat center center fixed;
      background-size: cover;
      z-index: -1;
      will-change: filter;
    }

    .hero-bg-wrapper::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to top, rgba(18, 18, 28, 1) 20%, rgba(18, 18, 28, 0) 100%);
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.5s ease-out;
    }

    .hero-bg-wrapper.scroll-1::after {
      opacity: 0.2;
    }

    .hero-bg-wrapper.scroll-2::after {
      opacity: 0.4;
    }

    .hero-bg-wrapper.scroll-3::after {
      opacity: 0.6;
    }

    .hero-bg-wrapper.scroll-4::after {
      opacity: 0.8;
    }

    .hero-bg-wrapper.scroll-5::after {
      opacity: 1;
    }


    video#fogVideo {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      object-fit: cover;
      z-index: 1.5;
      opacity: 0.1;
      pointer-events: none;
      filter: brightness(2.2) contrast(2.1);
    }

    .mystic-menu {
      position: fixed;
      top: 20px;
      right: 20px;
      background: rgba(0, 0, 0, 0.5);
      border: 2px solid rgba(255, 255, 255, 0.2);
      border-radius: 16px;
      backdrop-filter: blur(3px);
      padding: 10px 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
      z-index: 10;
    }

    .mystic-menu ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      gap: 50px;
    }

    .mystic-menu ul li a {
      font-size: 1.1rem;
      color: #fff;
      text-decoration: none;
      font-weight: 700;
      letter-spacing: 0.5px;
      transition: all 0.3s ease-in-out;
    }

    .mystic-menu ul li a:hover {
      color: #ffd700;
      text-shadow: 0 0 10px rgba(255, 215, 0, 0.8);
    }

    .menu-toggle {
      display: none;
      background: transparent;
      border: none;
      color: white;
      font-size: 1.8rem;
      cursor: pointer;
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 20;
    }

    @media (max-width: 860px) {
      .menu-toggle {
        display: block;
      }

      .mystic-menu {
        position: fixed;
        top: 20px;
        right: 20px;
        width: 100px;
        background: rgba(0, 0, 0, 0.6);
        border: 2px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        backdrop-filter: blur(3px);
        z-index: 100;
        padding-top: 60px;
        /* Space for centered button */
        text-align: center;
      }

      /* Tombol toggle ditaruh di tengah atas menu */
      .menu-toggle {
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        background: none;
        border: none;
        color: white;
        font-size: 1.8rem;
        cursor: pointer;
        z-index: 101;
      }

      /* Nav list */
      #mystic-nav-links {
        display: none;
        flex-direction: column;
        gap: 12px;
        padding: 0;
        margin: 0;
        list-style: none;
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity 0.4s ease, transform 0.4s ease;
      }

      /* Muncul dengan animasi */
      #mystic-nav-links.menu-open {
        display: flex;
        opacity: 1;
        transform: translateY(0);
      }

      #mystic-nav-links li a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        font-size: 1.1rem;
        padding: 5px 0;
        display: inline-block;
        transition: color 0.2s;
      }

      #mystic-nav-links li a:hover {
        color: #ffd700;
      }

    }

    @media (max-width: 480px) {
      .menu-toggle {
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 1.2rem;
      }

      .mystic-menu {
        width: 60px;
        padding-top: 40px;
      }

      #mystic-nav-links {
        display: none;
        flex-direction: column;
        gap: 5px;
      }

      #mystic-nav-links li a {
        font-size: 0.8rem;
      }
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

    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.7);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    /* Modal background */
    .modal-bg {
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(8px);
    }

    /* Modal container */
    .modal-login {
      background: #1e1e2f;
      border-radius: 10px 10px 10px 10px;
      padding: 2rem;
      box-shadow: 0 0 20px rgba(129, 140, 248, 0.5);
      width: 100%;
      max-width: 300px;
      position: relative;
      animation: fadeIn 0.5s ease-out;
    }

    /* Logo atau simbol atas */
    .modal-login .logo {
      width: 70px;
      margin: 0 auto 1.5rem;
      display: block;
    }

    /* Input field */
    .modal-login input[type="text"],
    .modal-login input[type="email"],
    .modal-login input[type="password"] {
      background-color: rgba(250, 240, 137, 0.08);
      border: 1px solid #718096;
      color: #fefcbf;
      width: 100%;
      padding: 12px 15px;
      border-radius: 10px;
      margin-bottom: 1rem;
      transition: 0.3s ease;
      box-sizing: border-box;
      max-width: 100%;
    }

    .modal-login input:focus {
      border-color: #9f7aea;
      outline: none;
      box-shadow: 0 0 5px #9f7aea;
    }

    .modal-login .tab-buttons {
      display: flex;
      justify-content: center;
      margin-bottom: 1.5rem;
      gap: 10px;
      border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }

    .modal-login .tab-btn {
      background: none;
      border: none;
      padding: 10px 20px;
      font-weight: bold;
      font-size: 1rem;
      color: #bbb;
      cursor: pointer;
      position: relative;
      transition: color 0.3s ease;
    }

    .modal-login .tab-btn::after {
      content: "";
      position: absolute;
      bottom: -4px;
      left: 50%;
      transform: translateX(-50%);
      width: 0;
      height: 3px;
      background: linear-gradient(to right, #6b46c1, #9f7aea);
      border-radius: 2px;
      transition: width 0.3s ease;
    }

    .modal-login .tab-btn.active {
      color: #fff;
    }

    .modal-login .tab-btn.active::after {
      width: 100%;
    }

    /* Tombol login */
    .modal-login button {
      background: linear-gradient(to right, #6b46c1, #9f7aea);
      color: white;
      font-weight: bold;
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-login button:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .modal-login .spinner {
      border: 3px solid rgba(255, 255, 255, 0.2);
      border-top: 3px solid #fff;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      animation: spin 1s linear infinite;
      margin-right: 10px;
      display: none;
    }

    .modal-login .spinner-reg {
      border: 3px solid rgba(255, 255, 255, 0.2);
      border-top: 3px solid #fff;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      animation: spin 1s linear infinite;
      margin-right: 10px;
      display: none;
    }

    .modal-login .btn-text {
      display: inline-block;
    }

    .modal-login #message {
      margin-top: 1rem;
      font-size: 12px;
      text-align: center;
    }

    .modal-login h2 {
      margin-top: 1rem;
      font-size: 24px;
      text-align: center;
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

    .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      cursor: pointer;
      font-size: 1.2rem;
      color: #999;
    }

    .opt-button {
      position: absolute;
      bottom: 10px;
      right: 10%;
      cursor: pointer;
      font-size: 1rem;
    }

    .modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(8px);
      justify-content: center;
      align-items: center;
      z-index: 999;
      padding: 1rem;
    }

    .modal-content {
      background: #111827;
      padding: 1.5rem;
      border-radius: 14px;
      width: 100%;
      max-width: 600px;
      /* 🔹 Ukuran lebih ramping */
      box-shadow: 0 0 30px #60a5fa33;
      position: relative;
      color: #fff;
      overflow-y: auto;
      max-height: 90vh;
    }

    .close-btn {
      position: absolute;
      top: 10px;
      right: 12px;
      background: transparent;
      border: none;
      font-size: 1.2rem;
      color: #ccc;
      cursor: pointer;
    }

    .token-table {
      width: 100%;
      margin-top: 1rem;
      border-collapse: collapse;
      font-size: 0.9rem;
    }

    .token-table th,
    .token-table td {
      padding: 8px 10px;
      border-bottom: 1px solid #333;
      text-align: left;
    }

    .token-table th {
      background: #1e293b;
      color: #e5e7eb;
    }

    .desktop-label {
      display: inline;
    }

    .mobile-label {
      display: none;
    }

    @media (max-width: 480px) {
      .modal-content {
        padding: 1rem;
        font-size: 0.85rem;
      }

      .token-table th,
      .token-table td {
        padding: 6px 8px;
        font-size: 0.8rem;
      }

      canvas {
        max-width: 100% !important;
      }

      .desktop-label {
        display: none;
      }

      .mobile-label {
        display: inline;
      }
    }

    .pie-wrapper {
      max-width: 300px;
      margin: 0 auto;
    }
  </style>
</head>

<body>
  <video autoplay muted loop id="fogVideo">
    <source src="{{ asset('videos/fog.webm') }}" type="video/webm">
  </video>

  <div class="hero-bg-wrapper" id="heroBackground"></div>
  <nav class="mystic-menu">
    <!-- Toggle SELALU tampil -->
    <button class="menu-toggle" onclick="toggleMenu()">☰</button>

    <!-- Bagian yang ditampilkan/dihilangkan -->
    <ul id="mystic-nav-links">
      <li><a href="/">Home</a></li>
      <li><a href="/news">News</a></li>
      <li>
        <a href="/realm">
          <span class="desktop-label">Mystic Realm</span>
          <span class="mobile-label">Realm</span>
        </a>
      </li> <!-- <li><a href="/cerita">Story</a></li>
      <li><a href="/airdrop">Airdrop</a></li>
      <li><a href="/staking">Staking</a></li>
      <li><a href="/games">Games</a></li> -->
      <li id="guest-menu" style="display: none;" onclick="openLoginModal()"><a href="#">Login</a></li>
      <li id="user-menu" style="display: none;"><a href="/profile">Profile</a></li>
      <li id="user-menu-logout" style="display: none;"><a href="#" onclick="logout()">Logout</a></li>
    </ul>
  </nav>

  <!-- Modal Login -->
  <div id="loginModal" class="modal-overlay" style="display:none;">
    <div class="modal-bg">
      <div class="modal-login">
        <span class="close-btn" onclick="closeLoginModal()">✖</span>
        <img src="/images/logo-mystical.png" class="logo" />
        <div class="tab-buttons">
          <button class="tab-btn active" onclick="openLoginModal()">Login</button>
          <button class="tab-btn" onclick="openRegModal()">Register</button>
        </div>
        <form id="loginForm">
          <input type="email" id="email" placeholder="Email" required />
          <input type="password" id="password" placeholder="Password" required />
          <button id="btn">
            <span class="spinner"></span>
            <span class="btn-text">Login</span>
          </button>
          <div id="message"></div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal Register -->
  <div id="regModal" class="modal-overlay" style="display:none;">
    <div class="modal-bg">
      <div class="modal-login">
        <span class="close-btn" onclick="closeLoginModal()">✖</span>
        <img src="/images/logo-mystical.png" class="logo" />
        <div class="tab-buttons">
          <button class="tab-btn" onclick="openLoginModal()">Login</button>
          <button class="tab-btn active" onclick="openRegModal()">Register</button>
        </div>
        <form id="registerForm">
          <input type="text" id="name" placeholder="Nama" required>
          <input type="email" id="reg_email" placeholder="Email" required>
          <input type="password" id="reg_password" placeholder="Password" required>
          <input type="password" id="password_confirmation" placeholder="Ulangi Password" required>
          <button id="btn-reg">
            <span class="spinner-reg"></span>
            <span class="btn-text-reg">Daftar</span>
          </button>
          <div id="message-reg"></div>
        </form>
      </div>
    </div>
  </div>
  @yield('content')
  <script>
    function toggleMenu() {
      const navList = document.getElementById('mystic-nav-links');
      navList.classList.toggle('menu-open');
    }

    const token = localStorage.getItem('token');

    if (token) {
      // Verifikasi token masih valid (opsional: bisa fetch ke /api/user)
      document.getElementById('user-menu').style.display = 'block';
      document.getElementById('user-menu-logout').style.display = 'block';
    } else {
      document.getElementById('guest-menu').style.display = 'block';
    }

    // function logout() {
    //   localStorage.removeItem('token');
    //   window.location.href = '/'; 
    // }

    async function logout() {
      try {
        const token = localStorage.getItem('token');

        const response = await fetch('/api/logout', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        });

        if (response.ok) {
          localStorage.removeItem('token');
          window.location.href = '/';
        } else {
          const data = await response.json();
          console.error('Logout gagal:', data.message);
        }
      } catch (error) {
        console.error('Terjadi kesalahan:', error);
      }
    }
  </script>
  <script>
    function openLoginModal() {
      document.getElementById('regModal').style.display = 'none';
      document.getElementById('loginModal').style.display = 'flex';
    }

    function closeLoginModal() {
      document.getElementById('loginModal').style.display = 'none';
      document.getElementById('regModal').style.display = 'none';
    }
    const form = document.getElementById('loginForm');
    const msg = document.getElementById('message');
    const btn = document.getElementById('btn');
    const spinner = btn.querySelector('.spinner');
    const btnText = btn.querySelector('.btn-text');

    form.addEventListener('submit', async function(e) {
      e.preventDefault();

      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;

      // 🔒 Tampilkan loading
      btn.disabled = true;
      btnText.textContent = 'Sedang Verifikasi...';
      spinner.style.display = 'inline-block';

      try {
        const response = await fetch('/api/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            email,
            password
          })
        });

        const result = await response.json();

        if (response.ok) {
          msg.innerHTML = `<span style="color:green;">✅ ${result.message}</span>`;
          localStorage.setItem('token', result.access_token);
          setTimeout(() => window.location.href = '/', 1000); // Redirect setelah sukses
        } else {
          msg.innerHTML = `<span style="color:red;">❌ ${result.message || Object.values(result.errors).join('<br>')}</span>`;
          resetButton();
        }
      } catch (error) {
        msg.innerHTML = `<span style="color:red;">⚠️ Terjadi kesalahan: ${error.message}</span>`;
        resetButton();
      }
    });

    function openRegModal() {
      document.getElementById('loginModal').style.display = 'none';
      document.getElementById('regModal').style.display = 'flex';
    }

    function resetButton() {
      btn.disabled = false;
      btnText.textContent = 'Login';
      spinner.style.display = 'none';
    }
  </script>
  <script>
    const formr = document.getElementById('registerForm');
    const msgr = document.getElementById('message-reg');
    const btnreg = document.getElementById('btn-reg');
    const spinnerreg = btnreg.querySelector('.spinner-reg');
    const btnTextreg = btnreg.querySelector('.btn-text-reg');
    formr.addEventListener('submit', async function(e) {
      e.preventDefault();

      const name = document.getElementById('name').value;
      const email = document.getElementById('reg_email').value;
      const password = document.getElementById('reg_password').value;
      const password_confirmation = document.getElementById('password_confirmation').value;
      btnreg.disabled = true;
      btnTextreg.textContent = 'Sedang Verifikasi...';
      spinnerreg.style.display = 'inline-block';
      try {
        const response = await fetch('api/register', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            name,
            email,
            password,
            password_confirmation
          })
        });

        const result = await response.json();

        if (response.ok) {
          msgr.innerHTML = `<span style="color:green;">✅ ${result.message}</span>`;
          localStorage.setItem('token', result.access_token); // simpan token
          setTimeout(() => window.location.href = '/', 1000); // Redirect setelah sukses
        } else {
          msgr.innerHTML = `<span style="color:red;">❌ ${result.message || Object.values(result.errors).join('<br>')}</span>`;
          resetButtonReg();
        }
      } catch (error) {
        msgr.innerHTML = `<span style="color:red;">Terjadi kesalahan: ${error.message}</span>`;
        resetButtonReg();
      }
    });

    function resetButtonReg() {
      btnreg.disabled = false;
      btnTextreg.textContent = 'Register';
      spinnerreg.style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
      const hero = document.querySelector('.hero-bg-wrapper');

      window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;

        // Hapus semua kelas scroll
        for (let i = 1; i <= 5; i++) {
          hero.classList.remove('scroll-' + i);
        }

        // Tambahkan kelas sesuai tingkat scroll
        if (scrollY > 8000) {
          hero.classList.add('scroll-5');
        } else if (scrollY > 6000) {
          hero.classList.add('scroll-4');
        } else if (scrollY > 4000) {
          hero.classList.add('scroll-3');
        } else if (scrollY > 2000) {
          hero.classList.add('scroll-2');
        } else if (scrollY > 1000) {
          hero.classList.add('scroll-1');
        }
        // Jika < 300, tidak ada class = opacity 0
      });
    });
  </script>

</body>

</html>