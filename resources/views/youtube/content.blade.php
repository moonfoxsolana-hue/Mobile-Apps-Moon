@extends('layouts.app')

@section('content')
<style>
    /* ?? Mystic Nusa – YouTube Content List (Mobile Optimized & YouTube Flagging) */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    /* Global Setup */
    body {
        font-family: 'Inter', sans-serif;
        background-color: #050508;
        color: #e0e0f0;
        margin: 0;
        min-height: 100vh;
    }

    .content-container {
        max-width: 1200px;
        margin: 50px auto;
        background: #0e0e12;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, .7), 0 0 0 1px rgba(85, 85, 120, 0.15);
        border: none;
    }

    /* --- Modal Base CSS --- */
    .modal-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        display: none;
        /* Akan diubah ke flex lewat JS */
        justify-content: center;
        /* Menengah kan secara horizontal */
        align-items: center;
        /* Menengah kan secara vertikal */
        z-index: 1000;
        padding: 20px;
        /* Jarak aman agar modal tidak nempel layar saat di mobile */
    }

    /* 2. Batasi lebar kotak modal di sini */
    .modal {
        background: #0e0e12;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.9);
        position: relative;

        /* PENGATURAN LEBAR DI SINI */
        width: 100%;
        /* Biar responsif di layar kecil */
        max-width: 800px;
        /* Maksimal lebar 800px sesuai keinginan Anda */

        /* Animasi */
        transform: scale(0.9);
        transition: transform 0.3s ease-out;

        /* Agar konten panjang bisa di-scroll dalam modal */
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-bg[style*="display: flex"] .modal {
        transform: scale(1);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #2d2d3a;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }

    .modal-header h3 {
        color: #f0f0ff;
        margin: 0;
    }

    .modal-close-btn {
        background: none;
        border: none;
        color: #fff;
        font-size: 20px;
        cursor: pointer;
        opacity: 0.7;
    }

    /* --- Header & Buttons --- */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 35px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .header-title-group {
        display: flex;
        align-items: center;
    }

    .header-title-group h2 {
        color: #f0f0ff;
        font-size: 32px;
        font-weight: 700;
        text-shadow: 0 0 15px rgba(120, 80, 255, .6);
        margin: 0;
    }

    .header-icon {
        font-size: 36px;
        color: #8f6bff;
        margin-right: 15px;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .primary-btn {
        background: linear-gradient(135deg, #7059ff, #5d44ff);
        border: none;
        color: #fff;
        padding: 12px 20px;
        border-radius: 14px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all .3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-generate {
        background: linear-gradient(135deg, #28a745, #1e7e34);
    }

    /* --- Table & Badges --- */
    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .table td {
        background: #15151d;
        padding: 18px;
        color: #ddd;
        border: 1px solid #2d2d3a;
        font-size: small;
    }

    .badge {
        padding: 6px 14px;
        border-radius: 16px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-done {
        background: #2f6f4e;
        color: #b6ffd7;
    }

    .badge-progress {
        background: #3b3b55;
        color: #ddd;
    }

    .badge-uploaded {
        background: #065fd4;
        color: #fff;
    }

    /* YouTube Blue */

    .status-yt-tag {
        color: #ff4e4e;
        font-size: 11px;
        font-weight: 700;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* --- Responsive Mobile View (768px) --- */
    @media(max-width: 768px) {
        .content-container {
            padding: 20px;
            margin: 10px;
            border-radius: 12px;
        }

        .header-title-group h2 {
            font-size: 22px;
        }

        .header-actions {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .btn-generate {
            grid-column: span 2;
        }

        .primary-btn {
            justify-content: center;
            width: 100%;
            padding: 14px;
        }

        .table,
        .table thead,
        .table tbody,
        .table th,
        .table td,
        .table tr {
            display: block;
            font-size: small;
        }

        .table thead {
            display: none;
        }

        /* Hide headers on mobile */
        .table tr {
            margin-bottom: 15px;
            border-radius: 12px;
            overflow: hidden;
        }

        .table td {
            position: relative;
            padding: 12px 15px 12px 40% !important;
            border: none;
            border-bottom: 1px solid #2d2d3a;
            text-align: left;
        }

        .table td:before {
            position: absolute;
            left: 15px;
            font-weight: 700;
            color: #8f6bff;
            font-size: 11px;
            text-transform: uppercase;
        }

        .table td:nth-of-type(1):before {
            content: "Konten:";
        }

        .table td:nth-of-type(2):before {
            content: "Status:";
        }

        .table td:nth-of-type(3):before {
            content: "Waktu:";
        }

        .table td:nth-of-type(4) {
            padding: 15px !important;
            text-align: center;
        }

        .action-group {
            flex-direction: column;
            width: 100%;
        }

        .action-btn {
            width: 100%;
            justify-content: center;
            padding: 12px;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Additional Utility */
    .modal-body-scrollable {
        max-height: 70vh;
        overflow-y: auto;
        padding-right: 5px;
    }

    .narration-box {
        background: #1a1a25;
        padding: 15px;
        border-radius: 10px;
        min-height: 200px;
        white-space: pre-wrap;
        color: #ccc;
    }

    .path-icon-success {
        color: #3cb371;
    }

    .path-icon-fail {
        color: #ff6b6b;
    }

    /* --- Action Buttons in Table --- */
    .action-group {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        padding: 10px 15px;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        justify-content: center;
    }

    /* Tombol Detail (Biru Indigo) */
    .btn-detail {
        background: #3949ab;
        color: #fff;
    }

    .btn-detail:hover {
        background: #4759c5;
        transform: translateY(-1px);
    }

    /* Tombol Upload (Merah/YouTube Style) */
    .btn-upload {
        background: #e53935;
        color: #fff;
    }

    .btn-upload:hover:not(:disabled) {
        background: #ff524d;
        transform: translateY(-1px);
    }

    /* Gaya Tombol saat Disabled (Sudah Upload / Belum Ready) */
    .btn-upload:disabled {
        background: #252525;
        color: #666;
        cursor: not-allowed;
        opacity: 0.8;
    }

    /* Perbaikan Khusus Mobile (Agar tombol aksi terlihat bagus saat bertumpuk) */
    @media(max-width: 768px) {
        .action-group {
            flex-direction: column;
            width: 100%;
            gap: 10px;
            padding: 5px 0;
        }

        .action-btn {
            width: 100%;
            padding: 12px;
            /* Lebih besar untuk touch screen */
        }
    }

    /* Styling untuk Form Group (Jarak antar input) */
    .form-group {
        margin-bottom: 20px;
    }

    /* Styling Label */
    .form-group label {
        display: block;
        color: #b0b0cc;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }

    /* Styling Input Text dan Textarea */
    .form-group input[type="text"],
    .form-group textarea {
        width: 100%;
        padding: 14px;
        background: #1a1a25;
        /* Warna gelap senada modal */
        border: 1px solid #2d2d3a;
        border-radius: 12px;
        color: #fff;
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        box-sizing: border-box;
        /* Agar padding tidak merusak lebar */
        transition: all 0.3s ease;
    }

    /* Efek Focus saat input diklik */
    .form-group input[type="text"]:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #8f6bff;
        box-shadow: 0 0 0 3px rgba(143, 107, 255, 0.2);
        background: #1e1e2e;
    }

    /* Khusus Textarea agar bisa di-resize tapi tetap rapi */
    .form-group textarea {
        min-height: 150px;
        resize: vertical;
        line-height: 1.6;
    }

    /* Button Simpan (Jika belum ada class primary-btn di halaman ini) */
    .btn-save-edit {
        background: linear-gradient(135deg, #7059ff, #5d44ff);
        color: white;
        border: none;
        padding: 15px;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 10px;
        transition: transform 0.2s;
    }

    .btn-save-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(93, 68, 255, 0.4);
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="content-container">
    <div class="header">
        <div class="header-title-group">
            <span class="header-icon"><i class="fas fa-video"></i></span>
            <h2>Mystic Nusa</h2>
        </div>
        <div class="header-actions">
            <a href="/youtube-content-generator" class="action-btn-link">
                <button class="primary-btn"><i class="fas fa-cogs"></i> Config</button>
            </a>
            <a href="/youtube-list-themes" class="action-btn-link">
                <button class="primary-btn"><i class="fas fa-palette"></i> Themes</button>
            </a>
            <button class="primary-btn btn-generate" onclick="generateContent()">
                <i class="fas fa-hammer"></i> Generate
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 45%;">Judul & Tema</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 20%;">Dibuat</th>
                    <th style="width: 20%; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="content-list"></tbody>
        </table>
    </div>
</div>

<div class="modal-bg" id="contentDetailModal">
    <div class="modal modal-large">
        <div class="modal-header">
            <h3 id="detailTitle">Detail Konten</h3>
            <button class="modal-close-btn" onclick="closeDetailModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body-scrollable">
            <div class="detail-grid">
                <div class="detail-metadata">
                    <h4>Informasi</h4>
                    <p><strong>ID:</strong> <span id="detailId"></span></p>
                    <p><strong>Tema:</strong> <span id="detailTheme"></span></p>
                    <p><strong>Dibuat:</strong> <span id="detailCreatedAt"></span></p>
                    <div id="detailStatusBadge" class="badge" style="margin-top: 10px;"></div>

                    <h4 style="margin-top:20px;">File Status</h4>
                    <p class="status-path-info">Audio: <i id="audioIcon"></i></p>
                    <p class="status-path-info">Subtitle: <i id="subtitleIcon"></i></p>
                    <p class="status-path-info">Video: <i id="videoIcon"></i></p>
                    <p class="status-path-info">YouTube Upload ID: <span id="detailyoutubeuploadid"></span> <i id="youtubeuploadidIcon"></i></p>

                    <div class="media-player-container" id="mediaPlayerContainer"></div>

                    <div class="action-group-detail" style="display:flex; flex-direction:column; gap:8px; margin-top:20px;">
                        <button class="primary-btn" style="background:#e6c200; color:#000" id="btnEditFast"><i class="fas fa-edit"></i> Edit Narasi</button>
                        <button class="primary-btn" id="btnRegenFast"><i class="fas fa-redo"></i> Re-Generate Video</button>
                        <button class="primary-btn btn-upload-detail" id="btnUploadFast"><i class="fas fa-upload"></i> Upload Sekarang</button>
                    </div>
                </div>
                <div class="detail-content-narration">
                    <h4>Narasi</h4>
                    <div id="detailContentNarration" class="narration-box"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-bg" id="editContentModal">
    <div class="modal modal-small">
        <div class="modal-header">
            <h3>Edit Konten</h3>
            <button class="modal-close-btn" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body-scrollable">
            <form id="editContentForm">
                <input type="hidden" id="editContentId">
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" id="editTitle" maxlength="80">
                </div>
                <div class="form-group">
                    <label>Narasi</label>
                    <textarea id="editContent" maxlength="5000"></textarea>
                </div>
                <button type="submit" class="primary-btn btn-save-edit" style="width:100%"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
</div>

<script>
    const API = '/api/youtube';
    const Token = localStorage.getItem('token');
    const DETAIL_MODAL = document.getElementById('contentDetailModal');
    const EDIT_MODAL = document.getElementById('editContentModal');

    document.addEventListener('DOMContentLoaded', () => {
        loadContent();
        setupModalClose(DETAIL_MODAL, closeDetailModal);
        setupModalClose(EDIT_MODAL, closeEditModal);
    });

    function setupModalClose(el, fn) {
        el.addEventListener('click', e => {
            if (e.target === el) fn();
        });
    }

    function loadContent() {
        fetch(API + '/list-content', {
                headers: {
                    Authorization: 'Bearer ' + Token
                }
            })
            .then(r => r.json())
            .then(res => {
                const tbody = document.getElementById('content-list');
                tbody.innerHTML = '';
                window.contentData = res.data;

                if (!res.data.length) {
                    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center">Belum ada konten.</td></tr>';
                    return;
                }

                res.data.forEach(c => {
                    const isUploaded = !!c.youtube_upload_id;
                    const isProcessing = (c.video_path || c.audio_path) && !c.is_complete;

                    // Inisialisasi status default (Draft)
                    let status = {
                        text: 'Draft',
                        class: 'badge-progress',
                        icon: 'fas fa-clock'
                    };

                    if (isUploaded) {
                        status = {
                            text: 'Uploaded',
                            class: 'badge-uploaded',
                            icon: 'fab fa-youtube'
                        };
                    } else if (c.is_complete) {
                        status = {
                            text: 'Ready',
                            class: 'badge-done',
                            icon: 'fas fa-check-circle'
                        };
                    } else if (isProcessing) {
                        status = {
                            text: 'Processing',
                            class: 'badge-progress',
                            icon: 'fas fa-spinner fa-spin' // Ikon berputar
                        };
                    }

                    const createdDate = new Date(c.created_at).toLocaleString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    tbody.innerHTML += `
                <tr>
                    <td>
                        <div style="font-weight:600; color:#fff">${c.title || 'Untitled'}</div>
                        <div class="content-theme-info">Tema: ${c.theme || '-'}</div>
                        ${isUploaded ? `<div class="status-yt-tag"><i class="fab fa-youtube"></i> LIVE ON YOUTUBE</div>` : ''}
                    </td>
                    <td><span class="badge ${status.class}"><i class="${status.icon}"></i> ${status.text}</span></td>
                    <td>${createdDate}</td>
                    <td>
                        <div class="action-group">
                            <button class="action-btn btn-detail" onclick="viewDetail(${c.id})"><i class="fas fa-info-circle"></i> Detail</button>
                            <button class="action-btn btn-upload" ${(!c.is_complete || isUploaded) ? 'disabled' : ''} onclick="uploadContent(${c.id})">
                                <i class="${isUploaded ? 'fas fa-check-double' : 'fas fa-upload'}"></i> ${isUploaded ? 'Uploaded' : 'Upload'}
                            </button>
                        </div>
                    </td>
                </tr>`;
                });
            });
    }

    function viewDetail(id) {
        const c = window.contentData.find(item => item.id == id);
        const isUploaded = !!c.youtube_upload_id;
        const isProcessing = (c.video_path || c.audio_path) && !c.is_complete;
        document.getElementById('detailId').textContent = c.id;
        document.getElementById('detailTheme').textContent = c.theme;
        document.getElementById('detailCreatedAt').textContent = new Date(c.created_at).toLocaleString();
        document.getElementById('detailContentNarration').textContent = c.content || 'Kosong';

        // Status & Flagging Logic
        const badge = document.getElementById('detailStatusBadge');
        const upBtn = document.getElementById('btnUploadFast');

        if (isUploaded) {
            badge.className = 'badge badge-uploaded';
            badge.innerHTML = '<i class="fab fa-youtube"></i> Uploaded to YouTube';
        } else if (c.is_complete) {
            badge.className = 'badge badge-done';
            badge.innerHTML = '<i class="fas fa-check-circle"></i> Ready to Upload';
        } else if (isProcessing) {
            badge.className = 'badge badge-progress';
            badge.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Video...';
        } else {
            badge.className = 'badge badge-progress';
            badge.innerHTML = '<i class="fas fa-clock"></i> Draft / Pending';
        }

        // YT ID Flag
        const ytId = document.getElementById('detailyoutubeuploadid');
        const ytIcon = document.getElementById('youtubeuploadidIcon');
        ytId.textContent = c.youtube_upload_id || 'None';
        ytIcon.className = isUploaded ? 'fas fa-check-circle path-icon-success' : 'fas fa-times-circle path-icon-fail';

        // Icons for paths
        document.getElementById('audioIcon').className = c.audio_path ? 'fas fa-check-circle path-icon-success' : 'fas fa-times-circle path-icon-fail';
        document.getElementById('subtitleIcon').className = c.subtitles_path ? 'fas fa-check-circle path-icon-success' : 'fas fa-times-circle path-icon-fail';
        document.getElementById('videoIcon').className = c.video_path ? 'fas fa-check-circle path-icon-success' : 'fas fa-times-circle path-icon-fail';

        // Media Preview
        const container = document.getElementById('mediaPlayerContainer');
        container.innerHTML = c.video_path ? `<video controls src="/content/video/${c.id}" style="width:100%; border-radius:8px; margin-top:10px;"></video>` : c.audio_path ? `<audio controls src="/content/audio/${c.id}" style="width:100%; border-radius:8px; margin-top:10px;"></audio>` : '<p>No preview</p>';

        // Fast Action Buttons
        document.getElementById('btnEditFast').onclick = () => showEditModal(id);
        document.getElementById('btnRegenFast').onclick = () => regenerateContentApi(id);
        upBtn.onclick = () => uploadContent(id);

        DETAIL_MODAL.style.display = 'flex';
    }

    function showEditModal(id) {
        const c = window.contentData.find(item => item.id == id);
        document.getElementById('editContentId').value = c.id;
        document.getElementById('editTitle').value = c.title;
        document.getElementById('editContent').value = c.content;
        closeDetailModal();
        EDIT_MODAL.style.display = 'flex';
    }

    function closeDetailModal() {
        // 1. Cari elemen audio & video di dalam container modal
        const videoElement = DETAIL_MODAL.querySelector('video');
        const audioElement = DETAIL_MODAL.querySelector('audio');

        // 2. Jika audio/video ditemukan, hentikan pemutaran
        if (videoElement) {
            videoElement.pause();
            videoElement.currentTime = 0; // Opsional: kembalikan ke detik 0
        }
        if (audioElement) {
            audioElement.pause();
            audioElement.currentTime = 0; // Opsional: kembalikan ke detik 0
        }


        // 3. Sembunyikan modal
        DETAIL_MODAL.style.display = 'none';

        // Opsional: Kosongkan container agar memori bersih
        // document.getElementById('mediaPlayerContainer').innerHTML = '';
    }

    function closeEditModal() {
        EDIT_MODAL.style.display = 'none';
    }

    // Forms & API Calls
    document.getElementById('editContentForm').onsubmit = e => {
        e.preventDefault();
        const id = document.getElementById('editContentId').value;
        fetch(`${API}/edit-content/${id}`, {
            method: 'POST',
            headers: {
                Authorization: 'Bearer ' + Token,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                title: document.getElementById('editTitle').value,
                content: document.getElementById('editContent').value
            })
        }).then(() => {
            alert('Updated!');
            closeEditModal();
            loadContent();
        });
    };

    function generateContent() {
        if (!confirm('Mulai generate?')) return;
        fetch(API + '/generate-content', {
                method: 'POST',
                headers: {
                    Authorization: 'Bearer ' + Token
                }
            })
            .then(() => {
                alert('Started!');
                loadContent();
            });
    }

    function uploadContent(id) {
        if (!confirm('Upload ke YouTube?')) return;
        fetch(API + '/upload-content', {
            method: 'POST',
            headers: {
                Authorization: 'Bearer ' + Token,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                content_id: String(id)
            })
        }).then(() => {
            alert('Upload process started!');
            loadContent();
        });
    }

    function regenerateContentApi(id) {
        if (!confirm('Re-generate video?')) return;
        fetch(`${API}/regenerate-content/${id}`, {
                method: 'POST',
                headers: {
                    Authorization: 'Bearer ' + Token
                }
            })
            .then(() => {
                alert('Re-gen started!');
                closeDetailModal();
                loadContent();
            });
    }
</script>
@endsection