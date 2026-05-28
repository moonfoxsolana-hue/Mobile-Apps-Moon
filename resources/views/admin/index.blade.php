@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
        background-color: #050508;
        color: #e0e0f0;
        margin: 0;
        height: 100vh;
        overflow: hidden;
    }

    .content-container {
        max-width: 1400px;
        height: calc(100vh - 60px);
        margin: 30px auto;
        background: #0e0e12;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, .7);
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
    }

    /* Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-shrink: 0;
    }

    .page-title {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .page-title h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }

    .header-controls {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Table Wrapper (Scrollable area) */
    .table-wrapper {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        border-radius: 8px;
        background: #0e0e12;
    }

    .table-wrapper::-webkit-scrollbar { width: 8px; }
    .table-wrapper::-webkit-scrollbar-track { background: #0e0e12; }
    .table-wrapper::-webkit-scrollbar-thumb { background: #2d2d3a; border-radius: 4px; }
    .table-wrapper::-webkit-scrollbar-thumb:hover { background: #8f6bff; }

    /* Table */
    .table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .table th {
        text-align: left;
        padding: 15px;
        border-bottom: 2px solid #2d2d3a;
        color: #8f6bff;
        text-transform: uppercase;
        font-size: 12px;
        font-weight: 600;
        background: #0e0e12;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table td {
        padding: 15px;
        border-bottom: 1px solid #2d2d3a;
        vertical-align: middle;
        word-wrap: break-word;
    }

    .row-main { cursor: pointer; transition: background 0.3s; }
    .row-main:hover { background: #15151d; }
    .row-detail { display: none; background: #111118; }

    /* Detail Panel */
    .detail-wrapper {
        padding: 25px;
        border-left: 4px solid #8f6bff;
        margin: 10px 0;
        box-sizing: border-box;
        overflow: hidden;
    }

    .detail-section { margin-bottom: 30px; }
    .section-title { font-size: 14px; font-weight: 600; color: #8f6bff; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Form Grid */
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 25px; }
    .form-group { display: flex; flex-direction: column; }
    .form-group.full-width { grid-column: span 2; }
    .form-group label { font-size: 12px; color: #b0b0cc; font-weight: 600; margin-bottom: 8px; }
    .form-group input, .form-group textarea, .form-group select {
        width: 100%; padding: 12px; background: #1a1a25; border: 1px solid #2d2d3a; border-radius: 8px; color: #fff; font-size: 14px; transition: border-color 0.3s; box-sizing: border-box;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: #8f6bff; }
    .form-group textarea { resize: vertical; min-height: 100px; }

    /* Media Gallery - Horizontal Scroll */
    .media-gallery {
        background: #0a0a0f; border-radius: 12px; padding: 20px; border: 1px solid #2d2d3a; width: 100%; box-sizing: border-box;
    }
    .media-scroll-container { display: flex; gap: 15px; overflow-x: auto; padding-bottom: 10px; scroll-behavior: smooth; width: 100%; }
    .media-scroll-container::-webkit-scrollbar { height: 8px; }
    .media-scroll-container::-webkit-scrollbar-track { background: #1a1a25; border-radius: 4px; }
    .media-scroll-container::-webkit-scrollbar-thumb { background: #8f6bff; border-radius: 4px; }
    .media-scroll-container::-webkit-scrollbar-thumb:hover { background: #9f7bff; }

    .media-item {
        min-width: 200px; max-width: 200px; background: #15151d; border-radius: 8px; overflow: hidden; border: 1px solid #2d2d3a; transition: transform 0.2s, border-color 0.2s; flex-shrink: 0;
    }
    .media-item:hover { transform: translateY(-2px); border-color: #8f6bff; }
    .media-item-image { width: 100%; height: 150px; object-fit: cover; background: #000; }
    .media-item-info { padding: 12px; }
    .media-item-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
    .media-order { font-size: 11px; font-weight: 600; color: #8f6bff; background: rgba(143, 107, 255, 0.2); padding: 3px 8px; border-radius: 4px; }
    
    /* Input Durasi Khusus Media */
    .media-duration-wrapper { display: flex; gap: 5px; margin-bottom: 8px; }
    .media-duration-input { 
        width: 100%; padding: 4px 8px; background: #1a1a25; border: 1px solid #2d2d3a; border-radius: 4px; color: #fff; font-size: 11px; 
    }
    .media-duration-input:focus { outline: none; border-color: #8f6bff; }

    /* Tombol Icon Kecil */
    .btn-icon { padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer; color: white; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s; }
    .btn-icon:hover { opacity: 0.8; }
    .btn-icon-danger { background: #dc3545; }
    .btn-icon-primary { background: #7059ff; }

    .media-prompt { font-size: 11px; color: #999; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .media-empty { padding: 40px; text-align: center; color: #666; width: 100%; }

    /* Buttons Umum */
    .btn { padding: 10px 18px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn:hover { opacity: 0.85; transform: translateY(-1px); }
    .btn-primary { background: #7059ff; color: white; }
    .btn-success { background: #28a745; color: white; }
    .btn-danger { background: #dc3545; color: white; }
    .btn-info { background: #17a2b8; color: white; }
    .btn-sm { padding: 6px 12px; font-size: 11px; }
    .form-actions { display: flex; gap: 10px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid #2d2d3a; }

    /* Badges & Content */
    .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .badge-purple { background: rgba(143, 107, 255, 0.2); color: #8f6bff; }
    .badge-success { background: rgba(40, 167, 69, 0.2); color: #28a745; }
    .badge-warning { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
    .content-title { font-weight: 600; margin-bottom: 5px; }

    /* Video Preview & Gambar */
    .video-preview { background: #000; border-radius: 8px; padding: 15px; text-align: center; }
    .video-preview video { max-width: 100%; height: 200px; border-radius: 4px; }
    .video-preview .no-video { color: #666; padding: 40px; font-size: 14px; }
    .image-preview { text-align: center; background: #1a1a25; padding: 15px; border-radius: 8px; border: 1px solid #2d2d3a; }
    .image-preview img { max-width: 200px; border-radius: 4px; }

    .loading-state, .error-state { text-align: center; padding: 40px; }
    .loading-state { color: #666; }
    .error-state { color: #dc3545; }
</style>

<div class="content-container">
    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-database" style="font-size: 24px; color: #8f6bff;"></i>
            <h2>Master Content AI</h2>
        </div>
        <div class="header-controls">
            <select id="typeSelector" onchange="loadContent()" style="padding: 10px; border-radius: 8px; background: #1a1a25; color: white; border: 1px solid #2d2d3a;">
                <option value="general">General</option>
                <option value="mimpi">Mimpi</option>
                <option value="biota">Biota</option>
            </select>
            <button class="btn btn-primary" onclick="loadContent()">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>Judul & Tema</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 150px;">Tanggal</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="master-list">
                <tr>
                    <td colspan="5" class="loading-state">
                        <i class="fas fa-spinner fa-spin"></i> Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
<script>
    const API_BASE = '/api/v1/admin/content';
    const Token = localStorage.getItem('token');

    // Load content list
    async function loadContent() {
        const type = document.getElementById('typeSelector').value;
        const tbody = document.getElementById('master-list');

        tbody.innerHTML = '<tr><td colspan="5" class="loading-state"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>';

        try {
            const response = await fetch(`${API_BASE}/${type}`, {
                headers: { 'Authorization': `Bearer ${Token}` }
            });

            if (!response.ok) throw new Error('Failed to fetch');

            const res = await response.json();
            renderContentList(res.data.data);
        } catch (err) {
            console.error('Load error:', err);
            tbody.innerHTML = '<tr><td colspan="5" class="error-state"><i class="fas fa-exclamation-triangle"></i> Gagal mengambil data. Silakan coba lagi.</td></tr>';
        }
    }

    // Render content list
    function renderContentList(items) {
        const tbody = document.getElementById('master-list');

        if (!items || items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="loading-state">Tidak ada data.</td></tr>';
            return;
        }

        tbody.innerHTML = items.map(item => `
            <tr class="row-main" onclick="toggleDetail(${item.id})">
                <td>#${item.id}</td>
                <td>
                    <div class="content-title">${escapeHtml(item.title || 'Untitled')}</div>
                    <span class="badge badge-purple">${escapeHtml(item.theme || 'No Theme')}</span>
                </td>
                <td>
                    ${item.youtube_upload_id ? 
                        '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Uploaded</span>' : 
                        '<span class="badge badge-warning"><i class="fas fa-clock"></i> Draft</span>'
                    }
                </td>
                <td>${formatDate(item.created_at)}</td>
                <td>
                    <button class="btn btn-info btn-sm">
                        <i class="fas fa-chevron-down"></i> Detail
                    </button>
                </td>
            </tr>
            <tr class="row-detail" id="detail-${item.id}">
                <td colspan="5">
                    <div class="detail-wrapper">
                        ${renderDetailPanel(item)}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    // Render detail panel
    function renderDetailPanel(item) {
        return `
            <form onsubmit="saveContent(event, ${item.id})">
                <div class="detail-section">
                    <h3 class="section-title"><i class="fas fa-edit"></i> Informasi Konten</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Judul Konten</label>
                            <input type="text" name="title" value="${escapeHtml(item.title || '')}" required>
                        </div>
                        <div class="form-group">
                            <label>Tema</label>
                            <input type="text" name="theme" value="${escapeHtml(item.theme || '')}">
                        </div>
                        <div class="form-group full-width">
                            <label>Narasi / Script</label>
                            <textarea name="content" rows="6">${escapeHtml(item.content || '')}</textarea>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h3 class="section-title"><i class="fas fa-image"></i> Thumbnail</h3>
                    <div class="form-group">
                        <div class="image-preview">
                            ${item.image_path ? 
                                `<img src="/${item.image_path}" alt="">` : 
                                '<div class="no-image" style="color: #666;"><i class="fas fa-image"></i><br>Tidak ada gambar</div>'
                            }
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h3 class="section-title"><i class="fas fa-video"></i> Video Preview</h3>
                    <div class="video-preview">
                        ${item.video_path ? 
                            `<video src="/cerita/video/${item.id}" controls></video>` : 
                            '<div class="no-video"><i class="fas fa-film"></i><br>Tidak ada video</div>'
                        }
                    </div>
                </div>

                <div class="detail-section">
                    <h3 class="section-title"><i class="fas fa-images"></i> Media Assets</h3>
                    <div class="media-gallery">
                        <div id="media-container-${item.id}" class="media-scroll-container">
                            <div class="media-empty"><i class="fas fa-spinner fa-spin"></i> Memuat media...</div>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>YouTube Upload ID</label>
                            <span class="badge badge-purple" style="font-size: 13px; padding: 10px;">${escapeHtml(item.youtube_upload_id || 'Belum ada ID')}</span>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <button type="button" class="btn btn-danger" onclick="deleteContent(${item.id})">
                            <i class="fas fa-trash"></i> Hapus Konten
                        </button>
                    </div>
                </div>
            </form>
        `;
    }

    // Toggle detail row and load media
    async function toggleDetail(id) {
        const type = document.getElementById('typeSelector').value;
        const detailRow = document.getElementById(`detail-${id}`);
        const allRows = document.querySelectorAll('.row-detail');

        const isCurrentlyVisible = detailRow.style.display === 'table-row';

        allRows.forEach(row => row.style.display = 'none');

        if (!isCurrentlyVisible) {
            detailRow.style.display = 'table-row';
            await loadMediaAssets(id, type);
        }
    }

    // Load media assets
    async function loadMediaAssets(id, type) {
        const container = document.getElementById(`media-container-${id}`);

        try {
            const response = await fetch(`${API_BASE}/${type}/${id}/media`, {
                headers: { 'Authorization': `Bearer ${Token}` }
            });

            if (!response.ok) throw new Error('Failed to fetch media');

            const res = await response.json();
            
            // Tambahkan parameter id content saat memanggil renderMediaAssets
            renderMediaAssets(container, res.data, id); 
        } catch (err) {
            console.error('Media load error:', err);
            container.innerHTML = '<div class="media-empty"><i class="fas fa-exclamation-triangle"></i> Gagal memuat media</div>';
        }
    }

    // Render media assets (DIPERBARUI DENGAN FITUR EDIT/HAPUS)
    function renderMediaAssets(container, mediaItems, contentId) {
        if (!mediaItems || mediaItems.length === 0) {
            container.innerHTML = '<div class="media-empty"><i class="fas fa-image"></i> Tidak ada media</div>';
            return;
        }

        container.innerHTML = mediaItems.map(media => `
            <div class="media-item">
                ${media.image_path ? 
                    `<img src="/${media.image_path}" alt="Media" class="media-item-image">` :
                    '<div class="media-item-image" style="display:flex;align-items:center;justify-content:center;color:#666;"><i class="fas fa-image fa-2x"></i></div>'
                }
                <div class="media-item-info">
                    <div class="media-item-header">
                        <span class="media-order">#${media.id}</span>
                        <button type="button" class="btn-icon btn-icon-danger" onclick="deleteMedia(${contentId}, ${media.id})" title="Hapus Gambar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    
                    <div class="media-duration-wrapper">
                        <input type="text" id="duration-${media.id}" class="media-duration-input" value="${media.duration_start || '0:00:00'}" placeholder="0:00:00">
                        <button type="button" class="btn-icon btn-icon-primary" onclick="updateMediaDuration(${contentId}, ${media.id})" title="Simpan Durasi">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                    
                    <p class="media-prompt" title="${escapeHtml(media.prompt || '')}">${escapeHtml(media.prompt || 'No prompt')}</p>
                </div>
            </div>
        `).join('');
    }

    // -------------------------------------------------------------
    // FUNGSI API BARU UNTUK MEDIA
    // -------------------------------------------------------------

    // Update Durasi Media
    async function updateMediaDuration(contentId, mediaId) {
        const type = document.getElementById('typeSelector').value;
        const durationValue = document.getElementById(`duration-${mediaId}`).value;

        try {
            // Asumsi Endpoint Backend: PUT /api/v1/admin/content/{type}/media/{mediaId}
            const response = await fetch(`${API_BASE}/${type}/media/${mediaId}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${Token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ duration_start: durationValue })
            });

            if (!response.ok) throw new Error('Failed to update duration');
            
            // Reload aset media tanpa mereload seluruh halaman
            await loadMediaAssets(contentId, type);
            // Opsional: Beri notifikasi kecil jika menggunakan framework (e.g. Toast)
        } catch (err) {
            console.error('Update media duration error:', err);
            alert('✗ Gagal menyimpan durasi media.');
        }
    }

    // Hapus Gambar Media
    async function deleteMedia(contentId, mediaId) {
        if (!confirm('Yakin ingin menghapus gambar media ini?')) return;

        const type = document.getElementById('typeSelector').value;

        try {
            // Asumsi Endpoint Backend: DELETE /api/v1/admin/content/{type}/media/{mediaId}
            const response = await fetch(`${API_BASE}/${type}/media/${mediaId}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${Token}` }
            });

            if (!response.ok) throw new Error('Failed to delete media');
            
            // Reload aset media tanpa mereload seluruh halaman
            await loadMediaAssets(contentId, type);
        } catch (err) {
            console.error('Delete media error:', err);
            alert('✗ Gagal menghapus media.');
        }
    }

    // -------------------------------------------------------------

    // Save content utama
    async function saveContent(e, id) {
        e.preventDefault();

        if (!confirm('Simpan perubahan konten?')) return;

        const type = document.getElementById('typeSelector').value;
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(`${API_BASE}/${type}/${id}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${Token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) throw new Error('Save failed');

            alert('✓ Berhasil diupdate!');
            loadContent();
        } catch (err) {
            console.error('Save error:', err);
            alert('✗ Gagal menyimpan. Silakan coba lagi.');
        }
    }

    // Delete content utama
    async function deleteContent(id) {
        if (!confirm('Yakin ingin menghapus konten ini selamanya? Tindakan ini tidak dapat dibatalkan.')) return;

        const type = document.getElementById('typeSelector').value;

        try {
            const response = await fetch(`${API_BASE}/${type}/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${Token}` }
            });

            if (!response.ok) throw new Error('Delete failed');

            alert('✓ Konten berhasil dihapus!');
            loadContent();
        } catch (err) {
            console.error('Delete error:', err);
            alert('✗ Gagal menghapus. Silakan coba lagi.');
        }
    }

    // Utility functions
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            year: 'numeric', month: 'short', day: 'numeric'
        });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', loadContent);
</script>
@endsection