@extends('layouts.app')

@section('content')
<style>
    body {
        padding: 2rem;
        padding-top: 4rem;
    }

    #Story-list {
        max-width: 900px;
        margin: 0 auto;
    }

    .Story-item {
        display: flex;
        gap: 20px;
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 0 15px rgba(0, 255, 255, 0.1);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .Story-item:hover {
        box-shadow: 0 0 25px rgba(0, 255, 255, 0.3);
        transform: scale(1.01);
    }

    .Story-image {
        width: 150px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
    }

    .Story-content {
        flex: 1;
        color: #fff;
    }

    .Story-content h5 {
        text-shadow: 0 0 8px #facc15;
        color: rgb(240, 205, 65);
        margin-bottom: 1rem;
        margin: 0;
        font-size: clamp(0.7rem, 2.5vw, 1.2rem);
    }

    .Story-content p {
        margin: 5px 0 0;
        color: #ccc;
        font-size: clamp(0.6rem, 1.5vw, 0.9rem);
    }

    #pagination {
        text-align: center;
        margin-top: 30px;
    }

    #pagination button {
        background-color: transparent;
        border: 1px solid #00ffff;
        color: #00ffff;
        padding: 6px 12px;
        margin: 0 3px;
        border-radius: 6px;
        cursor: pointer;
    }

    #pagination button:hover {
        background-color: #00ffff;
        color: #000;
    }

    #StoryModal {
        position: fixed;
        top: 3%;
        left: 50%;
        bottom: 3%;
        transform: translateX(-50%);
        background-color: #111;
        color: #fff;
        padding: 25px;
        border-radius: 12px;
        max-width: 700px;
        width: 80%;
        max-height: 90vh;
        overflow-y: auto;
        z-index: 9999;
        display: none;
        box-shadow: 0 0 40px rgba(0, 255, 255, 0.5);
        animation: fadeIn 2s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    #StoryModal h3 {
        text-shadow: 0 0 8px #facc15;
        color: rgb(240, 205, 65);
        margin-bottom: 1rem;
        margin: 1rem;
        font-size: clamp(0.7rem, 2.5vw, 1.2rem);
        text-align: center;
    }

    #StoryModal p {
        margin: 15px 0 0;
        color: #ccc;
        font-size: clamp(0.6rem, 1.5vw, 0.9rem);
    }

    #StoryModal ul {
        margin: 5px 0 0;
        color: #ccc;
        font-size: clamp(0.6rem, 1.5vw, 0.9rem);
    }

    #StoryModal li {
        margin: 5px 0 0;
        color: #ccc;
        font-size: clamp(0.6rem, 1.5vw, 0.9rem);
    }

    #StoryModal img {
        max-width: 90%;
        margin-bottom: 15px;
        border-radius: 8px;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }

    #StoryModal .close-btn {
        position: absolute;
        top: 10px;
        right: 15px;
        cursor: pointer;
        font-size: 1.2rem;
        color: #00ffff;
    }

    #StoryModal::-webkit-scrollbar {
        width: 8px;
    }

    #StoryModal::-webkit-scrollbar-track {
        background: transparent;
    }

    #StoryModal::-webkit-scrollbar-thumb {
        background-color: rgba(0, 255, 255, 0.3);
        border-radius: 6px;
        border: 1px solid transparent;
    }

    #StoryModal:hover::-webkit-scrollbar-thumb {
        background-color: rgba(116, 136, 136, 0.5);
    }

    #StoryModal {
        scrollbar-width: thin;
        scrollbar-color: rgba(178, 218, 218, 0.5) transparent;
    }

    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 9998;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        z-index: 1000;
        overflow: auto;
    }

    h1 {
        font-size: clamp(1.2rem, 4vw, 1.8rem);
        text-shadow: 0 0 8px #facc15;
        color: rgb(240, 205, 65);
        margin-bottom: 1rem;
        line-height: 1.2;
        text-align: center;
    }

    #modalContent {
        white-space: pre-line;
        font-size: 16px;
        line-height: 1.7;
    }

    .modal-actions {
        display: flex;
        justify-content: right;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
        margin-bottom: 0px;
        opacity: 1;
        transform: translateY(-10px);
        transition: all 0.4s ease;
        z-index: 1;
    }

    /* Gaya tombol melayang */
    .modal-actions a,
    .modal-actions button {
        background: rgba(40, 0, 60, 0.8);
        color: #d9b3ff;
        border: 1px solid rgba(180, 0, 255, 0.5);
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: 0 0 10px rgba(160, 0, 255, 0.3);
    }

    /* Hover effect: aura mistis */
    .modal-actions a:hover,
    .modal-actions button:hover {
        background: rgba(100, 0, 180, 0.9);
        color: #fff;
        box-shadow: 0 0 20px rgba(200, 0, 255, 0.6);
        transform: scale(1.1);
    }

    .mystic-btn {
        background-color: #0a74da;
        border: none;
        padding: 8px 14px;
        border-radius: 8px;
        color: #fff;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: 0.2s;
    }

    .mystic-btn:hover {
        opacity: .8;
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }

    @media (max-width: 600px) {
        .Story-item {
            gap: 6px;
            padding: 4px;
        }
    }
