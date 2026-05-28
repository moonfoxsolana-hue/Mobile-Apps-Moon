@extends('layouts.app')

@section('content')
<style>
    body {
        padding: 2rem;
        padding-top: 4rem;
    }

    #news-list {
        max-width: 900px;
        margin: 0 auto;
    }

    .news-item {
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

    .news-item:hover {
        box-shadow: 0 0 25px rgba(0, 255, 255, 0.3);
        transform: scale(1.01);
    }

    .news-image {
        width: 150px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
    }

    .news-content {
        flex: 1;
        color: #fff;
    }

    .news-content h5 {
        text-shadow: 0 0 8px #facc15;
        color: rgb(240, 205, 65);
        margin-bottom: 1rem;
        margin: 0;
        font-size: clamp(0.7rem, 2.5vw, 1.2rem);
    }

    .news-content p {
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

    #newsModal {
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
    }

    #newsModal h3 {
        text-shadow: 0 0 8px #facc15;
        color: rgb(240, 205, 65);
        margin-bottom: 1rem;
        margin: 1rem;
        font-size: clamp(0.7rem, 2.5vw, 1.2rem);
        text-align: center;
    }

    #newsModal p {
        margin: 15px 0 0;
        color: #ccc;
        font-size: clamp(0.6rem, 1.5vw, 0.9rem);
    }

    #newsModal ul {
        margin: 5px 0 0;
        color: #ccc;
        font-size: clamp(0.6rem, 1.5vw, 0.9rem);
    }

    #newsModal li {
        margin: 5px 0 0;
        color: #ccc;
        font-size: clamp(0.6rem, 1.5vw, 0.9rem);
    }

    #newsModal img {
        max-width: 90%;
        margin-bottom: 15px;
        border-radius: 8px;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }

    #newsModal .close-btn {
        position: absolute;
        top: 10px;
        right: 15px;
        cursor: pointer;
        font-size: 1.2rem;
        color: #00ffff;
    }

    #newsModal::-webkit-scrollbar {
        width: 8px;
    }

    #newsModal::-webkit-scrollbar-track {
        background: transparent;
    }

    #newsModal::-webkit-scrollbar-thumb {
        background-color: rgba(0, 255, 255, 0.3);
        border-radius: 6px;
        border: 1px solid transparent;
    }

    #newsModal:hover::-webkit-scrollbar-thumb {
        background-color: rgba(116, 136, 136, 0.5);
    }

    #newsModal {
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
        font-size: clamp(1.8rem, 5vw, 2.8rem);
        text-shadow: 0 0 8px #facc15;
        color: rgb(240, 205, 65);
        margin-bottom: 1rem;
        line-height: 1.2;
        text-align: center;
    }
</style>

<div class="container">
    <h1 class="text-center text-white mb-4">🧙‍♂️ Berita Terbaru</h2>
        <div id="news-list"></div>
        <div id="pagination"></div>
</div>

<!-- Modal -->
<div class="overlay" id="overlay" onclick="hideModal()"></div>
<div id="newsModal">
    <div class="close-btn" onclick="hideModal()">✖</div>
    <h3 id="modalTitle"></h3>
    <img id="modalImage">
    <div id="modalContent" style="padding: 10px;"></div>
</div>
@endsection


<script>
    function escapeHTML(str) {
        return str.replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }

    function loadNews(page = 1) {
        fetch(`/news/list?page=${page}`)
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById('news-list');
                list.innerHTML = '';
                data.data.forEach(news => {
                    const item = document.createElement('div');
                    item.classList.add('news-item');
                    item.onclick = () => showModal(news.title, news.image, news.content);

                    item.innerHTML = `
                    <img src="${news.image}" class="news-image" alt="${news.title}">
                    <div class="news-content">
                        <h5>${news.title}</h5>
                        <p>${news.summary.substring(0, 100) ?? news.content.replace(/<[^>]+>/g, '').substring(0, 100)}...</p>
                        <p style="color:yellow;">${new Date(news.created_at).toLocaleDateString('id-ID', {
  day: 'numeric',
  month: 'long',
  year: 'numeric'
})}</p>
                    </div>
                `;
                    list.appendChild(item);
                });

                let pagination = '';
                for (let i = 1; i <= data.last_page; i++) {
                    pagination += `<button onclick="loadNews(${i})">${i}</button> `;
                }
                document.getElementById('pagination').innerHTML = pagination;
            });
    }

    function showModal(title, image, content) {
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalImage').src = image;
        document.getElementById('modalContent').innerHTML = content;
        document.getElementById('newsModal').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    }

    function hideModal() {
        document.getElementById('newsModal').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadNews();
    });
</script>