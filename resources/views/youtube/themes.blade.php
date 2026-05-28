@extends('layouts.app')

@section('content')

<style>
    /* 🌙 Mystic Nusa – Premium Themes UI (Mobile Optimized) */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    /* --- Global Styling --- */
    body {
        font-family: 'Inter', sans-serif;
        background-color: #050508;
        color: #e0e0f0;
        margin: 0;
        min-height: 100vh;
    }

    .themes-container {
        max-width: 1100px;
        margin: 50px auto;
        background: #0e0e12;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, .7), 0 0 0 1px rgba(85, 85, 120, 0.15);
    }

    /* --- Header Styling --- */
    .themes-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 35px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-title-group {
        display: flex;
        align-items: center;
    }

    .themes-header h2 {
        color: #f0f0ff;
        font-size: 32px;
        font-weight: 700;
        text-shadow: 0 0 15px rgba(120, 80, 255, .6);
        margin: 0;
        letter-spacing: 0.5px;
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

    /* --- Button Styling --- */
    .add-btn {
        background: linear-gradient(135deg, #7059ff, #5d44ff);
        border: none;
        color: #fff;
        padding: 12px 20px;
        border-radius: 14px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all .3s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3cb371, #2f945a);
    }

    /* --- List Card Styling --- */
    .themes-list-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .theme-card {
        background: #15151d;
        border: 1px solid #2d2d3a;
        border-radius: 18px;
        padding: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.2s, box-shadow 0.3s;
    }

    .theme-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
        border-color: #4a4a60;
    }

    .theme-title {
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .theme-meta {
        color: #b0b0cc;
        font-size: 14px;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 12px;
    }

    .theme-meta span i {
        margin-right: 6px;
        color: #8f6bff;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        margin-right: 8px;
    }

    .badge-unused {
        background: #2a2a3d;
        color: #a0a0c0;
    }

    .badge-used {
        background: rgba(47, 111, 78, 0.2);
        color: #b6ffd7;
        border: 1px solid #2f6f4e;
    }

    .card-actions {
        display: flex;
        gap: 10px;
    }

    .action-btn {
        background: #1a1a25;
        border: 1px solid #333;
        color: #ff6b6b;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .action-btn.edit-btn {
        color: #6bb6ff;
    }

    .action-btn:hover {
        background: #2d2d3a;
    }

    /* --- Modal Styling --- */
    .modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .85);
        backdrop-filter: blur(5px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
    }

    .modal {
        background: #11111a;
        padding: 30px;
        border-radius: 24px;
        width: 100%;
        max-width: 450px;
        border: 1px solid #333;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        /* Judul di kiri, tombol di kanan */
        align-items: center;
        /* Sejajar secara vertikal */
        margin-bottom: 20px;
        width: 100%;
        /* Pastikan mengambil lebar penuh modal */
    }

    .modal-header h3 {
        margin: 0;
        /* Hapus margin bawaan agar tidak merusak alignment */
        font-size: 20px;
    }

    .modal label {
        display: block;
        color: #a0a0c0;
        font-size: 13px;
        margin-bottom: 8px;
        margin-top: 15px;
    }

    .modal input {
        width: 100%;
        padding: 14px;
        border-radius: 12px;
        background: #09090d;
        border: 1px solid #2d2d3a;
        color: #fff;
        font-size: 16px;
    }

    /* --- Responsif Mobile --- */
    @media(max-width: 768px) {
        .themes-container {
            padding: 20px;
            margin: 10px;
            border-radius: 0;
        }

        .themes-header h2 {
            font-size: 24px;
        }

        .header-icon {
            font-size: 28px;
        }

        .header-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
        }

        .btn-primary {
            grid-column: span 2;
        }

        .add-btn {
            padding: 14px;
            width: 100%;
            font-size: 14px;
        }

        .themes-list-grid {
            grid-template-columns: 1fr;
        }

        .theme-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }

        .theme-meta {
            flex-direction: column;
            gap: 8px;
        }

        .card-actions {
            width: 100%;
            justify-content: flex-end;
            border-top: 1px solid #2d2d3a;
            padding-top: 15px;
        }

        .action-btn {
            width: 48px;
            height: 48px;
        }

        /* Target jari lebih besar */
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="themes-container">
    <div class="themes-header">
        <div class="header-title-group">
            <span class="header-icon"><i class="fas fa-palette"></i></span>
            <h2>Channel Themes</h2>
        </div>

        <div class="header-actions">
            <a href="/youtube-content-generator" class="action-btn-link">
                <button class="add-btn">
                    <i class="fas fa-cogs"></i> Config
                </button>
            </a>

            <a href="/youtube-content" class="action-btn-link">
                <button class="add-btn">
                    <i class="fas fa-list-alt"></i> Content
                </button>
            </a>

            <button class="add-btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus-circle"></i> Tambah Tema
            </button>
        </div>
    </div>

    <div id="themes-list" class="themes-list-grid">
    </div>
</div>