</style>

<div class="container">
    <h1 class="text-center text-white mb-4">✨ Cerita Mystic Nusa</h1>
    <div id="Story-list"></div>
    <div id="pagination"></div>
</div>

<!-- Modal -->
<div class="overlay" id="overlay" onclick="hideModal()"></div>
<div id="StoryModal">
    <div class="close-btn" onclick="hideModal()">✖</div>
    <div id="modalId" style="display: none;"></div>
    <h3 id="modalTitle"></h3>
    <img id="modalImage">
    <video id="modalVideo" controls style="max-width: 100%; display: none;"></video>
    <div id="modalContent" style="padding: 10px;"></div>
    <div class="modal-actions">
        <div id="button-message"></div>
        <button class="icon svg-icon" onclick="copyAll()" title="Copy">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
            </svg>
        </button>
        <button class="icon svg-icon" onclick="downloadImage()" title="Download">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                <polyline points="14 2 14 8 20 8" />
                <path d="M12 11v5" />
                <path d="m10 14 2 2 2-2" />
            </svg>
        </button>
        <button class="icon svg-icon" onclick="openYoutube()" title="Open in YouTube">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21.8 8.2c-.2-1.4-1.3-2.5-2.7-2.7C17 5.2 12 5.2 12 5.2s-5 0-7.1.3c-1.4.2-2.5 1.3-2.7 2.7C2 10.3 2 12 2 12s0 1.7.2 3.8c.2 1.4 1.3 2.5 2.7 2.7C7 18.8 12 18.8 12 18.8s5 0 7.1-.3c1.4-.2 2.5-1.3 2.7-2.7.2-2.1.2-3.8.2-3.8s0-1.7-.2-3.8z"></path>
                <path d="M10 15.5v-7l6 3.5-6 3.5z" fill="#fff"></path>
            </svg>
        </button>
        <!-- <button class="icon svg-icon" onclick="downloadVideo()" title="Download">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <polyline points="7 10 12 15 17 10" />
                <line x1="12" y1="15" x2="12" y2="3" />
            </svg>
        </button> -->
        <button class="icon svg-icon" onclick="playAudio()" title="Play Audio">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <polygon points="10 8 16 12 10 16 10 8" />
            </svg>
        </button>

    </div>
</div>

<audio id="bgmaudio" src="/sound/bgm/bgmvideo.mp3"></audio>
@endsection


