<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Document Analyzer</title>
</head>
<style>
    :root {
        --color-primary: #4A90E2;
        /* Biru Elegan untuk Aksen */
        --color-background: #F4F7F9;
        /* Putih Gading Pucat */
        --color-card-bg: #FFFFFF;
        /* Putih Bersih untuk Kartu */
        --color-text-dark: #333333;
        /* Teks Gelap */
        --color-text-light: #6A7F93;
        /* Teks Sekunder/Placeholder */
        --color-border: #E0E6ED;
        /* Garis Tipis */
        --color-shadow: rgba(0, 0, 0, 0.05);
        /* Bayangan Halus */
        --color-hover: #3C7DC8;
        /* Hover untuk Biru */
    }

    /* Reset Dasar */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--color-background);
        color: var(--color-text-dark);
        line-height: 1.6;
        padding: 40px 20px;
    }

    /* Struktur Kontainer */
    .container {
        max-width: 900px;
        margin: 0 auto;
    }

    /* Header */
    header {
        text-align: center;
        margin-bottom: 50px;
    }

    header h1 {
        font-size: 2.5em;
        color: var(--color-text-dark);
        margin-bottom: 10px;
        font-weight: 300;
    }

    .highlight {
        font-weight: 600;
        color: var(--color-primary);
    }

    header p {
        font-size: 1.1em;
        color: var(--color-text-light);
    }

    /* Kartu Utama (Main Card) */
    .card {
        background-color: var(--color-card-bg);
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 10px 30px var(--color-shadow);
        margin-bottom: 40px;
        border-top: 5px solid var(--color-primary);
    }

    /* Area Unggah Dokumen */
    .upload-area {
        text-align: center;
        margin-bottom: 25px;
        padding: 30px;
        border: 2px dashed var(--color-border);
        border-radius: 8px;
        transition: border-color 0.3s ease;
    }

    .upload-area:hover {
        border-color: var(--color-primary);
    }

    .upload-button {
        display: inline-block;
        background-color: var(--color-primary);
        color: white;
        padding: 12px 25px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.3s ease, transform 0.1s ease;
        margin-bottom: 10px;
        user-select: none;
    }

    .upload-button:hover {
        background-color: var(--color-hover);
    }

    .upload-button:active {
        transform: scale(0.98);
    }

    .upload-button .icon {
        margin-right: 8px;
        font-size: 1.2em;
    }

    .file-info {
        font-size: 0.9em;
        color: var(--color-text-light);
        margin-top: 5px;
    }

    .file-name-display {
        margin-top: 15px;
        font-style: italic;
        color: var(--color-text-light);
        padding: 8px;
        background-color: var(--color-background);
        border-radius: 4px;
    }

    /* Tombol Aksi */
    .action-section {
        text-align: center;
        padding-top: 15px;
        border-top: 1px solid var(--color-border);
    }

    .analyze-button {
        background-color: #AAAAAA;
        /* Warna abu-abu saat dinonaktifkan */
        color: white;
        padding: 15px 40px;
        border: none;
        border-radius: 6px;
        font-size: 1.1em;
        font-weight: 600;
        cursor: not-allowed;
        transition: background-color 0.3s ease, transform 0.1s ease;
    }

    .analyze-button.ready {
        background-color: #2ECC71;
        /* Hijau saat siap */
        cursor: pointer;
    }

    .analyze-button.ready:hover {
        background-color: #27AE60;
    }

    .analyze-button.ready:active {
        transform: scale(0.98);
    }

    /* Bagian Placeholder Hasil */
    .results-placeholder h2 {
        font-size: 1.8em;
        color: var(--color-text-dark);
        margin-bottom: 15px;
        font-weight: 400;
    }

    .placeholder-box {
        background-color: var(--color-card-bg);
        padding: 30px;
        border-radius: 12px;
        border: 1px solid var(--color-border);
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .placeholder-box p {
        color: var(--color-text-light);
        font-style: italic;
    }
</style>

<body>

    <div class="container">
        <header>
            <h1><span class="highlight">Analisis</span> Dokumen AI</h1>
            <p>Unggah dokumen Anda untuk mendapatkan wawasan dan ringkasan mendalam.</p>
        </header>

        <main class="card">
            <div class="upload-area">
                <input type="file" id="documentUpload" hidden accept=".pdf,.doc,.docx,.txt,.jpg,.png" />
                <label for="documentUpload" class="upload-button">
                    <span class="icon">📁</span>
                    Klik atau Seret Dokumen ke Sini
                </label>
                <p class="file-info">Hanya file PDF, DOCX, TXT, JPG, dan PNG yang didukung.</p>
                <div id="fileNameDisplay" class="file-name-display">Belum ada dokumen yang diunggah.</div>
            </div>

            <div class="action-section">
                <button class="analyze-button" disabled id="analyzeBtn">Mulai Analisis</button>
            </div>
        </main>

        <section class="results-placeholder">
            <h2>Hasil Analisis AI</h2>
            <div class="placeholder-box">
                <p id="resultText">Hasil analisis akan ditampilkan di sini setelah Anda mengunggah dan memproses dokumen.</p>
            </div>
        </section>
    </div>

    <script>
        const fileInput = document.getElementById('documentUpload');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const analyzeBtn = document.getElementById('analyzeBtn');
        const resultText = document.getElementById('resultText');

        fileInput.addEventListener('change', (event) => {
            if (event.target.files.length > 0) {
                const fileName = event.target.files[0].name;
                fileNameDisplay.textContent = `Dokumen: ${fileName}`;
                analyzeBtn.disabled = false;
                analyzeBtn.classList.add('ready');
            } else {
                fileNameDisplay.textContent = 'Belum ada dokumen yang diunggah.';
                analyzeBtn.disabled = true;
                analyzeBtn.classList.remove('ready');
            }
        });

        analyzeBtn.addEventListener('click', async () => {
            if (fileInput.files.length === 0) return;

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);

            analyzeBtn.disabled = true;
            analyzeBtn.classList.remove('ready');
            analyzeBtn.textContent = 'Menunggu hasil...';
            resultText.textContent = 'Sedang memproses dokumen...';

            try {
                const formData = new FormData();
                formData.append('file', fileInput.files[0]);

                const response = await fetch('/api/analyze', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    const analysisData = data.data;
                    resultText.innerHTML = `
                        <div style="text-align: left;">
                            <div style="background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%); padding: 20px; border-radius: 12px; margin-bottom: 20px; color: white;">
                                <h3 style="margin: 0; font-size: 1.3em;">✓ Analisis Berhasil</h3>
                            </div>
                            
                            <div style="background-color: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 15px; border-left: 4px solid #4A90E2;">
                                <p style="margin: 0; color: #666;"><strong>Judul:</strong></p>
                                <p style="margin: 8px 0 0 0; color: #333; font-size: 1.05em;">${analysisData.title || '-'}</p>
                            </div>
                            
                            <div style="background-color: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 15px; border-left: 4px solid #4A90E2;">
                                <p style="margin: 0; color: #666;"><strong>Ringkasan Konten:</strong></p>
                                <p style="margin: 10px 0 0 0; color: #555; line-height: 1.7;">${analysisData.content?.substring(0, 300) || '-'}...</p>
                            </div>
                            
                            <div style="background-color: #f8fafc; padding: 20px; border-radius: 12px; border-left: 4px solid #4A90E2;">
                                <p style="margin: 0; color: #666;"><strong>Analisis Detail:</strong></p>
                                <p style="margin: 10px 0 0 0; color: #555; line-height: 1.7;">${analysisData.analysis?.replace(/\*\*([^*]+)\*\*/g, '⚡$1') || '-'}</p>
                            </div>
                            <div style="margin-top: 20px; padding: 15px; background-color: #e8f5e9; border-radius: 8px; border-left: 4px solid #2ECC71;">
                                <p style="margin: 0; color: #2E7D32;">
                                    🎧 <strong>Dengarkan Analisis:</strong>
                                </p>
                                <audio controls style="margin-top: 10px; width: 100%; max-width: 400px;">
                                    <source src="${analysisData.audio_path}" type="audio/mpeg">
                                    Browser Anda tidak mendukung elemen audio.
                                </audio>
                            </div>
                    `;
                } else {
                    resultText.textContent = `Error: ${data.message || 'Gagal menganalisis dokumen'}`;
                }
            } catch (error) {
                resultText.textContent = `Error: ${error.message}`;
            } finally {
                analyzeBtn.disabled = false;
                analyzeBtn.classList.add('ready');
                analyzeBtn.textContent = 'Mulai Analisis';
            }
        });
    </script>

</body>

</html>