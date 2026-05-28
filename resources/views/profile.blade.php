@extends('layouts.app')


@section('content')
<style>
  body {
    margin: 0;
    padding: 2rem;
    padding-top: 5rem;
    background: radial-gradient(circle at center, #0d0f1a 0%, #070709 100%);
    color: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .profile-container {
    background-color: rgba(30, 30, 48, 0.95);
    border-radius: 16px;
    padding: 3rem;
    width: 90%;
    max-width: 420px;
    box-shadow: 0 0 30px rgba(212, 175, 55, 0.3);
  }

  .profile-container h2 {
    color: #d4af37;
    text-align: center;
    margin-bottom: 2rem;
  }

  .info {
    margin-bottom: 1.25rem;
    display: flex;
    flex-direction: column;
  }

  .label {
    font-weight: bold;
    color: #d4af37;
    margin-bottom: 0.25rem;
  }

  .value {
    color: #f5f5f5;
    background-color: #1f1f2e;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    word-break: break-all;
  }

  @media (max-width: 480px) {
    .profile-container {
      padding: 1.5rem;
      width: 95%;
    }
  }

  .tab-btn-profile {
    background-color: transparent;
    border: none;
    color: #d4af37;
    font-weight: bold;
    cursor: pointer;
    padding: 0.5rem 1rem;
    border-bottom: 2px solid transparent;
    transition: border-color 0.3s;
  }

  .tab-btn-profile.active {
    border-color: #d4af37;
  }

  .staking-label {
    font-weight: bold;
    color: #d4af37;
  }

  /* Gaya dasar semua tombol Mystic */
  .btn-mystic {
    font-family: 'Cinzel', serif;
    /* gaya font klasik/mistik, pastikan sudah dimuat */
    padding: 12px 24px;
    font-size: 16px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    width: 100px;
    transition: transform 0.2s ease;
    box-shadow: 0 0 10px rgba(128, 0, 128, 0.2);
    position: relative;
    overflow: hidden;
    color: #fff;
    font-weight: bold;
  }

  /* Button Claim */
  .btn-claim {
    background: linear-gradient(135deg, #6e44ff, #b892ff);
  }

  .btn-claim:hover {
    background: linear-gradient(135deg, #b892ff, #c09bf7ff);
    box-shadow: 0 0 20px rgba(182, 146, 255, 0.6);
  }

  /* Button Cancel */
  .btn-cancel {
    background: linear-gradient(135deg, #ff4444, #8b0000);
  }

  .btn-cancel:hover {
    background: linear-gradient(135deg, #ff6b6b, #a00000);
    box-shadow: 0 0 20px rgba(255, 50, 50, 0.6);
  }


  .btn-mystic:hover {
    transform: scale(1.05);
  }

  .history-add {
  color: #00ff99;
  font-weight: bold;
}
.history-sub {
  color: #ff6666;
  font-weight: bold;
}

</style>

<div class="profile-container">

  {{-- TAB MENU --}}
  <div class="tab-menu" style="display: flex; justify-content: center; gap: 1.5rem; margin-bottom: 2rem;">
    <button class="tab-btn-profile active" onclick="switchTab('profile')">Profile</button>
    <button class="tab-btn-profile" onclick="switchTab('staking')">Staking</button>
    <button class="tab-btn-profile" onclick="switchTab('history')">History</button>
  </div>

  {{-- TAB CONTENT: PROFILE --}}
  <div id="tab-profile">
    <h2>Profil Pengguna</h2>
    <div class="info">
      <div class="label">Nama</div>
      <div class="value" id="user-name">Memuat...</div>
    </div>
    <div class="info">
      <div class="label">Email</div>
      <div class="value" id="user-email">Memuat...</div>
    </div>
    <div class="info">
      <div class="label">Wallet Address</div>
      <div class="value" id="wallet">Memuat...</div>
    </div>
    <div class="info">
      <div class="label">Total Token</div>
      <div class="value" id="token">0</div>
    </div>
    <div class="info">
      <div class="label">Token Terkunci</div>
      <div class="value" id="locked">0</div>
    </div>
    <div class="info">
      <div class="label">Status Redeem</div>
      <div class="value" id="claim">Memuat...</div>
    </div>

    <div id="exclusive-feature" style="margin-top:32px; padding:20px; border-radius:12px; background:rgba(255,255,255,0.05); backdrop-filter:blur(8px); box-shadow:0 0 20px rgba(183,127,255,0.3);">

  <h3 style="color:#b77fff; text-align:center; margin-bottom:16px;">
    🌙 Aktivasi Fitur Eksklusif Mystic Nusa
  </h3>

  <p style="text-align:center; color:#ffb347; margin-bottom:20px;">
    Buka akses auto-generate & auto-upload YouTube selama 1 tahun.
  </p>

  <!-- Countdown -->
  <div id="countdown" 
       style="text-align:center; font-size:1.4rem; font-weight:bold; color:#f4eaff; margin-bottom:20px;
              text-shadow:0 0 12px #b77fff;">
    Memuat countdown...
  </div>

  <!-- Button -->
  <div style="text-align:center;">
    <button id="activate-btn"
            style="padding:14px 28px; font-size:1rem; font-weight:bold;
                   background:linear-gradient(135deg, #b77fff, #ffb347);
                   border:none; border-radius:10px; color:#1a0033; cursor:pointer;
                   box-shadow:0 0 18px #b77fff; transition:0.3s;" disabled=true>
      🔮 Bergabung
    </button>
  </div>

</div>
</div>

  {{-- TAB CONTENT: STAKING --}}
  <div id="tab-staking" style="display: none;">
    <h2>Staking Anda</h2>
    <div id="staking-list">Memuat data staking...</div>
  </div>
  <div id="tab-history" style="display: none;">
    <h2>Token History</h2>
    <div id="token-history-list">Memuat data history...</div>

  </div>
<script>
// === TARGET DATE ===
const targetTime = new Date("Jan 1, 2026 00:00:00").getTime();

// === INITIAL DISABLE ===
const btn = document.getElementById("activate-btn");
btn.disabled = true;
btn.style.opacity = "0.4";
btn.style.cursor = "not-allowed";

function updateCountdown() {
  const now = new Date().getTime();
  const distance = targetTime - now;

  if (distance <= 0) {
    // Countdown selesai
    document.getElementById("countdown").innerHTML = "✨ Fitur Sudah Bisa Diaktifkan!";
    
    // Aktifkan tombol
    btn.disabled = false;
    btn.style.opacity = "1";
    btn.style.cursor = "pointer";
    btn.style.boxShadow = "0 0 22px #ffb347";
    btn.style.transform = "scale(1.02)";
    btn.style.transition = "0.4s";

    return;
  }

  // Hitung waktu tersisa
  const days = Math.floor(distance / (1000 * 60 * 60 * 24));
  const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((distance % (1000 * 60)) / 1000);

  document.getElementById("countdown").innerHTML =
    `${days}d : ${hours}h : ${minutes}m : ${seconds}s`;
}

setInterval(updateCountdown, 1000);
updateCountdown();
</script>


  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const token = localStorage.getItem('token');
      if (!token) {
        window.location.href = '/';
      }
      fetch('/api/profile', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        })
        .then(res => res.json())
        .then(user => {
          document.getElementById('user-name').textContent = user.name ?? '-';
          document.getElementById('user-email').textContent = user.email ?? '-';
          document.getElementById('wallet').textContent = user.wallet_address ?? '-';
          document.getElementById('token').textContent = user.total_token ?? '0';
          document.getElementById('claim').textContent = user.has_claimed ? 'Sudah' : 'Belum';
          document.getElementById('locked').textContent = user.locked_balance ?? '0';
        })
        .catch(err => {
          console.error(err);
          alert('Gagal mengambil data profil.');
        });
    });
  </script>

  <script>
    function switchTab(tab) {
      const tabBtns = document.querySelectorAll('.tab-btn-profile');
      tabBtns.forEach(btn => btn.classList.remove('active'));
      console.log('Success:', tabBtns[0]);
      console.log('Success:', tabBtns[1]);
      console.log('Success:', tabBtns[2]);
      console.log('tab:', tab);

      if (tab === 'profile') {
        document.getElementById('tab-staking').style.display = 'none';
        document.getElementById('tab-history').style.display = 'none';
        document.getElementById('tab-profile').style.display = 'block';
        tabBtns[0].classList.add('active');
      }
      if (tab === 'staking') {
        document.getElementById('tab-profile').style.display = 'none';
        document.getElementById('tab-history').style.display = 'none';
        document.getElementById('tab-staking').style.display = 'block';
        tabBtns[1].classList.add('active');
        loadStaking(); // ambil data staking saat buka tab
      } if (tab === 'history') {
        document.getElementById('tab-profile').style.display = 'none';
        document.getElementById('tab-staking').style.display = 'none';
        document.getElementById('tab-history').style.display = 'block';
        tabBtns[2].classList.add('active');
        loadTokenHistory(); // ambil data staking saat buka tab
      }
    }

    function loadStaking() {
      const token = localStorage.getItem('token');
      fetch('/api/user/stakings', {
          headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
          }
        })
        .then(res => res.json())
        .then(data => {
          const container = document.getElementById('staking-list');
          if (data.length === 0) {
            container.innerHTML = '<p>Tidak ada data staking.</p>';
            return;
          }

          const now = new Date();
          container.innerHTML = data.map(item => {
            const end = new Date(item.end_date);
            const status = item.status;
            const canClaim = (status === 'active') && (item.claimed === 0) && (end <= now);
            const canCancel = (status === 'active') && (item.claimed === 0) && (end > now);

            return `
        <div style="margin-bottom: 1rem; border-bottom: 1px solid #666; padding-bottom: 0.5rem;">
          <span class="staking-label"><strong>Jumlah:</strong></span> ${item.amount} token</br>
          <span class="staking-label"><strong>Reward:</strong></span> ${item.expected_reward}</br>
          <span class="staking-label"><strong>Start:</strong></span> ${item.start_date}</br>
          <span class="staking-label"><strong>End:</strong></span> ${item.end_date}</br>
          <span class="staking-label"><strong>Status:</strong></span> ${status}</br>
          ${canClaim ? `<button class="btn-mystic btn-claim" onclick="claimStaking(${item.id})">Claim</button>` : ''}
          ${canCancel ? `<button class="btn-mystic btn-cancel" onclick="cancelStaking(${item.id})">Cancel</button>` : ''}
        </div>
      `;
          }).join('');
        })
        .catch(() => {
          document.getElementById('staking-list').innerHTML = '<p>Gagal memuat data staking.</p>';
        });
    }

function loadTokenHistory() {
  const token = localStorage.getItem('token');

  fetch('/api/token-history', {
    headers: {
      'Authorization': 'Bearer ' + token,
      'Accept': 'application/json'
    }
  })
  .then(res => res.json())
  .then(data => {
    const container = document.getElementById('token-history-list');

    if (data.length === 0) {
      container.innerHTML = '<p>Tidak ada riwayat token.</p>';
      return;
    }

    container.innerHTML = data.map(item => {
      const typeLabel = item.action === 'add' ? '+ ' : '- ';
      const typeClass = item.action === 'add' ? 'history-add' : 'history-sub';

      return `
        <div style="margin-bottom: 1rem; border-bottom: 1px solid #666; padding-bottom: 0.5rem;">
          <span class="staking-label"><strong>Waktu:</strong></span> ${item.created_at}<br/>
          <span class="staking-label"><strong>Jenis:</strong></span> <span class="${typeClass}">${item.type}</span><br/>
          <span class="staking-label"><strong>Jumlah:</strong></span> <span class="${typeClass}">${typeLabel}${item.amount}</span><br/>
          <span class="staking-label"><strong>Keterangan:</strong></span> ${item.description}<br/>
        </div>
      `;
    }).join('');
  })
  .catch(() => {
    document.getElementById('token-history-list').innerHTML = '<p>Gagal memuat riwayat token.</p>';
  });
}


    function claimStaking(id) {
      const token = localStorage.getItem('token');
      fetch(`/api/staking/claim/${id}`, {
          method: 'POST',
          headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
          }
        }).then(res => res.json())
        .then(data => {
          alert(data.message || 'Claim berhasil');
          loadStaking();
        });
    }

    function cancelStaking(id) {
      const token = localStorage.getItem('token');
      fetch(`/api/staking/cancel/${id}`, {
          method: 'DELETE',
          headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
          }
        }).then(res => res.json())
        .then(data => {
          alert(data.message || 'Staking dibatalkan');
          loadStaking();
        });
    }
  </script>


  @endsection