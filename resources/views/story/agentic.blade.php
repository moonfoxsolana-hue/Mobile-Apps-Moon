@extends('layouts.app')

@section('content')
<style>
    /* Dark Theme: Hitam sebagai base, biru untuk border/tombol, ungu untuk user, kuning untuk AI */
    body {
        background-color: #121212;
        /* Hitam gelap */
        color: #e0e0e0;
        /* Abu muda untuk teks */
        font-family: Arial, sans-serif;
    }

    .chat-container {
        display: flex;
        flex-direction: column;
        /* max-width: 1000px; */
        /* margin: 100px auto; */
        background-color: #1e1e1e;
        /* Hitam lebih terang */
        border: 1px solid #0a74da;
        /* Biru untuk border */
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
    }

    .chat-messages {
        height: 90vh;
        width: 100%;
        padding: 20px;
        overflow-y: auto;
        background-color: #121212;
    }

    .message {
        padding: 10px 15px;
        margin-bottom: 10px;
        border-radius: 20px;
        max-width: 80%;
        word-wrap: break-word;
        white-space: pre-wrap;
        /* Ini preserve \n jadi line breaks asli */
    }

    .message.user {
        background-color: #6a1b9a;
        /* Ungu untuk pesan user */
        color: #fff;
        align-self: flex-end;
        margin-left: auto;
    }

    .message.ai {
        background-color: #ffd700;
        /* Kuning untuk pesan AI */
        color: #000;
        /* Hitam untuk kontras */
        align-self: flex-start;
        margin-right: auto;
    }

    .chat-form {
        display: flex;
        height: 100%;
        width: 100%;
        border-top: 1px solid #0a74da;
        /* Biru untuk pemisah */
        background-color: #1e1e1e;
    }

    #chat-input {
        flex: 1;
        padding: 15px;
        border: none;
        background-color: transparent;
        color: #e0e0e0;
    }

    #chat-input:focus {
        outline: none;
    }

    button[type="submit"] {
        padding: 15px 20px;
        background-color: #0a74da;
        /* Biru untuk tombol */
        color: #fff;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button[type="submit"]:hover {
        background-color: #005bb5;
        /* Biru lebih gelap saat hover */
    }

    /* Scrollbar custom untuk dark theme */
    .chat-messages::-webkit-scrollbar {
        width: 8px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: #1e1e1e;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #6a1b9a;
        /* Ungu untuk scrollbar */
        border-radius: 4px;
    }

    .request-mode {
        margin-left: 10px;
        background-color: #121212;
        color: #e0e0e0;
        border: 1px solid #0a74da;
        padding: 5px;
    }

    /* Tambahan untuk typing indicator */
    .typing-indicator {
        background-color: #ffd700;
        /* Kuning seperti bubble AI */
        color: #000;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 10px 15px;
        border-radius: 20px;
        width: fit-content;
    }

    .dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        margin: 0 2px;
        background-color: #000;
        /* Hitam untuk dots */
        border-radius: 50%;
        animation: bounce 1.2s infinite;
    }

    .dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes bounce {

        0%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-5px);
        }
    }

    @media (max-width: 600px) {
        #chat-input {
            width: 50%;
        }

        .request-mode {
            width: 30%;
        }
        button[type="submit"] {
            width: 20%;
        }
    }
</style>

<div class="chat-container">
    <div class="chat-messages" id="chat-messages">
        <!-- Pesan akan ditambahkan di sini via JS -->
    </div>
    <form id="chat-form" class="chat-form">
        <input type="text" id="chat-input" placeholder="Ketik pesanmu..." required>
        <select id="request-mode" value="chat" class="request-mode">
            <option value="story" selected>Cerita</option>
            <option value="story-audio">Cerita + Audio</option>
            <option value="story-image">Cerita + Gambar AI</option>
            <option value="story-audio-image">Cerita + Audio + Gambar AI</option>
        </select>
        <button type="submit">Kirim</button>
    </form>
</div>

<script>
    const chatform = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const messages = document.getElementById('chat-messages');

    chatform.addEventListener('submit', async (e) => {
        e.preventDefault();
        const userMessage = input.value.trim();
        const requestMode = document.getElementById('request-mode').value;
        if (!userMessage) return;

        // Tampilkan pesan user langsung
        addMessage(userMessage, 'user', false); // false = no animation
        input.value = '';

        // Tambahkan typing indicator untuk AI
        const typingIndicator = addTypingIndicator();

        // Kirim ke backend Laravel via AJAX
        try {
            const response = await fetch('/api/story/agent', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    query: userMessage,
                    mode: requestMode
                })
            });
            const jsonData = await response.json();

            // Asumsi respons seperti contohmu: ambil content atau seluruh data

            let aiMessage = '';
            if (jsonData.status === 'success' && jsonData.data) {
                aiMessage = jsonData.data.title + '\n\n' + jsonData.data.content; // Gabung title & content
            } else {
                aiMessage = 'Error: Respons tidak valid.';
            }

            // Hapus typing indicator
            typingIndicator.remove();

            // Tampilkan AI message dengan animasi typing
            addMessage(aiMessage, 'ai', true); // true = with animation
        } catch (error) {
            typingIndicator.remove();
            addMessage('Error: Tidak bisa terhubung ke AI.', 'ai', false);
        }
    });

    // Function untuk tambah message (support animation)
    function addMessage(text, sender, withAnimation = false) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', sender);

        if (!withAnimation) {
            // Tanpa animasi: langsung tampil full
            messageDiv.textContent = text;
        } else {
            // Dengan animasi: typing effect
            let index = 0;
            messageDiv.textContent = ''; // Mulai kosong
            const typingInterval = setInterval(() => {
                if (index < text.length) {
                    messageDiv.textContent += text.charAt(index);
                    index++;
                } else {
                    clearInterval(typingInterval);
                }
            }, 5); // Speed: 5ms per karakter (adjust sesuai keinginan)
        }

        messages.appendChild(messageDiv);
        messages.scrollTop = messages.scrollHeight;
        return messageDiv; // Return div untuk bisa di-remove jika perlu
    }

    // Function untuk typing indicator (misalnya dots animasi)
    function addTypingIndicator() {
        const indicatorDiv = document.createElement('div');
        indicatorDiv.classList.add('message', 'ai', 'typing-indicator');
        indicatorDiv.innerHTML = '<span class="dot"></span><span class="dot"></span><span class="dot"></span>';
        messages.appendChild(indicatorDiv);
        messages.scrollTop = messages.scrollHeight;
        return indicatorDiv;
    }
</script>
@endsection