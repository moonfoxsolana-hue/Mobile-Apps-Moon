@extends('layouts.app')

@section('content')
<style>
  body {
    padding: 2rem;
    padding-top: 5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
  }

  .staking-wrapper {
    position: relative;
    box-sizing: border-box;
    /* padding: 5vh 2vw; */
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
    background-size: cover;
  }

  .staking-wrapper h1 {
    font-size: clamp(1.8rem, 5vw, 2.8rem);
    text-shadow: 0 0 8px #facc15;
    color: rgb(240, 205, 65);
    margin-bottom: 1rem;
    line-height: 1.2;
    text-align: center;
  }

  .staking-buttons {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-bottom: 30px;
  }

  .staking-buttons button {
    padding: 10px 18px;
    background: #d4af37;
    color: #1a1a1a;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
    transform: scale(1);
  }

  .staking-buttons button:hover {
    background-color: #fcd555ff;
    transform: scale(1.05);
  }

  .staking-buttons button.active {
    background-color: #ffd138ff;
  }

  .staking-cards {
    position: relative;
    width: 100%;
    height: 420px;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .staking-card {
    position: absolute;
    width: 240px;
    height: 400px;
    border-radius: 20px;
    transition: all 0.5s ease;
    color: white;
    font-size: 1.1rem;
    font-weight: bold;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    opacity: 1;
    background: rgba(0, 0, 0, 0.25);
    box-shadow: 0 0 8px #4f46e5;
    backdrop-filter: blur(18px);
  }

  /* Default Position (Card 1 active) */
  .active-card1 #card1 {
    z-index: 5;
    transform: translateX(-60px) scale(1.1);
  }

  .active-card1 #card2 {
    z-index: 4;
    transform: translateX(0px) scale(1);
  }

  .active-card1 #card3 {
    z-index: 3;
    transform: translateX(60px) scale(0.9);
  }

  /* Card 2 active */
  .active-card2 #card1 {
    z-index: 4;
    transform: translateX(-60px) scale(1);
  }

  .active-card2 #card2 {
    z-index: 5;
    transform: translateX(0px) scale(1.15);
  }

  .active-card2 #card3 {
    z-index: 4;
    transform: translateX(60px) scale(1);
  }

  /* Card 3 active */
  .active-card3 #card1 {
    z-index: 3;
    transform: translateX(-60px) scale(0.9);
  }

  .active-card3 #card2 {
    z-index: 4;
    transform: translateX(0px) scale(1);
  }

  .active-card3 #card3 {
    z-index: 5;
    transform: translateX(60px) scale(1.1);
  }

  .staking-card img {
    width: 100px;
    margin: 10px auto;
    border-radius: 3rem;
    animation: glow 2s ease-in-out infinite;
  }
   @keyframes glow {

    0%,
    100% {
      filter: drop-shadow(0 0 6px #a97bffff);
    }

    50% {
      filter: drop-shadow(0 0 12px #b648ffff);
    }
  }

  .card-title {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 1rem;
  }

  .chest-image {
    width: 120px;
    height: auto;
    object-fit: contain;
    margin-bottom: 1rem;
  }

  .duration-options {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
  }

  .duration-options label {
    font-size: clamp(0.75rem, 2vw, 1.1rem);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .submit-btn {
    padding: 0.5rem 1rem;
    font-size: 1rem;
    font-weight: bold;
    background-color: #ffd138ff;
    color: #1a1a1a;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
    margin: 0.5rem 1rem;
  }

  .submit-btn:hover {
    background-color: #fcd555ff;
    color: black;
    transform: scale(1.05);
  }

  .submit-btn.disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 999;
  }

  .modal-content {
    background: #2a2a4a;
    padding: 2rem;
    border-radius: 12px;
    width: 300px;
    text-align: center;
  }

  .modal-content button {
    margin-top: 1rem;
    padding: 8px 16px;
    width: 100px;
    border: none;
    border-radius: 8px;
    background: linear-gradient(to right, #6b46c1, #9f7aea);
    color: white;
  }

  .modal-content button:hover {
    transform: scale(1.05);
    color: white;
  }


  /* --- MOBILE RESPONSIVE ANIMATED VIEW --- */
  @media (max-width: 480px) {
    .staking-cards {
      align-items: center;
    }

    .card-title {
      font-size: 1.2rem;
      font-weight: bold;
      margin-bottom: 1rem;
    }

    .active-card1 #card1 {
      z-index: 5;
      transform: translateX(0%) translateY(0px) scale(1.05);
    }

    .active-card1 #card2 {
      z-index: 4;
      transform: translateX(10%) translateY(0px) scale(1);
    }

    .active-card1 #card3 {
      z-index: 3;
      transform: translateX(20%) translateY(0px) scale(0.95);
    }

    .active-card2 #card1 {
      z-index: 4;
      transform: translateX(-10%) translateY(0px) scale(1);
    }

    .active-card2 #card2 {
      z-index: 5;
      transform: translateX(0%) translateY(0px) scale(1.05);
    }

    .active-card2 #card3 {
      z-index: 4;
      transform: translateX(10%) translateY(0px) scale(1);
    }

    .active-card3 #card1 {
      z-index: 3;
      transform: translateX(-20%) translateY(0px) scale(0.95);
    }

    .active-card3 #card2 {
      z-index: 4;
      transform: translateX(-10%) translateY(0px) scale(1);
    }

    .active-card3 #card3 {
      z-index: 5;
      transform: translateX(0%) translateY(0px) scale(1.05);
    }
  }

  .messagestaking {
    display: none;
    text-align: center;
  }

  .messagestaking.success {
    color: #00ff00ff;
  }

  .messagestaking.error {
    color: #ff5757ff;
  }

  /* *{
 outline: 1px solid red;
} */
</style>