<div class="modal-bg" id="addThemeModal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-feather-alt"></i> Tema Baru</h3>
            <button class="modal-close-btn" onclick="closeAddModal()" style="background:none; border:none; color:#666; font-size:20px; cursor:pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <label>Nama Tema</label>
        <input id="themeInput" placeholder="e.g. Sejarah Kuno" />
        <label>Focus</label>
        <input id="focusInput" placeholder="e.g. Budaya, Sains" />
        <label>Tanggal / Periode</label>
        <input type="date" id="dateInput" />
        <button onclick="saveTheme()" class="add-btn" style="width:100%; margin-top:20px;">Simpan Tema</button>
    </div>
</div>

<div class="modal-bg" id="editThemeModal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Tema</h3>
            <button class="modal-close-btn" onclick="closeEditModal()" style="background:none; border:none; color:#666; font-size:20px; cursor:pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <input type="hidden" id="editThemeId">
        <label>Nama Tema</label>
        <input id="editThemeInput" />
        <label>Focus</label>
        <input id="editFocusInput" />
        <label>Tanggal</label>
        <input type="date" id="editDateInput">
        <button onclick="updateTheme()" class="add-btn" style="width:100%; margin-top:20px;">Update Tema</button>
    </div>
</div>

<script>
    // Logic JS Anda tetap sama 100%, tidak ada perubahan di sini
    const API_BASE = '/api/youtube';
    const Token = localStorage.getItem('token');
    let THEMES_LIST = [];
    const ADD_MODAL = document.getElementById('addThemeModal');
    const EDIT_MODAL = document.getElementById('editThemeModal');

    function formatDateForDisplay(dateString) {
        if (!dateString || dateString.length < 10) return '-';
        const date = new Date(dateString);
        return isNaN(date.getTime()) ? dateString : date.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function openAddModal() {
        ADD_MODAL.style.display = 'flex';
    }

    function closeAddModal() {
        ADD_MODAL.style.display = 'none';
    }

    function closeEditModal() {
        EDIT_MODAL.style.display = 'none';
    }

    function fetchThemes() {
        fetch(API_BASE + '/list-themes', {
                headers: {
                    'Authorization': 'Bearer ' + Token
                }
            })
            .then(r => r.json())
            .then(res => {
                const list = document.getElementById('themes-list');
                list.innerHTML = '';
                THEMES_LIST = res.data;

                if (THEMES_LIST.length === 0) {
                    list.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:50px; color:#666;"><i class="fas fa-box-open" style="font-size:40px; margin-bottom:15px; display:block;"></i> Kosong</div>`;
                    return;
                }

                THEMES_LIST.forEach(t => {
                    const usedBadge = t.used ? `<span class="badge badge-used"><i class="fas fa-check"></i> USED</span>` : `<span class="badge badge-unused"><i class="far fa-circle"></i> UNUSED</span>`;
                    const displayDate = formatDateForDisplay(t.date);
                    list.innerHTML += `
                <div class="theme-card">
                    <div class="theme-info">
                        <div class="theme-title">${t.theme}</div>
                        <div class="theme-meta">
                            <span><i class="fas fa-bullseye"></i> ${t.focus || '-'}</span>
                            <span><i class="far fa-calendar-alt"></i> ${displayDate}</span>
                        </div>
                        <div class="badges">${usedBadge}</div>
                    </div>
                    <div class="card-actions">
                        <button class="action-btn edit-btn" onclick="editTheme(${t.id})"><i class="fas fa-edit"></i></button>
                        <button class="action-btn" onclick="deleteTheme(${t.id})"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </div>`;
                });
            });
    }

    // Fungsi saveTheme, deleteTheme, editTheme, updateTheme sesuai logic asli Anda...
    function saveTheme() {
        fetch(API_BASE + '/insert-themes', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + Token,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                themes: document.getElementById('themeInput').value,
                focus: document.getElementById('focusInput').value,
                date: document.getElementById('dateInput').value
            })
        }).then(() => {
            closeAddModal();
            fetchThemes();
        });
    }

    function deleteTheme(id) {
        if (!confirm('Hapus tema ini?')) return;
        fetch(API_BASE + '/delete-themes/' + id, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + Token
            }
        }).then(() => fetchThemes());
    }

    function editTheme(id) {
        const t = THEMES_LIST.find(item => item.id === id);
        if (!t) return;
        document.getElementById('editThemeId').value = t.id;
        document.getElementById('editThemeInput').value = t.theme;
        document.getElementById('editFocusInput').value = t.focus || '';
        document.getElementById('editDateInput').value = t.date || '';
        EDIT_MODAL.style.display = 'flex';
    }

    function updateTheme() {
        const id = document.getElementById('editThemeId').value;
        fetch(API_BASE + '/edit-themes/' + id, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + Token,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                theme: document.getElementById('editThemeInput').value,
                focus: document.getElementById('editFocusInput').value,
                date: document.getElementById('editDateInput').value
            })
        }).then(() => {
            closeEditModal();
            fetchThemes();
        });
    }

    fetchThemes();
</script>

@endsection