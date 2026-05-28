@extends('layouts.app')

@section('content')
<style>
    body {
        margin: 0;
        background: radial-gradient(circle at center, #080010 0%, #000 100%);
        height: 100vh;
        overflow: hidden;
    }

    #introScreen {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.6s ease;
    }

    #startButton {
        background: none;
        border: 2px solid #c6a664;
        color: #c6a664;
        font-size: 1.4rem;
        padding: 12px 28px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.4s ease;
    }

    #startButton:hover {
        background: #c6a664;
        color: #000;
        box-shadow: 0 0 12px #c6a664;
    }

    .story-title {
        text-align: center;
        font-size: 2rem;
        color: #ffe100ff;
        margin-bottom: 24px;
        text-shadow: 0 0 18px rgba(43, 255, 0, 0.);
        animation: titleGlow 3s ease-in-out infinite alternate;
    }

    .story-screen {
        position: relative;
        width: 100%;
        height: 100vh;
        color: #ffd6a5;
        text-shadow: 0 0 12px #ff9b33, 0 0 25px #ff6b00;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        overflow: hidden;
    }

    .story-text {
        font-size: 1.6rem;
        line-height: 1.8;
        opacity: 0;
        transition: opacity 1.2s ease;
        max-width: 85%;
        padding: 0 10px;
    }

    .start-btn {
        position: absolute;
        bottom: 30%;
        border: none;
        color: white;
        font-size: 1.3rem;
        font-weight: bold;
        padding: 14px 34px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .start-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 0 25px rgba(255, 120, 0, 0.8);
    }
    @keyframes titleGlow {
        0% {
            text-shadow: 0 0 10px #ffe100ff, 0 0 20px #ffb700ff, 0 0 30px #ff8c00ff, 0 0 40px #ff5e00ff;
        }
        100% {
            text-shadow: 0 0 20px #ffe100ff, 0 0 30px #ffb700ff, 0 0 40px #ff8c00ff, 0 0 50px #ff5e00ff;
        }
    }
    @media (max-width: 768px) {
        .story-text {
            font-size: 1.25rem;
        }
    }
    .story-author {
        text-align: center;
        font-size: 1rem;
        color: #cccccc;
        margin-bottom: 16px;
    }
    .ending-text {
    color: #ffe6a7;
    font-size: clamp(1.3rem, 5vw, 2rem);
    font-weight: bold;
    text-shadow: 0 0 16px rgba(255, 230, 167, 0.7);
}
</style>




<div id="storyScreen" class="story-screen">
    <div id="storyText" class="story-text"></div>
    <div id="introScreen">
        <div class="story-container">
            <h1 class="story-title">{{ $story->title }}</h1>
            <div class="story-author">Judul, cerita, dan narasi dibuat oleh AI</div>

        </div>
        <button id="startButton" class="start-btn">✨ Mulai Cerita</button>
    </div>
    <audio id="storyAudio" preload="auto">
        <source src="{{ route('cerita.audio', ['story' => $story->id]) }}" type="audio/mpeg">
    </audio>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const intro = document.getElementById('introScreen');
    const audio = document.getElementById("storyAudio");
    const startBtn = document.getElementById("startButton");
    const storyText = document.getElementById("storyText");

    // Ambil paragraf dari server
    const rawText = `{!! addslashes(trim($story->content)) !!}`;
    const lines = rawText.split(/\n+/).map(l => l.trim()).filter(Boolean);

    // Gabungkan setiap 2 baris jadi 1 paragraf
    const paragraphs = [];
    for (let i = 0; i < lines.length; i += 2) {
        paragraphs.push(lines[i] + " " + (lines[i + 1] || ""));
    }

    let index = 0;

    startBtn.addEventListener("click", async () => {
        startBtn.style.display = "none";
        intro.style.opacity = 0;
        setTimeout(() => intro.remove(), 500);
        try {
            setTimeout(() => audio.play(), 1000);
        } catch {
            console.warn("Autoplay blocked. User must interact again.");
        }

        const showParagraph = () => {
            if (index < paragraphs.length) {
                // Tampilkan paragraf
                storyText.style.opacity = 0;
                setTimeout(() => {
                    storyText.innerHTML = paragraphs[index];
                    storyText.style.opacity = 1;
                    index++;
                    setTimeout(showParagraph, 10000); // lanjut ke paragraf berikutnya
                }, 800);
            } else {
                // Setelah semua paragraf selesai
                showEndingMessage();
            }
        };

        const showEndingMessage = () => {
            storyText.style.opacity = 0;
            setTimeout(() => {
                storyText.innerHTML = `<span class="ending-text">✨ Ikuti kisah lainnya, hanya di <strong>Mystic Nusa</strong>. ✨</span>`;
                storyText.style.opacity = 1;

                setTimeout(() => {
                    storyText.style.opacity = 0;
                }, 10000); // hilang setelah 10 detik
            }, 800);
        };

        showParagraph();
    });
});
</script>
@endsection