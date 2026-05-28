@extends('layouts.app')

@section('content')
<div class="games-wrapper"></div>
<div class="games-landing">
    <h1 class="page-title">Mystic Nusa Games</h1>

    <div class="games-list">
        <!-- Game 1 -->
        <div class="game-card">
            <img src="{{ asset('images/ngepet-online.jpg') }}" alt="Ngepet Online">
            <h2>Ngepet Online</h2>
            <a href="{{ url('/games/ngepet/') }}" class="play-btn">Play Now</a>
        </div>

        <!-- Game 2 -->
        <div class="game-card">
            <img src="{{ asset('images/intuition-test.jpg') }}" alt="Intuition Test">
            <h2>Uji Intuisi</h2>
            <a href="{{ url('/games/intuition/') }}" class="play-btn">Play Now</a>
        </div>

        <!-- Game 3 -->
        <div class="game-card">
            <img src="{{ asset('images/logic-minds.jpg') }}" alt="Mystical Logic of Minds">
            <h2>Mystical Logic of Minds</h2>
            <a href="{{ url('/games/logical/') }}" class="play-btn">Play Now</a>
        </div>
        <!-- Game 4 -->
        <div class="game-card">
            <img src="{{ asset('images/trivia.jpg') }}" alt="Arcane of Trivia">
            <h2>Arcane of Trivia</h2>
            <a href="{{ url('/games/trivia/') }}" class="play-btn">Play Now</a>
        </div>

        <!-- Game 5 -->
        <div class="game-card">
            <img src="{{ asset('images/tarot.jpg') }}" alt="Tarot of Mystic Nusa">
            <h2>Tarot of Mystic Nusa</h2>
            <a href="{{ url('/games/tarot/') }}" class="play-btn">Play Now</a>
        </div>
        <!-- Game 6 -->
        <div class="game-card coming-soon">
            <img src="{{ asset('images/coming-soon-games.jpg') }}" alt="Coming Soon">
            <h2>Coming Soon</h2>
        </div>
    </div>
</div>

<style>
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        margin: 0;
        padding: 0;
        background: #0b0f0d;
        overflow-x: hidden;
    }

    .games-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background: #0b0f0d;
        z-index: -1;
    }

    .games-landing {
        text-align: center;
        padding-top: 5rem;
        padding-bottom: 2rem;
        color: #e0e0e0;
        font-family: 'Poppins', sans-serif;
        width: 100%;
        max-width: 1200px;
        margin: auto;
    }

    .page-title {
        font-size: clamp(1.8rem, 6vw, 2.8rem);
        text-shadow: 0 0 8px #facc15;
        color: rgb(240, 205, 65);
        margin-bottom: 1.5rem;
        line-height: 1.3;
        text-align: center;
        padding: 0 1rem;
    }

    /* 🎮 Grid Layout */
    .games-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 20px;
        padding: 0 1rem;
    }

    .game-card {
        background: #151c19;
        border: 2px solid #00ff88;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 0 15px rgba(0, 255, 136, 0.4);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        box-sizing: border-box;
    }

    .game-card img {
        width: 100%;
        border-radius: 8px;
        margin-bottom: 15px;
        height: auto;
        object-fit: cover;
    }

    .game-card h2 {
        font-size: 1rem;
        margin-bottom: 15px;
        word-wrap: break-word;
    }

    .game-card .play-btn {
        display: inline-block;
        padding: 10px 18px;
        background: #00ff88;
        color: #000;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        transition: background 0.2s ease;
        font-size: 0.9rem;
    }

    .game-card .play-btn:hover {
        background: #00cc6d;
    }

    .game-card:hover {
        transform: scale(1.05);
        box-shadow: 0 0 25px rgba(0, 255, 136, 0.6);
    }

    .coming-soon {
        opacity: 0.6;
        pointer-events: none;
    }

    /* 🌙 Responsif: tetap minimal 2 kolom */
    @media (max-width: 768px) {
        .games-landing {
            padding-top: 4rem;
        }

        .games-list {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .game-card {
            padding: 16px;
        }

        .game-card h2 {
            font-size: 0.9rem;
        }

        .game-card .play-btn {
            width: 100%;
            padding: 10px 0;
        }
    }

    /* 📱 Untuk layar sangat kecil (≤400px) */
    @media (max-width: 400px) {
        .games-list {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .game-card {
            padding: 14px;
        }

        .game-card h2 {
            font-size: 0.85rem;
        }

        .game-card .play-btn {
            font-size: 0.8rem;
        }
    }
</style>
@endsection