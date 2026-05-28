@extends('layouts.app')

@section('content')
<style>
    body {
        margin: 0;
        background: radial-gradient(circle at center, #0b0011 0%, #000 80%);
        color: #eee;
        overflow-x: hidden;
    }

    .result-bg {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 40px 20px;
    }

    .content {
        position: relative;
        z-index: 10;
        max-width: 900px;
    }

    h2 {
        font-size: 2.5em;
        color: #c89bff;
        text-shadow: 0 0 15px rgba(180, 0, 255, 0.6);
        margin-bottom: 40px;
        animation: fadeDown 1.5s ease;
    }

    @keyframes fadeDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .image-box {
        display: inline-block;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(160, 0, 255, 0.4);
        box-shadow: 0 0 40px rgba(120, 0, 255, 0.2);
        transition: box-shadow 0.5s ease, transform 0.5s ease;
        animation: glowPulse 6s ease-in-out infinite alternate;
    }

    .image-box:hover {
        transform: scale(1.02);
        box-shadow: 0 0 50px rgba(200, 0, 255, 0.5);
    }

    @keyframes glowPulse {
        0% {
            box-shadow: 0 0 20px rgba(140, 0, 255, 0.3);
        }

        100% {
            box-shadow: 0 0 40px rgba(255, 0, 255, 0.5);
        }
    }

    img {
        width: 100%;
        max-height: 70vh;
        object-fit: contain;
        display: block;
        border-radius: 20px;
    }

    .actions {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-block;
        padding: 12px 28px;
        border-radius: 10px;
        text-decoration: none;
        color: white;
        font-weight: bold;
        font-size: 1em;
        letter-spacing: 1px;
        text-transform: uppercase;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease, background-position 0.4s ease;
        background-size: 200% auto;
    }

    .btn:hover {
        transform: translateY(-3px);
    }

    .btn-download {
        background: linear-gradient(90deg, #7d00ff, #b800ff, #7d00ff);
        box-shadow: 0 0 20px rgba(160, 0, 255, 0.3);
    }

    .btn-download:hover {
        box-shadow: 0 0 25px rgba(200, 100, 255, 0.6);
        background-position: right center;
    }

    .btn-share {
        background: linear-gradient(90deg, #0077ff, #00b4ff, #0077ff);
        box-shadow: 0 0 20px rgba(0, 100, 255, 0.3);
    }

    .btn-share:hover {
        box-shadow: 0 0 25px rgba(100, 180, 255, 0.6);
        background-position: right center;
    }

    .loading,
    .error {
        color: #bbb;
        font-size: 1.2em;
        margin-top: 20px;
    }

    .loading span {
        display: inline-block;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 0.5;
        }

        50% {
            opacity: 1;
        }
    }
</style>

<div class="result-bg">
    <div class="content">
        <h2>🌌 Hasil Visualisasi Mistis 🌌</h2>

        @if($image->status === 'COMPLETED' && $image->image_url)
        <div class="image-box">
            <img src="{{ asset($image->image_url) }}" alt="Generated Image">
        </div>

        <div class="actions">
            <a href="{{ asset($image->image_url) }}" download class="btn btn-download">⬇️ Download</a>
            <a href="https://twitter.com/intent/tweet?text={{ urlencode('Hasil visual mistisku dari Mystic Nusa!') }}&url={{ urlencode(asset($image->image_url)) }}"
                target="_blank" class="btn btn-share">Share ke X</a>
        </div>
        </br>
        <a href="/ai-generator" class="btn btn-back">Kembali</a>
        @elseif($image->status === 'CREATED')
        <div class="loading">
            <p><span>✨</span> Gambar sedang diproses oleh dunia tak kasat mata...</p>
            <p class="small">Silakan tunggu beberapa saat dan refresh halaman ini.</p>
        </div>
        @else
        <div class="error">
            <p>⚠️ Gagal memuat hasil gambar.</p>
        </div>
        @endif
    </div>
</div>
@endsection