<script>
    let currentAudio = null;

    function escapeHTML(str) {
        return str.replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }

    function loadStory(page = 1) {
        fetch(`/cerita/list?page=${page}`)
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById('Story-list');
                list.innerHTML = '';
                data.data.forEach(Story => {
                    const item = document.createElement('div');
                    item.classList.add('Story-item');
                    item.onclick = () => showModal(Story.id, Story.title, Story.image_path ? Story.image_path : '/images/logo-mystic-nusa.png', Story.video_path, Story.content);

                    item.innerHTML = `
                    <img src="${Story.image_path ? Story.image_path : '/images/logo-mystic-nusa.png'}" class="Story-image" alt="${Story.title}">
                    <div class="Story-content">
                        <h5>${Story.title}</h5>
                        <p>${Story.content.substring(0, 100) ?? Story.content.replace(/<[^>]+>/g, '').substring(0, 100)}...</p>
                        <p style="color:yellow;">${new Date(Story.date).toLocaleDateString('id-ID', {
  day: 'numeric',
  month: 'long',
  year: 'numeric'
})}</p>
                    </div>
                `;
                    list.style.animation = 'fadeIn 3s ease';
                    list.appendChild(item);
                });

                let pagination = '';
                for (let i = 1; i <= data.last_page; i++) {
                    pagination += `<button onclick="loadStory(${i})">${i}</button> `;
                }
                document.getElementById('pagination').innerHTML = pagination;
            });
    }

    function showModal(id, title, image, video, content) {
        document.getElementById('modalId').innerText = id;
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalImage').src = image;
        document.getElementById('modalVideo').src = video;
        document.getElementById('modalContent').innerHTML = content;
        document.getElementById('StoryModal').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    }

    function hideModal() {
        // document.getElementById('StoryModal').style.display = 'none';
        stopAudio();
        document.getElementById('overlay').style.display = 'none';
        document.getElementById('StoryModal').style.animation = 'fadeOut 3s ease';
        setTimeout(() => {
            document.getElementById('StoryModal').style.display = 'none';
            document.getElementById('StoryModal').style.animation = '';
        }, 2000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadStory();
    });

    function copyAll() {
        const buttonMessage = document.getElementById('button-message');
        const title = document.getElementById('modalTitle').innerText;
        const content = document.getElementById('modalContent').innerText;

        const combined = title + "\n\n" + content;

        navigator.clipboard.writeText(combined).then(() => {
            buttonMessage.innerText = "Copied!";
            setTimeout(() => {
                buttonMessage.innerText = "";
            }, 5000);
        });
    }

    function downloadImage() {
        const imgSrc = document.getElementById('modalImage').src;

        const link = document.createElement('a');
        link.href = imgSrc;
        link.download = "story-image.jpg";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function downloadVideo() {
        const storyId = document.getElementById('modalId').innerText;
        const videopath = document.getElementById('modalVideo').src;
        const buttonMessage = document.getElementById('button-message');
        if (!videopath || videopath === "" || videopath.includes("null")) {
            buttonMessage.innerText = "Video tidak tersedia.";
            setTimeout(() => {
                buttonMessage.innerText = "";
            }, 5000);
            return;
        }
        const videoUrl = `/cerita/video/${storyId}`;

        const link = document.createElement('a');
        link.href = videoUrl;
        link.download = "story-video.mp4";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function playAudio() {
        const storyId = document.getElementById('modalId').innerText;
        const bgmaudio = document.getElementById('bgmaudio');
        const buttonMessage = document.getElementById('button-message');
        const audioUrl = `cerita/audio/${storyId}`;
        currentAudio = new Audio(audioUrl);
        currentAudio.onerror = () => {
            buttonMessage.innerText = "Audio tidak tersedia.";
            setTimeout(() => {
                buttonMessage.innerText = "";
            }, 5000);
        };
        currentAudio.play();
        bgmaudio.currentTime = 0;
        bgmaudio.volume = 0.02;
        bgmaudio.play();
    }

    function stopAudio() {
        if (currentAudio) {
            currentAudio.pause();
            currentAudio.currentTime = 0;
            currentAudio = null;
            const bgmaudio = document.getElementById('bgmaudio');
            bgmaudio.pause();
            bgmaudio.currentTime = 0;
        }
    }

    function openYoutube() {
        window.open('https://www.youtube.com/@MysticNusa', '_blank');
    }
</script>