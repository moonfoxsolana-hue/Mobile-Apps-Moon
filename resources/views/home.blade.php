@extends('layouts.app')

@section('content')
<style>
  body,
  html {
    margin: 0;
    padding: 0;
    scroll-behavior: smooth;
    height: 100%;
    scroll-padding-top: 0;
  }

  .section {
    transition: opacity 0.5s ease;
    padding: 2rem 1rem;
    text-align: center;
    box-sizing: border-box;
  }

  @media (max-width: 768px) {
    section {
      padding: 1.5rem 1rem;
    }
  }

  @media (max-height: 500px) {
    .section {
      height: auto;
    }
  }
    @media (max-width: 480px) {
        .section {
            padding: 0rem;
        }
    }
  .mystic-hero {
    position: relative;
    overflow: hidden;
    padding: 1rem 1rem;
    text-align: center;
  }

  .mystic-hero h1 {
    font-size: clamp(1.8rem, 5vw, 2.8rem);
    text-shadow: 0 0 8px #facc15;
    color: rgb(240, 205, 65);
    margin-bottom: 1rem;
    line-height: 1.2;
    text-align: center;
  }

  .mystic-hero h2 {
    font-size: clamp(1.2rem, 3.5vw, 1.6rem);
    text-shadow: 0 0 10px #a278ff;
    margin-bottom: 1rem;
    font-weight: 400;
  }

  .mystic-hero p {
    font-size: clamp(0.95rem, 2vw, 1.1rem);
    margin-top: 1.2rem;
    color: rgb(242, 234, 255);
    max-width: 70%;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
  }

  .mystic-button,
  .mystic-link {
    display: inline-block;
    padding: clamp(0.6rem, 1.5vw, 1rem) clamp(1.2rem, 3vw, 2rem);
    font-size: clamp(0.9rem, 1.2vw, 1.1rem);
    border: none;
    border-radius: 25px;
    background: linear-gradient(to top, rgb(255, 230, 7), rgb(229, 255, 0));
    color: black;
    text-decoration: none;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 0 10px #ffd700;
    transition: all 0.3s ease;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s, opacity 0.5s ease;
  }

  .mystic-button:hover,
  .mystic-link:hover {
    transform: scale(1.2);
  }

  .mystic-button:active,
  .mystic-link:active {
    transform: scale(0.98);
  }

  .mystic-list {
    list-style: none;
    padding: 0;
    margin: 2rem auto;
    max-width: 1000px;
    text-align: center;
  }

  .mystic-list li {
    margin-bottom: 1rem;
    padding: 0rem 0rem;
    border-radius: 12px;
    color: #fef9c3;
    font-size: clamp(0.95rem, 2vw, 1.1rem);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .mystic-list li:hover {
    transform: scale(1.05);
  }

  .mystic-tokenomics-table {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    border-collapse: collapse;
    background: #1e293b;
    border-radius: 8px;
    overflow: hidden;
    font-size: 1rem;
  }

  /* Header */
  .mystic-tokenomics-table thead tr {
    background: #374151;
  }

  .mystic-tokenomics-table th,
  .mystic-tokenomics-table td {
    padding: 8px;
    text-align: left;
    color: white;
  }

  /* Specific column */
  .mystic-tokenomics-table th {
    color: #fbbf24;
  }

  /* Border row separation */
  .mystic-tokenomics-table td {
    border-top: 1px solid #334155;
  }

  /* Tambahan untuk layar sangat lebar (desktop 2K ke atas) */
  @media (min-width: 1440px) {
    .mystic-hero h1 {
      font-size: 2.4rem;
    }

    .mystic-hero h2 {
      font-size: 1.4rem;
    }

    .mystic-hero p {
      font-size: 1rem;
    }
  }

  /* Tambahan untuk layar sangat kecil (HP kecil) */
  @media (max-width: 500px) {
    section {
      padding: 0.5rem 1rem;
      min-height: unset;
    }

    .hero-section {
      padding: 3rem 1rem !important;
      /* kurangi padding atas & bawah */
    }

    .mystic-hero h1 {
      font-size: 1.2rem;
      line-height: 1.2;
    }

    .mystic-hero h2 {
      font-size: 1rem;
    }

    .mystic-hero p {
      font-size: 0.75rem;
      max-width: 100%;
    }

    .mystic-list li {
      font-size: 0.75rem;
      padding: 0rem 0rem;
    }

    .mystic-tokenomics-table {
      font-size: 0.75rem;
      max-width: 100%;
    }

    .mystic-tokenomics-table th,
    .mystic-tokenomics-table td {
      padding: 5px;
    }

    .token-info p {
      font-size: 0.9rem;
      line-height: 1.4;
      word-break: break-word;
      padding: 0.3rem 0;
    }

    .mystic-button,
    .mystic-link {
      font-size: 0.65rem;
    }

    .mystic-button:hover,
    .mystic-link:hover {
      transform: scale(1.1);
    }
  }

  .floating-crystal {
    position: absolute;
    top: 20%;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    animation: floaty 6s ease-in-out infinite;
    opacity: 0.8;
    z-index: -1.5;
  }

  .koin-mynu {
    position: absolute;
    top: 105%;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    animation: floaty 3s ease-in-out infinite;
    z-index: -0.5;
  }

  .token-image {
    width: clamp(80px, 20vw, 120px);
    height: auto;
    margin: 2rem auto;
    animation: glow 2s ease-in-out infinite;
    border-radius: 16px;
    transition: filter 0.3s ease, transform 0.3s ease;

  }

  .token-image:hover {
    transform: scale(1.2);
    background: #a88aff;
    box-shadow: 0 0 10px #c8afff;
    filter: drop-shadow(0 0 15px rgba(255, 255, 150, 0.8)) drop-shadow(0 0 30px rgba(255, 200, 100, 0.6));
  }

  @keyframes glow {

    0%,
    100% {
      filter: drop-shadow(0 0 6px #d6baff);
    }

    50% {
      filter: drop-shadow(0 0 12px #a976ff);
    }
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

  .card-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
  }

  .card {
    background: rgba(0, 0, 0, 0.25);
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    aspect-ratio: 4 / 3;
    display: flex;
    flex-direction: column;
    justify-content: center;
    box-shadow: 0 0 8px #4f46e5;
    backdrop-filter: blur(8px);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    min-width: 260px;
    min-height: 200px;
    max-height: 220px;
  }

  .card:hover {
    transform: translateY(-6px);
    box-shadow: 0 0 16px #7c3aed;
  }

  /* Font responsif */
  .card h3 {
    color: #facc15;
    margin-bottom: 0.6rem;
    font-size: clamp(0.95rem, 1.5vw, 1.2rem);
    line-height: 1.3;
  }

  .card p {
    font-size: clamp(0.7rem, 1.1vw, 0.95rem);
    color: #ddd;
    line-height: 1.5;
  }

  .card ul {
    list-style: disc;
    /* atau gunakan emoji jika kamu pakai custom icon */
    list-style-position: inside;
    /* atau gunakan 'outside' jika ingin bullet di luar padding */
    padding-left: 1rem;
    /* sesuaikan, bisa 1.5rem jika perlu lebih */
    text-align: left;
    margin: 0;
  }

  .card li {
    padding: 1px 0;
    font-size: clamp(0.7rem, 1.1vw, 0.95rem);
    text-align: left;
  }

  /* Tablet dan bawah: 2 kolom */
  @media (max-width: 1024px) {
    .card-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 1.2rem;
    }

    .card ul {
      list-style: disc;
      /* atau gunakan emoji jika kamu pakai custom icon */
      list-style-position: inside;
      /* atau gunakan 'outside' jika ingin bullet di luar padding */
      padding-left: 1rem;
      /* sesuaikan, bisa 1.5rem jika perlu lebih */
      text-align: left;
      margin: 0;
    }

    .card li {
      padding: 1px 0;
      font-size: clamp(0.7rem, 1.1vw, 0.95rem);
      text-align: left;
    }
  }

  /* Mobile kecil < 480px */
  @media (max-width: 480px) {
    .card-grid {
      grid-template-columns: repeat(2, 1fr);
      padding: 1rem 0.5rem;
      gap: 1rem;
    }

    .card {
      padding: 0.1rem;
      aspect-ratio: 4 / 3;
      min-width: 160px;
      min-height: 100px;
    }

    .card h3 {
      padding-top: 0rem;
      padding-bottom: 0rem;
      font-size: 0.55rem;
    }

    .card p {
      font-size: 0.6rem;
    }

    .card ul {
      list-style: disc;
      /* atau gunakan emoji jika kamu pakai custom icon */
      list-style-position: inside;
      /* atau gunakan 'outside' jika ingin bullet di luar padding */
      padding-left: 0.5rem;
      /* sesuaikan, bisa 1.5rem jika perlu lebih */
      text-align: left;
      margin: 0;
    }

    .card li {
      padding: 0px 0;
      font-size: 0.5rem;
      text-align: left;
    }
  }

  .utility-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
  }

  .utility-card {
    backdrop-filter: blur(10px);
    padding: 1.5rem;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 0 8px #4f46e5;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: rgba(0, 0, 0, 0.25);
  }

  .utility-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 0 20px #7c3aed;
  }

  .utility-card h3 {
    color: #facc15;
    margin-bottom: 1rem;
    font-size: 1.3rem;
  }

  .utility-card p {
    font-size: 1rem;
    color: #ddd;
    line-height: 1.5;
  }

  .glowtitle {
    font-size: 3rem;
    font-weight: bold;
    text-shadow: 0 0 10px #fff, 0 0 20px #ffd700;
    margin-top: 100px;
    opacity: 1;
    transition: opacity 0.5s ease;
    color: rgb(255, 234, 45);
    z-index: 1;
  }

  .glow {
    font-size: 1.2rem;
    max-width: 700px;
    margin: 20px auto;
    text-shadow: 0 0 5px #ccc;
    opacity: 1;
    transition: opacity 0.5s ease;
  }

  .tokenomics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    max-width: 1000px;
    margin: auto;
  }

  .box {
    background: #222;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #333;
  }

  /* Fade effect on scroll */
  .faded {
    opacity: 0 !important;
  }

  footer {
    text-align: center;
    font-size: 0.9rem;
    padding: 40px 20px;
    background: #111;
    color: #aaa;
  }

  .container-mystic {
    max-width: 900px;
    margin: 0 auto;
    padding: 60px 20px;
    text-align: center;
  }

  h1 {
    font-size: 3rem;
    margin-bottom: 30px;
    color: #b88aff;
    text-shadow: 0 0 10px #b88aff;
  }

  .tokenomics-table {
    width: 100%;
    border-collapse: collapse;
    background-color: rgba(255, 255, 255, 0.05);
    box-shadow: 0 0 20px rgba(184, 138, 255, 0.2);
    border-radius: 12px;
    overflow: hidden;
    table-layout: fixed;
    word-wrap: break-word;
  }

  .tokenomics-table th,
  .tokenomics-table td {
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
  }

  .tokenomics-table tr:last-child td {
    border-bottom: none;
  }

  @media (max-width: 768px) {

    .tokenomics-table th,
    .tokenomics-table td {
      font-size: 0.9rem;
      padding: 12px;
    }
  }

  .emoji {
    font-size: 1.3rem;
  }

  .footer-note {
    margin-top: 40px;
    font-size: 0.9rem;
    color: #aaa;
    font-style: italic;
  }

  @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@500&display=swap');


  .roadmap-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
  }

  .roadmap-card {
    background: rgba(255, 255, 255, 0.05);
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 0 10px #6b21a8;
    transform: translateY(30px);
    opacity: 0;
    animation: fadeUp 0.8s ease forwards;
  }

  .roadmap-card:nth-child(1) {
    animation-delay: 0.1s;
  }

  .roadmap-card:nth-child(2) {
    animation-delay: 0.3s;
  }

  .roadmap-card:nth-child(3) {
    animation-delay: 0.5s;
  }

  .roadmap-card:nth-child(4) {
    animation-delay: 0.7s;
  }

  .roadmap-card h3 {
    color: #facc15;
    margin-bottom: 1rem;
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



  .fade-section {
    opacity: 0;
    transform: translateX(-30px);
    transition: opacity 1s ease, transform 1s ease;
    will-change: opacity, transform;
  }

  .fade-section.visible {
    opacity: 1;
    transform: translateX(0);
  }

  footer#mystic-footer {
    background-color: #0e0e1a;
    color: #ccc;
    text-align: center;
    padding: 2rem 1rem;
    opacity: 0;
    transition: opacity 1.2s ease;
  }

  footer#mystic-footer.visible {
    opacity: 1;
  }

  .faq-container {
    max-width: 700px;
    margin: 0 auto;
  }

  .faq-item {
    background: rgba(255, 255, 255, 0.05);
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .faq-item h3 {
    margin: 0;
    color: #facc15;
    font-size: clamp(0.7rem, 1vw, 0.85rem);
  }

  .faq-item p {
    font-size: clamp(0.5rem, 0.9vw, 0.8rem);
    margin: 0;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
  }

  .faq-item.open p {
    margin-top: 0.5rem;
    max-height: 200px;
  }

  .unlock-item {
    margin-bottom: 1.5rem;
  }

  .unlock-label {
    display: flex;
    /* justify-content: center; */
    align-items: center;
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 0.3rem;
    color: #d0e4ff;
  }

  .unlock-bar {
    height: 18px;
    background: linear-gradient(90deg, #2b5876, #4e4376);
    border-radius: 8px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 0 8px rgba(113, 173, 255, 0.3);
  }

  .unlock-fill {
    height: 100%;
    background: linear-gradient(90deg, #89f7fe, #66a6ff);
    box-shadow: 0 0 10px rgba(89, 246, 255, 0.5);
    animation: mist-glow 3s ease-in-out infinite alternate;
  }

  @keyframes mist-glow {
    0% {
      filter: brightness(1);
    }

    100% {
      filter: brightness(1.4);
    }
  }

  .unlock-percent {
    font-size: 0.85rem;
    color: #a0bfe9;
    margin-top: 4px;
  }

  @media (max-width: 640px) {
    .timeline-container {
      padding: 1.5rem 1rem;
    }

    .unlock-label {
      font-size: 0.9rem;
    }

    .unlock-percent {
      font-size: 0.8rem;
    }
  }

  .scroll-wrapper {
    overflow-x: auto;
    overflow-y: hidden;
    width: 100%;
    padding-bottom: 4px;
    scrollbar-width: thin;
    scrollbar-color: rgb(140, 192, 255) transparent;
    /* Firefox */
  }

  .scroll-wrapper::-webkit-scrollbar {
    height: 6px;
  }

  .scroll-wrapper::-webkit-scrollbar-track {
    background: transparent;
  }

  .scroll-wrapper::-webkit-scrollbar-thumb {
    background-color: #d4af37;
    border-radius: 10px;
    border: 2px solid transparent;
    background-clip: content-box;
  }

  .scroll-content {
    min-width: 800px;
    /* atur sesuai lebar chart */
    margin: 0 auto;
  }
   .btn-social {
      background: #1e1e2f;
      color: #fff;
      padding: 0.75rem 1.5rem;
      border-radius: 10px;
      transition: 0.3s;
      text-decoration: none;
      font-size: 1rem;
    }

    .btn-social:hover {
      background: #5ddcff;
      color: #000;
    }

    @media (max-width: 500px) {
      #komunitas h2 {
        font-size: 1.6rem;
      }

      #komunitas p {
        font-size: 0.95rem;
      }

      .btn-social {
        font-size: 0.9rem;
        padding: 0.6rem 1rem;
      }
    }
a {
  color: white;
  text-decoration: none; /* opsional jika ingin menghilangkan garis bawah */
}

a:hover {
  color: #1199f9ff; /* opsional warna saat hover, misalnya kuning keemasan */
}
</style>
<div class="section" id="section1">
  <section>
    <div class="mystic-hero">
      <img src="{{ asset('images/logo-mystic-nusa.webp') }}" alt="Mystic Nusa Logo" class="token-image">
      <h1>Mystic Nusa â€“ $MYNU</h1>
      <h2>"Legenda Desa Mistis â€” Antara Kekayaan dan Kutukan."</h2>
      <p>
        Mystic Nusa bukan hanya token â€” ini adalah awal perjalanan menuju Legenda Desa Mistis.
        Sebuah tempat yang dijaga oleh roh para leluhur, tempat para penjelajah mempertaruhkan jiwa demi kekayaan abadi. Token $MYNU adalah kunci â€” hanya yang terpilih yang bisa membuka gerbangnya.
      </p>
      <p style="margin-top: 1rem; font-style: italic;">
        â€œHanya yang terpilih yang bisa membuka gerbang kekayaan abadi.â€
      </p>
      <a class="mystic-link" id="cta-button" href="/airdrop">Klaim Airdrop</a>
    </div>
  </section>
</div>
<div class="section" id="section2">
  <section>
    <div class="mystic-hero">
      <h1>Apa itu Mystic Nusa?</h1>
      <p>
        <em>Mystic Nusa</em> adalah Proyek mystical meme token pertama dari Indonesia yang terinspirasi dari kekayaan budaya mistik Nusantara dengan token $MYNU sebagai inti ekosistem, kami menciptakan dunia interaktif yang menggabungkan <strong>cerita rakyat</strong>, <strong>ritual digital</strong>, dan <strong>gamefi</strong>.
      </p>
      <ul class="mystic-list">
        <li>âœ… 100% komunitas, tanpa VC, tanpa presale</li>
        <li>âœ… Utility : Trading, Staking, GameFi, Akses eksklusif, DAO, Swap merchandise.</li>
        <li>âœ… Berdiri di atas kepercayaan komunitas, bukan spekulasi semata</li>
      </ul>
      <h1>Kenapa unik?</h1>
      <p>
        Perpaduan antara budaya, mitos, gaya meme, utilitas digital, dan pengalaman komunitas yang immersive.
      </p>
      <h1>Apa Tujuannya?</h1>
      <p>
        Membentuk ekosistem kreatif yang hidup dari budaya mistis Nusantara melalui staking, NFT, GameFi, dan airdrop ritual harian.
      </p>
    </div>
  </section>
</div>
<div class="section" id="section3">
  <section>
    <div class="mystic-hero">
      <h1>ðŸ“Š Distribusi Token $MYNU</h1>
      <p>
        Token $MYNU memiliki total pasokan tetap dan dirancang untuk memastikan pertumbuhan berkelanjutan ekosistem serta penghargaan terhadap kontribusi komunitas.
      </p>
      <table class="mystic-tokenomics-table">
        <thead>
          <tr>
            <th>Kategori</th>
            <th style="text-align: right;">Alokasi</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>ðŸŽ Airdrop Komunitas</td>
            <td style="text-align: right;">20%</td>
          </tr>
          <tr>
            <td>ðŸ”’ Staking Reward</td>
            <td style="text-align: right;">10%</td>
          </tr>
          <tr>
            <td>ðŸ§™ Reward Holder Program</td>
            <td style="text-align: right;">15%</td>
          </tr>
          <tr>
            <td>ðŸŽ® Mini Games & Utilitas</td>
            <td style="text-align: right;">5%</td>
          </tr>
          <tr>
            <td>ðŸ’° Likuiditas DEX & CEX</td>
            <td style="text-align: right;">15%</td>
          </tr>
          <tr>
            <td>ðŸŒ± Ekosistem & Pengembangan</td>
            <td style="text-align: right;">10%</td>
          </tr>
          <tr>
            <td>ðŸ’°ðŸ“ˆ Marketing & Kemitraan</td>
            <td style="text-align: right;">10%</td>
          </tr>
          <tr>
            <td>ðŸ§™ Tim & Kontributor Awal</td>
            <td style="text-align: right;">10%</td>
          </tr>
          <tr>
            <td>ðŸ›ï¸ Treasury & Governance</td>
            <td style="text-align: right;">5%</td>
          </tr>
        </tbody>
      </table>
      <div style="margin-top: 2rem; text-align: center; font-size: 0.9rem; color: #aaa;">
        <p>ðŸ’Ž <strong>Supply Maksimum:</strong> 400.000.000 $MYNU</p>
        <p>ðŸ§¾ <strong>Jaringan:</strong> Solana SPL Token</p>
        <p>ðŸ“Œ <strong>Alamat Kontrak:</strong> <code><a href="https://explorer.solana.com/address/2F2jZge67xrD4xhH2eVXyBqhVkrUfdvfmeTyDT57tt25/" target="_blank">
                            2F2jZge67xrD4xhH2eVXyBqhVkrUfdvfmeTyDT57tt25</a></code></p>
      </div>
    </div>
  </section>
  </div>
<div class="section">
  <section>
    <div class="mystic-hero">
      <h1>ðŸ“Š Cart & Timeline Vesting</h1>
      <div style="max-width: 180px;margin: auto;">
        <canvas id="pieChart"></canvas>
      </div>
      <div class="scroll-wrapper">
        <div class="scroll-content" style="min-width: 500px;max-width: 700px;">
          <canvas id="vestingChart"></canvas>
        </div>
      </div>
      <a class="mystic-link" style="margin-top: 1rem" href="/tokenomics">ðŸ“œ Detail Tokenomics</a>
    </div>
      </section>
</div>
<div class="section" id="section5">
  <section>
    <div class="mystic-hero">
      <h1>ðŸ’  Token Utility Mystic Nusa</h1>
      <p>Beragam kegunaan token $MYNU dalam ekosistem Mystic Nusa.</p>
    </div>

    <div class="card-grid">
      <div class="card">
        <h3>ðŸ’± Trading</h3>
        <p> Gunakan token untuk jual beli dalam ekosistem Mystic Nusa dan kepemilikan mistikmu.</p>
      </div>
      <div class="card">
        <h3>ðŸ”’ Staking</h3>
        <p> Kunci tokenmu dan bangkitkan kekuatan ghaib. Dapatkan imbalan dari dunia tak kasat mata.</p>
      </div>
      <div class="card">
        <h3>ðŸŽ® GameFi</h3>
        <p>Gunakan token untuk bermain, bertarung, dan menaklukkan dunia dalam game.</p>
      </div>
      <div class="card">
        <h3>ðŸ”‘ Akses Eksklusif</h3>
        <p>Buka area rahasia, fitur premium, dan event terbatas di dunia Mystic Nusa.</p>
      </div>

      <div class="card">
        <h3>ðŸ—³ï¸ DAO Voting</h3>
        <p>Tentukan arah cerita dan fitur selanjutnya. Tokenmu adalah suara mistik yang berpengaruh.</p>
      </div>
      <div class="card">
        <h3>ðŸ–¼ï¸ Merch Swap</h3>
        <p> Tukar token dengan merchandise eksklusif bertema mistis â€” hanya untuk penjaga sejati.</p>
      </div>
    </div>
  </section>
</div>
<div class="section" id="section6">
  <section>
    <div class="mystic-hero">
      <h1>ðŸ”® Mystic Nusa Roadmap 2025â€“2026</h1>
      <p>Langkah spiritual menuju dunia kripto penuh mistik.</p>
    </div>

    <div class="card-grid">
      <div class="card">
        <h3>Q2 2025 â€“ Spirit Awakening</h3>
        <ul>
          <li>âœ”ï¸ Riset & validasi komunitas</li>
          <li>âœ”ï¸ Launch website resmi</li>
          <li>âœ”ï¸ Deploy & verifikasi token</li>
          <li>ðŸŽ Airdrop Campaign #1</li>
        </ul>
      </div>

      <div class="card">
        <h3>Q3 2025 â€“ Awakening of Mystic Nusa</h3>
        <ul>
          <li>ðŸš€ Launch staking program</li>
          <li>ðŸŽ® Launch mini-games #1</li>
          <li>ðŸ¤ Kolaborasi Partner</li>
          <li>ðŸ“ˆ Listing Raydium & Meteora</li>
        </ul>
      </div>
      <div class="card">
        <h3>Q4 2025 â€“ Ritual of Engagement</h3>
        <ul>
          <li>ðŸŒ Launch staking & reward</li>
          <li>ðŸŽ® Launch mini-games #2</li>
          <li>ðŸŽ Airdrop Reward #2</li>
          <li>ðŸ“ˆ Listing Jupiter</li>
        </ul>
      </div>
      <div class="card">
        <h3>Q1 2026 â€“ The Misty Realm</h3>
        <ul>
          <li>ðŸŒ Web3 wallet integration</li>
          <li>ðŸ† Leaderboard rewards</li>
          <li>ðŸ¤ Ekspansi komunitas</li>
        </ul>
      </div>

      <div class="card">
        <h3>Q2 2026 â€“ Mystic Expansion</h3>
        <ul>
          <li>ðŸ“‹ Audit & CEX listing plan</li>
          <li>ðŸ–¼ï¸ Integrasi NFT summon</li>
          <li>ðŸ—³ï¸ DAO voting untuk lore</li>
        </ul>
      </div>
      <div class="card">
        <h3>Q3 2026 â€“ Mystic Future</h3>
        <ul>
          <li>ðŸ“ˆ Listing CEX</li>
          <li>ðŸ”¥ Burn Unclaimed Token </li>
          <li>ðŸ’° Program reward holder</li>
          <li>â© Release plan 2027-2028 </li>
        </ul>
      </div>
    </div>
  </section>
</div>
<div class="section" id="section7">
  <section>
    <div class="mystic-hero">
      <h1>ðŸ“œ Ringkasan Whitepaper</h1>
      <p>
        Mystic Nusa adalah proyek komunitas Web3 bertema budaya mistik nusantara yang menghadirkan ekosistem interaktif, gamified, dan berbasis token. Tujuannya adalah menjembatani teknologi modern dan warisan leluhur Indonesia.
      </p>
      <a class="mystic-link" href="{{ asset('docs/whitepaper-mysticnusa.pdf') }}" target="_blank">ðŸ“¥ Unduh Whitepaper Lengkap</a>
    </div>
  </section>
</div>
<div class="section" id="section8">
  <section>
    <div class="mystic-hero">
    <h1>Gabung Komunitas Mystic Nusa</h1>
    <p>
      Masuki dunia mistis bersama ribuan penjelajah lainnya! Temukan <strong>kode airdrop</strong>, dapatkan <strong>akses eksklusif</strong>, dan jadilah bagian dari komunitas kripto mistis Indonesia.
    </p>

    <div class="social-icons" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem;">
      <a href="https://www.youtube.com/@MysticNusa" target="_blank" class="btn-social">Youtube</a>
      <a href="https://x.com/mysticnusa" target="_blank" class="btn-social">X (Twitter)</a>
      <a href="https://instagram.com/mysticnusa" target="_blank" class="btn-social">Instagram</a>
      <a href="https://t.me/mysticnusa" target="_blank" class="btn-social">Telegram</a>
      <a href="https://tiktok.com/@mysticnusa" target="_blank" class="btn-social">Tiktok</a>
    </div>

    <p style="color: #999;">
      Bergabunglah hari ini dan jadilah bagian dari perjalanan mistis digital pertama di Nusantara.
    </p>
    </div>
  </section>
</div>
<div class="section" id="section9">
  <section>
    <div class="mystic-hero">
      <h2 style="font-size: clamp(1rem, 4vw, 3rem); color: #c084fc;">â“ FAQ â€“ Tanya Jawab</h2>
    </div>
    <div class="faq-container">
      <div class="faq-item" onclick="this.classList.toggle('open')">
        <h3>Apa itu Mystic Nusa?</h3>
        <p>Mystic Nusa adalah Sebuah token komunitas bertema mistis Indonesia yang mengangkat legenda, mitos, dan cerita rakyat ke dunia Web3.</p>
      </div>
      <div class="faq-item" onclick="this.classList.toggle('open')">
        <h3>Bagaimana cara mendapatkan MYNU?</h3>
        <p>Kamu bisa mengikuti program airdrop, berpartisipasi dalam staking, atau membelinya di DEX saat listing.</p>
      </div>
      <div class="faq-item" onclick="this.classList.toggle('open')">
        <h3>Apa kegunaan token MYNU?</h3>
        <p>Token MYNU digunakan untuk voting komunitas, staking reward, akses mini-games, swap merchandise, dan utilitas dalam ekosistem.</p>
      </div>
      <div class="faq-item" onclick="this.classList.toggle('open')">
        <h3>Dimana saya bisa dapatkan kode Airdrop?</h3>
        <p>Kamu bisa mendapatkan kode airdrop dari berbagai platform sosial seperti instagram, twitter, dan konten partnership kami.</p>
      </div>
      <div class="faq-item" onclick="this.classList.toggle('open')">
        <h3>Kenapa saya harus mendaftar?</h3>
        <p>Kamu harus mendaftar terlebih dahulu di website Mystic Nusa untuk membuat akun dan menyimpan token sementara (temporary token) yang kamu dapatkan dari aktivitas seperti airdrop harian atau event komunitas.</p>
      </div>
      <div class="faq-item" onclick="this.classList.toggle('open')">
        <h3>Apa itu temporary token?</h3>
        <p>Temporary token adalah token yang kamu kumpulkan dalam akun selama kamu berpartisipasi di platform (misalnya melalui airdrop harian). Token ini bisa ditukar atau diklaim ke wallet setelah Token Listing di Dex tertentu.</p>
      </div>
      <div class="faq-item" onclick="this.classList.toggle('open')">
        <h3>Apakah token ini sudah bisa diperdagangkan?</h3>
        <p>Untuk saat ini, Mystic Nusa belum listing di DEX/CEX besar. Namun, kamu tetap bisa mengumpulkan token dari airdrop dan staking komunitas sebelum peluncuran resmi liquidity pool (LP).</p>
      </div>
      <div class="faq-item" onclick="this.classList.toggle('open')">
        <h3>Apakah proyek ini aman?</h3>
        <p>Sangat Aman. Dari Nusantara untuk komunitas dengan lebih dari 50% Token disebar. Kami tidak mengadakan private sale atau ICO. Semua distribusi token dilakukan melalui komunitas dan transparan. Sistem kami berbasis registrasi, anti-bot, dan akan diaudit sebelum listing resmi.</p>
      </div>
    </div>
  </section>
</div>
<div class="section" id="section10">
  <section style="padding: 4rem 2rem; color: white; font-family: 'Unbounded', sans-serif; text-align: center;">
<div class="mystic-hero">
   <h1>ðŸ¤ Partnership</h1>
    <p>Kami membuka kolaborasi dengan komunitas, konten kreator, serta platform Web3 untuk menciptakan ekosistem Mystic Nusa yang berkelanjutan dan inklusif.</p>
    <div class="partner-logos" style="margin-top: 2rem; display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem;">
      <a href="https://www.youtube.com/@KisahTengahMalamofficial"><img src="/images/partner1.jpg" alt="Kisah Tengah Mala" style="height: 60px;animation: glow 2s ease-in-out infinite;border-radius: 16px;"></a>
      <img src="/images/partner.jpg" alt="Partner 2" style="height: 60px; filter: grayscale(100%) brightness(1.5);border-radius: 16px;">
      <img src="/images/partner.jpg" alt="Partner 3" style="height: 60px; filter: grayscale(100%) brightness(1.5);border-radius: 16px;">
    </div>
  </section>
  <footer id="mystic-footer" style="background: rgba(255, 255, 255, 0.0);font-size: clamp(0.7rem, 1.1vw, 0.95rem);">
    <div>
      <p>&copy; 2025 Mystic Nusa. Powered by Nusantara Spirit. All rights reserved.</p>
      <div style="margin-top: 1rem;">
        <a href="/terms-and-conditions" style="color: #888; margin: 0 1rem; text-decoration: none;">Syarat & Ketentuan</a>
        <br>
        <a href="/privacy-policy" style="color: #888; margin: 0 1rem; text-decoration: none;">Kebijakan Privasi</a>
      </div>
    </div>
  </footer>
</div>




<div id="tokenomicsModal" class="modal-overlay" onclick="closeTokenomicsModal()">
  <div class="modal-content" onclick="event.stopPropagation()">
    <button class="close-btn" onclick="closeTokenomicsModal()">âœ–</button>
    <h2>ðŸ“Š Mystic Nusa Tokenomics</h2>

    <div class="pie-wrapper">
      <canvas id="pieChart"></canvas>
    </div>

    <table class="token-table">
      <thead>
        <tr>
          <th>Kategori</th>
          <th>Alokasi</th>
          <th>Catatan</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>ðŸ”¥ Airdrop Komunitas Awal</td>
          <td>25%</td>
          <td>Daily claim & misi komunitas</td>
        </tr>
        <tr>
          <td>ðŸ§™ Tim & Developer</td>
          <td>15%</td>
          <td>Vesting 5 tahun (cliff 6 bulan)</td>
        </tr>
        <tr>
          <td>ðŸŒ± Ekosistem & Pengembangan</td>
          <td>15%</td>
          <td>Staking reward, kolaborasi dApp</td>
        </tr>
        <tr>
          <td>ðŸ’§ Likuiditas DEX & CEX</td>
          <td>10%</td>
          <td>Listing dan stabilitas harga</td>
        </tr>
        <tr>
          <td>ðŸŽ® Mini Games & Utilitas</td>
          <td>10%</td>
          <td>Reward gameplay, NFT item</td>
        </tr>
        <tr>
          <td>ðŸ“ˆ Marketing & Komunitas</td>
          <td>10%</td>
          <td>Influencer, ads, sponsor</td>
        </tr>
        <tr>
          <td>ðŸŽ Reward Holder</td>
          <td>10%</td>
          <td>Program loyalitas & musim airdrop</td>
        </tr>
        <tr>
          <td>ðŸ’¼ Treasury DAO</td>
          <td>5%</td>
          <td>Voting dan fund cadangan</td>
        </tr>
      </tbody>
    </table>
    <div class="cta-buttons">
      <a href="/tokenomics">ðŸ“œ Lihat Detail Tokenomics</a>
    </div>
  </div>
</div>
<script>
  const labels = [
    'Month 0', 'Month 3', 'Month 6', 'Month 9', 'Month 12',
    'Month 15', 'Month 18', 'Month 21', 'Month 24', 'Month 27',
    'Month 30', 'Month 33', 'Month 36', 'Month 39', 'Month 42', 'Month 45', 'Month 48', 'Month 51', 'Month 54', 'Month 57', 'Month 60'
  ];

  const data = {
    labels,
    datasets: [{
        label: 'Airdrop',
        data: labels.map((_, i) => (i <= 8 ? (i + 1) * (20 / 9) : 20)),
        backgroundColor: '#a1c4fd'
      },
      {
        label: 'Staking',
        data: labels.map((_, i) => i <= 20 ? (i + 1) * (10 / 21) : 10),
        backgroundColor: '#c2e9fb'
      },
      {
        label: 'Reward Holder',
        data: labels.map((_, i) => i >= 8 ? (i - 8 + 1) * (15 / 13) : 0),
        backgroundColor: '#8ec5fc'
      },
      {
        label: 'Utilitas',
        data: labels.map((_, i) => i <= 4 ? (i + 1) * (5 / 5) : 5),
        backgroundColor: '#736ced'
      },
      {
        label: 'Likuiditas',
        data: labels.map((_, i) => {
          if (i === 0) return 1.25;
          if (i === 1) return 2.5;
          if (i === 2) return 3.75;
          if (i === 3) return 5;
          if (i === 4) return 5;
          if (i === 5) return 10;
          if (i >= 6 && i < 8) return 10;
          if (i >= 8) return 15;
        }),
        backgroundColor: '#3d348b'
      },
      {
        label: 'Ekosistem',
        data: labels.map((_, i) => i >= 8 ? (i - 8 + 1) * (10 / 13) : 0),
        backgroundColor: '#5ddcff'
      },
      {
        label: 'Marketing',
        data: labels.map((_, i) => i <= 12 ? (i + 1) * (10 / 13) : 10),
        backgroundColor: '#2b5876'
      },
      {
        label: 'Tim & Kontributor',
        data: labels.map((_, i) => i >= 12 ? (i - 11) * (10 / 9) : 0),
        backgroundColor: '#cbd5e1'
      },
      {
        label: 'Treasury',
        data: labels.map((_, i) => i >= 12 ? (i - 11) * (5 / 9) : 0),
        backgroundColor: '#8E8C84'
      }
    ]
  };
  const config = {
    type: 'bar',
    data: data,
    options: {
      responsive: true,
      interaction: {
        mode: 'index',
        intersect: false,
      },
      plugins: {
        title: {
          display: false,
        },
        tooltip: {
          mode: 'index',
          intersect: false,
        },
        legend: {
          labels: {
            color: '#ffffff'
          }
        }
      },
      scales: {
        x: {
          stacked: true,
          ticks: {
            color: '#ffffff'
          }
        },
        y: {
          stacked: true,
          ticks: {
            callback: function(value) {
              return value + '%';
            },
            color: '#ffffff'
          }
        }
      }
    },
  };

  new Chart(
    document.getElementById('vestingChart'),
    config
  );
</script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        } else {
          entry.target.classList.remove('visible');
        }
      });
    }, {
      threshold: 0.2
    });

    // Semua section
    document.querySelectorAll('section').forEach(section => {
      section.classList.add('fade-section');
      observer.observe(section);
    });

    // Footer fade ketika muncul di viewport bawah
    const footer = document.querySelector('footer#mystic-footer');
    if (footer) {
      observer.observe(footer);
    }
  });
</script>
<script>
  // Load Pie Chart once
  document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('pieChart').getContext('2d');
    new Chart(ctx, {
      type: 'pie',
      data: {
        labels: [
          'Airdrop', 'Staking', 'Reward Holder', 'Utilitas',
          'Likuiditas', 'Ekosistem', 'Marketing ', 'Tim & Kontributor', 'Treasury'
        ],
        datasets: [{
          data: [20, 10, 15, 5, 15, 10, 10, 10, 5],
          backgroundColor: [
            '#a1c4fd', '#c2e9fb', '#8ec5fc', '#736ced',
            '#3d348b', '#5ddcff', '#2b5876', '#cbd5e1', '#8E8C84'
          ],
          borderWidth: 1
        }]
      },
      options: {
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
  });
</script>
<script>
fetch('/api/track-homepage-visit', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
});
</script>
@endsection