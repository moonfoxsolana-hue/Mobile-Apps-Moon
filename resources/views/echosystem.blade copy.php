<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animasi Ganda Berbasis Scroll</title>
    <!-- Muat Tailwind CSS untuk styling dasar dan responsif -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Konfigurasi Font Inter -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            min-height: 700vh; /* Tambah tinggi agar bisa scroll melewati 2 animasi */
            background-color: #f7f7f7;
            color: #1f2937;
        }
        /* Style untuk Konten Utama di balik gerbang */
        .content-reveal {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden; 
        }
        /* GANTI URL DI BAWAH DENGAN URL GAMBAR PNG/JPG ANDA */
        #content-reveal-1 {
            /* Contoh URL Gambar PNG/JPG: Ganti dengan URL Gambar Sayap Terbuka Anda */
            background-image: url('https://picsum.photos/1200/800?random=1');
            background-size: cover;
            background-position: center;
        }
        /* GANTI URL DI BAWAH DENGAN URL GAMBAR PNG/JPG ANDA */
        #content-reveal-2 {
            /* Contoh URL Gambar PNG/JPG: Ganti dengan URL Gambar Gerbang Geser Anda */
            background-image: url('https://picsum.photos/1200/800?random=2');
            background-size: cover;
            background-position: center;
        }

        /* Style untuk Gerbang */
        .gate {
            position: absolute;
            top: 0;
            height: 100%;
            width: 200px; /* Lebar tetap 200px */
            z-index: 10;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #111827; /* Warna default gelap */
        }
        
        /* WARNA GERBANG BERBEDA */
        .gate-type-1 { background-color: #111827; } /* Gerbang Sayap (Gelap) */
        .gate-type-2 { background-color: #4f46e5; } /* Gerbang Geser (Ungu) */


        /* Origin Transform untuk efek rotasi sayap (Hanya untuk Tipe 1) */
        #left-gate-1 {
            left: calc(50% - 200px); /* Posisikan tepi kiri gerbang 200px dari tengah (agar menutupi separuh kiri tengah) */
            transform-origin: right center; /* Rotasi dari sisi kanan (tengah) */
            justify-content: flex-end; 
            padding-right: 2rem;
        }
        #right-gate-1 {
            left: 50%; /* Posisikan tepi kiri gerbang tepat di tengah (agar menutupi separuh kanan tengah) */
            transform-origin: left center; /* Rotasi dari sisi kiri (tengah) */
            justify-content: flex-start; 
            padding-left: 2rem;
        }
        /* Gerbang Geser Sederhana (Tipe 2) - Tetap di pinggir */
        #left-gate-2 { left: 0; justify-content: flex-end; padding-right: 2rem; }
        #right-gate-2 { right: 0; justify-content: flex-start; padding-left: 2rem; }

        .text-overlay {
            z-index: 5;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            padding: 2rem;
            border-radius: 0.5rem;
        }
        .gate-text {
            color: #d1d5db; /* Warna teks di gerbang */
            text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
        }
    </style>
