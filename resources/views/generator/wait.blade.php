@extends('layouts.app')

@section('content')
<style>
    body {
        margin: 0;
        background: radial-gradient(circle at center, #080008 0%, #000 90%);
        color: #d3a8ff;
        font-family: 'Cinzel', serif;
        overflow: hidden;
    }

    .wait-container {
        position: relative;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 20px;
    }

    .content {
        position: relative;
        z-index: 10;
    }

    .loading-circle {
        position: relative;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 3px solid rgba(160, 0, 255, 0.2);
        box-shadow: 0 0 30px rgba(180, 0, 255, 0.3);
        margin: 40px auto;
        animation: rotate 4s linear infinite;
    }

    .loading-circle::before {
        content: '';
        position: absolute;
        top: -3px;
        left: -3px;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border-top: 3px solid #c285ff;
        border-right: 3px solid transparent;
        border-bottom: 3px solid #c285ff;
        border-left: 3px solid transparent;
        animation: rotate 2s linear infinite reverse;
    }

    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    h1 {
        font-size: 2em;
        margin-bottom: 10px;
        text-shadow: 0 0 15px rgba(180, 0, 255, 0.4);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 0.7;
        }

        50% {
            opacity: 1;
        }
    }

    p {
        font-size: 0.9em;
        color: #a67edb;
        letter-spacing: 1px;
    }

    .task-id {
        margin-top: 10px;
        color: #6e4f99;
        font-size: 0.85em;
    }

    .glow-line {
        width: 80px;
        height: 2px;
        margin: 20px auto;
        background: linear-gradient(90deg, transparent, #b46aff, transparent);
        animation: shimmer 3s ease-in-out infinite;
    }

    @keyframes shimmer {

        0%,
        100% {
            opacity: 0.2;
        }

        50% {
            opacity: 1;
        }
    }
</style>

<div class="wait-container">
    <div class="content">
        <div class="loading-circle"></div>
        <h1>⏳ Memanggil Energi Visual...</h1>
        <div class="glow-line"></div>
        <p>Energi dari dunia tak kasat mata sedang membentuk visual mistikmu.</p>
        <p class="task-id">Task ID: {{ $taskId }}</p>
    </div>
</div>

<script>
    // Auto refresh setiap 5 detik untuk cek status baru
    setTimeout(() => {
        window.location.reload();
    }, 5000);
</script>
@endsection