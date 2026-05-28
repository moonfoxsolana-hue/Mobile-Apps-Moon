@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #121212;
        /* Hitam gelap */
        color: #e0e0e0;
        /* Abu muda untuk teks */
        font-family: Arial, sans-serif;
    }

    .ai-container {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        /* margin: 100px auto; */
        background-color: #1e1e1e;
        /* Hitam lebih terang */
        border: 1px solid #0a74da;
        /* Biru untuk border */
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        width: 100%;
        height: 100vh;
    }

    .btn-purple {
        background: #5a2ca0;
        border: none;
        color: white;
    }
    .btn-purple:hover {
        background: #7b3ed1;
        color: white;
    }
    .card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        background-color: #2c2c2c;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
    }

</style>

<div class="ai-container container py-5 text-white">

    <h2 class="mb-4">Mystic Nusa — AI PPT Generator</h2>

    <div class="card">
        <label>Masukkan Prompt</label></br>
        <textarea id="prompt" class="form-control p-200 mb-3 w-200" rows="4"
            placeholder="Contoh: Buatkan PPT 7 slide tentang Energi Halus Nusantara"></textarea><br>

        <button onclick="generateOutline()" class="btn btn-purple w-100 mt-3">Buat Outline</button>

        <div id="status" class="mt-3 text-warning"></div>
    </div>

    <div id="downloadArea" class="mt-4"></div>

</div>


<script>
    async function generateOutline() {
        const prompt = document.getElementById('prompt').value;
        document.getElementById('status').innerHTML = "Sedang membuat outline...";

        let res = await fetch('/api/outline', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                prompt
            })
        });

        let json = await res.json();
        if (json.success) {
            let id = json.presentation.id;
            document.getElementById('status').innerHTML = "Outline dibuat. Membuat PPT...";

            generatePpt(id);
        }
    }

    async function generatePpt(id) {
        let res = await fetch('/api/ppt/' + id);
        let json = await res.json();

        if (json.success) {
            document.getElementById('status').innerHTML = "";
            document.getElementById('downloadArea').innerHTML = `
                <div class='alert alert-success'>
                    PPT berhasil dibuat!<br>
                    <a href="${json.download_url}" class="btn btn-success mt-2">
                        Download PPT
                    </a>
                </div>
            `;
        }
    }
</script>

@endsection