<div class="staking-wrapper">
  <h1>Staking</h1>
  <div class="staking-buttons">
    <button onclick="setActiveCard(1)" id="btn1" class="active">10.000 Token</button>
    <button onclick="setActiveCard(2)" id="btn2">50.000 Token</button>
    <button onclick="setActiveCard(3)" id="btn3">100.000 Token</button>
  </div>

  <div class="staking-cards active-card1">
    <div class="staking-card card1 active" id="card1" data-type-id="1" data-amount="10000">
      <h2 class="card-title">Peti Kecil</h2>

      <img src="/images/chest-small.png" alt="Peti Kecil" class="chest-image" />

      <div class="duration-options">
        <label><input type="radio" name="duration" value="1"> 30 Hari</label>
        <label><input type="radio" name="duration" value="2"> 60 Hari</label>
        <label><input type="radio" name="duration" value="3"> 90 Hari</label>
      </div>

      <button class="submit-btn" onclick="openModal('card1')">Mulai Staking</button>
      <div class="messagestaking" style="font-size: clamp(0.55rem, 0.7rem, 0.7rem);"></div>
    </div>
    <div class="staking-card card2" id="card2" data-type-id="2" data-amount="50000">
      <h2 class="card-title">Peti Sedang</h2>
      <img src="/images/chest-medium.png" alt="Peti Sedang">
      <div class="duration-options">
        <label><input type="radio" name="duration" value="4"> 30 Hari</label>
        <label><input type="radio" name="duration" value="5"> 60 Hari</label>
        <label><input type="radio" name="duration" value="6"> 90 Hari</label>
      </div>
      <button class="submit-btn" onclick="openModal('card2')">Mulai Staking</button>
      <div class="messagestaking" style="font-size: clamp(0.55rem, 0.7rem, 0.7rem);"></div>
    </div>

    <div class="staking-card card3" id="card3" data-type-id="3" data-amount="100000">
      <h2 class="card-title">Peti Besar</h2>
      <img src="/images/chest-large.png" alt="Peti Besar">
      <div class="duration-options">
        <label><input type="radio" name="duration" value="7"> 30 Hari</label>
        <label><input type="radio" name="duration" value="8"> 60 Hari</label>
        <label><input type="radio" name="duration" value="9"> 90 Hari</label>
      </div>
      <button class="submit-btn" onclick="openModal('card3')">Mulai Staking</button>
      <div class="messagestaking" style="font-size: clamp(0.55rem, 0.7rem, 0.7rem);"></div>
    </div>
  </div>

</div>



<div id="confirmModal" class="modal">
  <div class="modal-content">
    <h3>Konfirmasi Staking</h3>
    <p id="confirmText"></p>
    <button class="submit-btn" onclick="confirmStake()">Ya</button>
    <button class="submit-btn" onclick="closeModal()">Batal</button>
  </div>
