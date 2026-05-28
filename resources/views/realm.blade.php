@extends('layouts.app')
@section('content')

<style>
  body {
    margin: 0;
    background-color: #07070a;
    color: #eee;
    overflow: hidden;
  }

  #fadeOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: black;
    opacity: 1;
    /* awalnya gelap penuh */
    z-index: 9999;
    pointer-events: none;
    transition: opacity 1.2s ease-in-out;
  }

  /* Saat halaman selesai dimuat → perlahan hilang (fade in) */
  #fadeOverlay.hidden {
    opacity: 0;
  }

  /* Saat klik link → perlahan gelap lagi (fade out) */
  .fade-out {
    opacity: 1 !important;
  }


  /* Fog video layer */
  video#fogVideo {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    object-fit: cover;
    z-index: 4;
    opacity: 0.12;
    pointer-events: none;
    filter: brightness(2.2) contrast(2.1);
    mix-blend-mode: lighten;
  }

  /* Map wrapper */
  .map-wrapper {
    width: 100vw;
    height: 100vh;
    overflow: hidden;
    position: relative;
    cursor: grab;
    z-index: 3;
  }

  .map-wrapper:active {
    cursor: grabbing;
  }

  .map-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 1920px;
    height: 820px;
    user-select: none;
    transition: transform 0.2s ease-out;
  }

  .map-base {
    width: 100%;
    height: auto;
    display: block;
    pointer-events: none;
    filter: brightness(0.65) drop-shadow(0 0 40px rgba(0, 0, 0, 0.8));
  }

  /* Interactive spots */
  .spot {
    position: absolute;
    transform: translate(-50%, -50%);
    cursor: pointer;
    transition: transform 0.3s ease, filter 0.3s ease;
    filter: brightness(0.7);
    z-index: 10;
  }

  .spot:hover {
    transform: translate(-50%, -50%);
    filter: brightness(0.9) drop-shadow(0 0 18px rgba(211, 165, 85, 1)) drop-shadow(0 0 28px rgba(211, 186, 85, 0.9));
  }

  .spot img {
    width: 140px;
    height: auto;
    user-select: none;
    pointer-events: none;
  }

  .spot::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100px;
    height: 100px;
    background: radial-gradient(circle, rgba(255, 215, 0, 0.15) 0%, transparent 70%);
    transform: translate(-50%, -50%);
    pointer-events: none;
    z-index: -1;
  }

  a {
    text-decoration: none;
    color: inherit;
  }

  a:hover,
  a:focus,
  a:active {
    text-decoration: none;
  }

  @media (max-width: 768px) {
    .spot {
      filter: brightness(0.9);
    }

    .spot img {
      animation: glowMythical 3s ease-in-out infinite;
    }
  }

  @keyframes glowMythical {
    0% {
      filter: drop-shadow(0 0 3px rgba(211, 182, 85, 0.5)) drop-shadow(0 0 12px rgba(211, 194, 85, 0.7));
    }

    50% {
      filter: drop-shadow(0 0 9px rgba(211, 182, 85, 1)) drop-shadow(0 0 28px rgba(211, 198, 85, 0.9));
    }

    100% {
      filter: drop-shadow(0 0 3px rgba(211, 198, 85, 0.5)) drop-shadow(0 0 12px rgba(211, 202, 85, 0.7));
    }
  }
</style>

<!-- Efek transisi gelap ke terang -->
<div class="fade-overlay" id="fadeOverlay"></div>

<!-- Fog layer -->
<video autoplay muted loop id="fogVideo">
  <source src="{{ asset('videos/fog.webm') }}" type="video/webm">
</video>

<!-- Map utama -->
<div class="map-wrapper" id="mapWrapper">
  <div class="map-container" id="mapContainer">
    <img src="{{ asset('images/asset/realm/mystic_nusa_map_base1.webp') }}" alt="Mystic Nusa Map" class="map-base">

    <a href="{{ url('/cerita') }}" class="spot" style="top: 25%; left: 62.2%;">
      <img src="{{ asset('images/asset/realm/story.png') }}" alt="Story">
      <div style="color: #eee; text-align: center;">Story</div>
    </a>

    <a href="{{ url('/airdrop') }}" class="spot" style="top: 59%; left: 27%;">
      <img src="{{ asset('images/asset/realm/airdrop2.png') }}" alt="Airdrop">
      <div style="color: #eee; text-align: center;">Airdrop</div>
    </a>

    <a href="{{ url('/staking') }}" class="spot" style="top: 58.5%; left: 73.5%;">
      <img src="{{ asset('images/asset/realm/staking2.png') }}" alt="Staking">
      <div style="color: #eee; text-align: center;">Staking</div>
    </a>

    <a href="{{ url('/games') }}" class="spot" style="top: 81.5%; left: 51.3%;">
      <img src="{{ asset('images/asset/realm/games2.png') }}" alt="Games">
      <div style="color: #eee; text-align: center;">Games</div>
    </a>

    <a href="{{ url('/ai-generator') }}" class="spot" style="top: 25.5%; left: 34.3%;">
      <img src="{{ asset('images/asset/realm/generator4.png') }}" alt="Ai Generator" style="transform: scaleX(-1);">
      <div style="color: #eee; text-align: center;">Ai Generator</div>
    </a>
  </div>