</head>
<body class="antialiased">

    <!-- Konten Dummy Awal -->
    <header class="h-screen flex items-center justify-center bg-gray-900 text-white shadow-2xl">
        <div class="text-center p-8">
            <h1 class="text-5xl font-extrabold mb-4">Mulai Animasi Ganda</h1>
            <p class="text-xl text-gray-300">Geser ke bawah untuk Animasi 1: Sayap Terbuka.</p>
            <svg class="w-8 h-8 mx-auto mt-6 animate-bounce text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
        </div>
    </header>

    <!-- SECTION 1: GERBANG SAYAP TERBUKA (Mulai dari tengah) -->
    <section id="gate-section-1">
        <div id="content-reveal-1" class="content-reveal">
            <div class="text-overlay text-center text-white bg-black/50">
                <h2 class="text-4xl md:text-6xl font-black mb-4">ANIMASI 1: SAYAP</h2>
                <p class="text-xl md:text-2xl">Gerbang Sayap Terbuka Penuh!</p>
            </div>
            
            <!-- Gerbang Kiri Sayap -->
            <div id="left-gate-1" class="gate gate-type-1">
                <p class="gate-text text-3xl font-bold">Sayap Kiri</p>
            </div>

            <!-- Gerbang Kanan Sayap -->
            <div id="right-gate-1" class="gate gate-type-1">
                <p class="gate-text text-3xl font-bold">Sayap Kanan</p>
            </div>
        </div>
    </section>

    <!-- Konten transisi antara 2 animasi -->
    <div class="h-screen flex items-center justify-center bg-gray-200 text-gray-800">
        <div class="text-center p-8">
            <h2 class="text-3xl font-bold mb-3">TRANSISI</h2>
            <p class="text-xl">Geser lagi ke bawah untuk Animasi 2: Gerbang Geser Sederhana.</p>
        </div>
    </div>
    
    <!-- SECTION 2: GERBANG GESER SEDERHANA (Mulai dari pinggir) -->
    <section id="gate-section-2">
        <div id="content-reveal-2" class="content-reveal">
            <div class="text-overlay text-center text-white bg-black/50">
                <h2 class="text-4xl md:text-6xl font-black mb-4">ANIMASI 2: GESER</h2>
                <p class="text-xl md:text-2xl">Gerbang Geser Terbuka Penuh!</p>
            </div>
            
            <!-- Gerbang Kiri Geser -->
            <div id="left-gate-2" class="gate gate-type-2">
                <p class="gate-text text-3xl font-bold">Gerbang Kiri</p>
            </div>

            <!-- Gerbang Kanan Geser -->
            <div id="right-gate-2" class="gate gate-type-2">
                <p class="gate-text text-3xl font-bold">Gerbang Kanan</p>
            </div>
        </div>
    </section>
    
    <!-- Konten Dummy Akhir -->
    <div class="h-screen flex items-center justify-center bg-gray-900 text-white">
        <p class="text-3xl font-semibold">Semua Animasi Selesai!</p>
    </div>

    <!-- SCRIPT UTAMA -->
    <!-- Muat Library GSAP dan Plugin ScrollTrigger -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            try {
                gsap.registerPlugin(ScrollTrigger);

                // --- 1. ANIMASI SAYAP TERBUKA (SECTION 1) - Pembukaan oleh Scroll ---
                gsap.timeline({
                    scrollTrigger: {
                        trigger: "#gate-section-1",
                        scrub: 1.5, 
                        start: "top bottom", 
                        end: "center center", 
                    }
                })
                // Gerbang Kiri: geser dan putar (tanpa gerakan Y terikat scroll)
                .to("#left-gate-1", {
                    xPercent: -100, // Geser jauh ke kiri
                    rotateZ: -60, 
                    duration: 1,
                    ease: "none"
                }, 0) 
                // Gerbang Kanan: geser dan putar (tanpa gerakan Y terikat scroll)
                .to("#right-gate-1", {
                    xPercent: 100, // Geser jauh ke kanan
                    rotateZ: 60, 
                    duration: 1,
                    ease: "none"
                }, 0); 

                // --- 1B. ANIMASI MENGAMBANG (FLOATING) - Terpisah, dimulai saat terlihat ---
                const floatAmount = 15; // Jarak mengambang (piksel)
                const floatSpeed = 2.5; // Kecepatan mengambang (detik)

                // Timeline Mengambang Kiri (Naik-Turun)
                const leftFloat = gsap.to("#left-gate-1", {
                    y: `-=${floatAmount}`,
                    duration: floatSpeed,
                    ease: "sine.inOut", // Kecepatan halus
                    yoyo: true,
                    repeat: -1, // Berulang tanpa batas
                    paused: true // Mulai dalam keadaan jeda
                });

                // Timeline Mengambang Kanan (Turun-Naik)
                const rightFloat = gsap.to("#right-gate-1", {
                    y: `+=${floatAmount}`,
                    duration: floatSpeed,
                    ease: "sine.inOut",
                    yoyo: true,
                    repeat: -1,
                    paused: true
                });

                // ScrollTrigger untuk mengontrol animasi mengambang (memutar/menjeda)
                ScrollTrigger.create({
                    trigger: "#gate-section-1",
                    start: "top bottom", // Mulai putar saat section muncul dari bawah
                    end: "bottom top",   // Jeda saat section hilang dari atas
                    
                    onEnter: () => {
                        leftFloat.play();
                        rightFloat.play();
                    },
                    onLeave: () => {
                        leftFloat.pause(0); // Jeda dan kembalikan ke awal
                        rightFloat.pause(0);
                    },
                    onEnterBack: () => {
                        leftFloat.play();
                        rightFloat.play();
                    },
                    onLeaveBack: () => {
                        leftFloat.pause(0);
                        rightFloat.pause(0);
                    }
                });
                
                // --- 2. ANIMASI GESER SEDERHANA (SECTION 2) - Tidak ada perubahan ---
                gsap.timeline({
                    scrollTrigger: {
                        trigger: "#gate-section-2",
                        scrub: 1.5, 
                        start: "top bottom", 
                        end: "center center", 
                    }
                })
                // Gerbang Kiri: geser ke kiri (tanpa rotasi)
                .to("#left-gate-2", {
                    xPercent: -150,
                    duration: 1,
                    ease: "none"
                }, 0) 
                // Gerbang Kanan: geser ke kanan (tanpa rotasi)
                .to("#right-gate-2", {
                    xPercent: 150,
                    duration: 1,
                    ease: "none"
                }, 0); 

            } catch (error) {
                console.error("Gagal menjalankan animasi GSAP:", error);
                const container1 = document.getElementById('gate-section-1');
                const container2 = document.getElementById('gate-section-2');
                if (container1) container1.innerHTML = `<div class="p-8 bg-red-100 text-red-800 text-center font-semibold">Gagal memuat Animasi 1.</div>`;
                if (container2) container2.innerHTML = `<div class="p-8 bg-red-100 text-red-800 text-center font-semibold">Gagal memuat Animasi 2.</div>`;
            }
        });
    </script>
</body>
</html>