</div>



<script>
  document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('token');
    if (!token) {
      // 🔐 Belum login, panggil modal login
      openLoginModal();
    }
  });

  function showMessage(text, type = 'success') {
    const activeCard = document.querySelector('.staking-card.active');
    const box = activeCard.querySelector('.messagestaking'); // ambil pesan dalam card itu

    if (!box) {
      console.warn('⚠️ .messagestaking element not found!');
      return;
    }
    box.innerText = text;
    box.className = 'messagestaking ' + type;
    box.style.display = 'block';

    setTimeout(() => {
      box.style.display = 'none';
    }, 4000);
  }

  function setActiveCard(index) {
    const container = document.querySelector('.staking-cards');
    container.className = 'staking-cards'; // Reset class
    container.classList.add(`active-card${index}`);

    // Toggle class 'active' pada tombol staking
    document.querySelectorAll('.staking-buttons button').forEach((btn, i) => {
      btn.classList.toggle('active', i === index - 1);
    });

    // Tambahkan atau hapus class 'active' pada tiap staking card
    const cards = document.querySelectorAll('.staking-card');
    cards.forEach((card, i) => {
      card.classList.toggle('active', i === index - 1); // aktifkan yang dipilih
    });
  }

  function openModal(tier) {
    selectedTier = tier;
    const token = localStorage.getItem('token');
    if (!token) {
      alert('⚠️ Kamu harus login terlebih dahulu sebelum klaim.');
      openLoginModal();
      return;
    }
    const card = document.querySelector(`.staking-card.${tier}`);
    const amount = card.getAttribute('data-amount');
    const durationInput = card.querySelector('input[type="radio"]:checked');
    const msgstk = card.querySelector('.messagestaking'); // ambil pesan dalam card itu
    if (!durationInput) {
      showMessage('Silakan pilih durasi dahulu.', 'error');
      return; // hentikan fungsi di sini
    }
    msgstk.innerText = '';
    const duration = durationInput ? durationInput.parentElement.textContent.trim() : '30 Hari';

    document.getElementById('confirmText').innerText = `Staking ${amount} MYNU selama ${duration} ?`;
    document.getElementById('confirmModal').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('confirmModal').style.display = 'none';
  }

  function confirmStake() {
    // Ambil elemen card yang sedang aktif
    const activeCard = document.querySelector('.staking-card.active');
    if (!activeCard) {
      showMessage('Silakan pilih peti terlebih dahulu.', 'error');
      return;
    }

    // Ambil type_id dari atribut data (misal: data-amount atau bisa pakai data-type-id)
    const typeId = activeCard.getAttribute('data-type-id'); // atau 'data-type-id'

    // Ambil durasi yang dipilih
    const durationInput = activeCard.querySelector('input[name="duration"]:checked');
    if (!durationInput) {
      showMessage('Silakan pilih durasi staking.', 'error');
      return;
    }
    const durationId = durationInput.value;
    // Ambil tombol submit dalam card yang aktif
    const submitBtn = activeCard.querySelector('.submit-btn');
    const originalText = submitBtn.textContent;

    // Ubah teks dan nonaktifkan tombol
    submitBtn.textContent = 'Memulai Staking...';
    submitBtn.classList.add('disabled');
    submitBtn.disabled = true;
    // Kirim ke backend
    fetch('/api/staking', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify({
          type_id: typeId,
          duration_id: durationId
        })
      })
      .then(res => res.json().then(data => ({
        status: res.status,
        body: data
      })))
      .then(({
        status,
        body
      }) => {
        if (status === 200) {
          showMessage(body.message, 'success');
          submitBtn.textContent = originalText;
          submitBtn.classList.remove('disabled');
          submitBtn.disabled = false;
        } else {
          showMessage(body.error || 'Gagal staking.', 'error');
          submitBtn.textContent = originalText;
          submitBtn.classList.remove('disabled');
          submitBtn.disabled = false;
        }
      })
      .catch(() => {
        showMessage('Terjadi kesalahan.', 'error');
        submitBtn.textContent = originalText;
        submitBtn.classList.remove('disabled');
        submitBtn.disabled = false;
      });

    closeModal();
  }
</script>

@endsection