</div>

<script>
  // ---- Fade in animation ----
  window.addEventListener("load", () => {
    const overlay = document.getElementById("fadeOverlay");
    // mulai dari gelap → perlahan hilang
    setTimeout(() => overlay.classList.add("hidden"), 200);
  });

  document.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", function(e) {
      const url = this.getAttribute("href");
      if (!url || url.startsWith("#") || this.target === "_blank") return;

      e.preventDefault();
      const overlay = document.getElementById("fadeOverlay");
      overlay.classList.remove("hidden"); // munculkan dulu
      overlay.classList.add("fade-out"); // lalu buat gelap

      setTimeout(() => {
        window.location.href = url;
      }, 900); // waktu sedikit lebih kecil dari transition
    });
  });



  // ---- Map Drag System with inertia ----
  const mapWrapper = document.getElementById('mapWrapper');
  const mapContainer = document.getElementById('mapContainer');

  let isDragging = false;
  let startX, startY, mapX = 0,
    mapY = 0;
  let velocityX = 0,
    velocityY = 0;
  let wrapperWidth = mapWrapper.offsetWidth;
  let wrapperHeight = mapWrapper.offsetHeight;

  mapX = (wrapperWidth - mapContainer.offsetWidth) / 4;
  mapY = (wrapperHeight - mapContainer.offsetHeight) / 4;
  mapContainer.style.transform = `translate(${mapX}px, ${mapY}px)`;

  function updateMapPosition() {
    mapContainer.style.transform = `translate3d(${mapX}px, ${mapY}px, 0)`;
  }

  function applyInertia() {
    if (!isDragging) {
      mapX += velocityX;
      mapY += velocityY;
      velocityX *= 0.93;
      velocityY *= 0.93;
      const minX = wrapperWidth - mapContainer.offsetWidth;
      const minY = wrapperHeight - mapContainer.offsetHeight;
      mapX = Math.min(0, Math.max(minX, mapX));
      mapY = Math.min(0, Math.max(minY, mapY));
      updateMapPosition();

      if (Math.abs(velocityX) > 0.1 || Math.abs(velocityY) > 0.1) {
        requestAnimationFrame(applyInertia);
      }
    }
  }

  window.addEventListener('resize', () => {
    wrapperWidth = mapWrapper.offsetWidth;
    wrapperHeight = mapWrapper.offsetHeight;
  });

  // Desktop Drag
  mapWrapper.addEventListener('mousedown', (e) => {
    isDragging = true;
    startX = e.clientX - mapX;
    startY = e.clientY - mapY;
    velocityX = velocityY = 0;
    mapWrapper.style.cursor = 'grabbing';
  });

  document.addEventListener('mouseup', () => {
    isDragging = false;
    mapWrapper.style.cursor = 'grab';
    requestAnimationFrame(applyInertia);
  });

  document.addEventListener('mousemove', (e) => {
    if (!isDragging) return;
    const prevX = mapX;
    const prevY = mapY;

    mapX = e.clientX - startX;
    mapY = e.clientY - startY;

    const minX = wrapperWidth - mapContainer.offsetWidth;
    const minY = wrapperHeight - mapContainer.offsetHeight;
    mapX = Math.min(0, Math.max(minX, mapX));
    mapY = Math.min(0, Math.max(minY, mapY));

    velocityX = mapX - prevX;
    velocityY = mapY - prevY;

    updateMapPosition();
  });

  // Mobile Drag
  mapWrapper.addEventListener('touchstart', (e) => {
    isDragging = true;
    const touch = e.touches[0];
    startX = touch.clientX - mapX;
    startY = touch.clientY - mapY;
    velocityX = velocityY = 0;
  }, {
    passive: false
  });

  document.addEventListener('touchend', () => {
    isDragging = false;
    requestAnimationFrame(applyInertia);
  }, {
    passive: false
  });

  document.addEventListener('touchmove', (e) => {
    if (!isDragging) return;
    const touch = e.touches[0];
    const prevX = mapX;
    const prevY = mapY;

    mapX = touch.clientX - startX;
    mapY = touch.clientY - startY;

    const minX = wrapperWidth - mapContainer.offsetWidth;
    const minY = wrapperHeight - mapContainer.offsetHeight;
    mapX = Math.min(0, Math.max(minX, mapX));
    mapY = Math.min(0, Math.max(minY, mapY));

    velocityX = mapX - prevX;
    velocityY = mapY - prevY;

    updateMapPosition();
  }, {
    passive: false
  });
</script>

@endsection