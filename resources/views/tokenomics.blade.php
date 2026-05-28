@extends('layouts.app')

@section('content')
<style>
    @keyframes glow {

        0%,
        100% {
            filter: drop-shadow(0 0 6px #d6baff);
        }

        50% {
            filter: drop-shadow(0 0 12px #a976ff);
        }
    }

    .token-info {
        color: #e5d6ff;
        padding: 2rem 1rem;
    }

    .token-info h2 {
        font-size: clamp(1rem, 3vw, 1.8rem);
        text-shadow: 0 0 8px #facc15;
        color: rgb(240, 205, 65);
        margin-bottom: 1rem;
        line-height: 1.2;
        text-align: center;
    }

    .token-info .token-logo {
        display: block;
        margin: 0 auto 2rem;
        width: clamp(80px, 20vw, 120px);
        height: auto;
        animation: glow 4s ease-in-out infinite;
        transition: filter 0.3s ease, transform 0.3s ease;
    }

    .token-info .token-logo:hover {
        transform: scale(1.2);
        filter: drop-shadow(0 0 15px rgba(255, 255, 150, 0.8)) drop-shadow(0 0 30px rgba(255, 200, 100, 0.6));
    }

    .token-tagline {
        text-align: center;
        margin-top: 3rem;
        font-style: italic;
        font-size: 1.1rem;
        color: #dbcaff;
    }

    .token-table-wrapper {
        max-width: 800px;
        margin: auto;
        border-radius: 12px;
        overflow: hidden;
    }

    .token-table {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        border-collapse: collapse;
        background: #1e293b;
        border-radius: 8px;
        overflow: hidden;
        font-size: 1rem;
    }

    .token-table th,
    .token-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #3c2a4f;
        background: rgba(255, 255, 255, 0.0);
    }

    .token-table th {
        padding: 8px;
        text-align: left;
        color: white;
        color: #fbbf24;
        vertical-align: top;
    }

    .token-table td {
        padding: 8px;
        text-align: left;
        color: white;
        word-break: break-word;
    }

    .token-table a {
        color: #a684ff;
        text-decoration: underline;
        word-break: break-word;
    }

    /* Responsive */
    @media (max-width: 600px) {

        .token-table th,
        .token-table td {
            background: rgba(255, 255, 255, 0.03);
            display: block;
            width: 100%;
        }

        .token-table th {
            border-bottom: none;
            font-size: 0.95rem;
        }

        .token-table td {
            border-bottom: 1px solid #3c2a4f;
        }
    }

    .pie-container {
        max-width: 1000px;
        margin: 3rem auto;
        text-align: center;
        color: white;
    }

    .pie {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: conic-gradient(#a1c4fd 0% 25%,
                /* Sky Whisper */
                #c2e9fb 25% 45%,
                /* Crystal Mist */
                #8ec5fc 45% 60%,
                /* Calm Ocean */
                #736ced 60% 75%,
                /* Aether Indigo */
                #3d348b 75% 85%,
                /* Space Aura */
                #5ddcff 85% 95%,
                /* Mist Light Cyan */
                #2b5876 95% 100%
                /* Dusk Blue */
            );
        margin: 0 auto 1.5rem;
        box-shadow: 0 0 30px rgba(147, 94, 255, 0.5);
        position: relative;
    }

    .legend {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        font-size: 0.95rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    } */

    .legend-color {
        display: inline-block;
        width: 16px;
        height: 16px;
        border-radius: 4px;
        margin-right: 5px;
    }

    .bg-skywhisper {
        background-color: #a1c4fd;
    }

    .bg-crystalmist {
        background-color: #c2e9fb;
    }

    .bg-oceanbloom {
        background-color: #8ec5fc;
    }

    .bg-violetstorm {
        background-color: #736ced;
    }

    .bg-royalember {
        background-color: #3d348b;
    }

    .bg-glowmist {
        background-color: #5ddcff;
    }

    .bg-deepsteel {
        background-color: #2b5876;
    }

    .bg-pearlveil {
        background-color: #cbd5e1;
    }

    .bg-moonstone {
        background-color: #8E8C84;
    }

    .timeline-container {
        max-width: 1000px;
        margin: 4rem auto;
        padding: 2rem;
        border-radius: 16px;
        background: rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(147, 197, 253, 0.08);
        box-shadow: 0 0 30px rgba(113, 173, 255, 0.06);
    }

    .timeline-title {
        text-align: center;
        font-size: 1.9rem;
        font-weight: bold;
        margin-bottom: 2rem;
        color: #b9d1ff;
        text-shadow: 0 0 10px #6c83ff80;
    }

    .unlock-item {
        margin-bottom: 1.5rem;
    }

    .unlock-label {
        display: flex;
        /* justify-content: center; */
        align-items: center;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.3rem;
        color: #d0e4ff;
    }

    .unlock-bar {
        height: 18px;
        background: linear-gradient(90deg, #2b5876, #4e4376);
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 0 8px rgba(113, 173, 255, 0.3);
    }

    .unlock-fill {
        height: 100%;
        background: linear-gradient(90deg, #89f7fe, #66a6ff);
        box-shadow: 0 0 10px rgba(89, 246, 255, 0.5);
        animation: mist-glow 3s ease-in-out infinite alternate;
    }

    @keyframes mist-glow {
        0% {
            filter: brightness(1);
        }

        100% {
            filter: brightness(1.4);
        }
    }

    .unlock-percent {
        font-size: 0.85rem;
        color: #a0bfe9;
        margin-top: 4px;
    }

    @media (max-width: 640px) {
        .timeline-container {
            padding: 1.5rem 1rem;
        }

        .unlock-label {
            font-size: 0.9rem;
        }

        .unlock-percent {
            font-size: 0.8rem;
        }
    }

    #unlock-progress {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        padding: 1rem;
    }

    .scroll-wrapper {
        overflow-x: auto;
        overflow-y: hidden;
        width: 100%;
        padding-bottom: 4px;
        scrollbar-width: thin;
        scrollbar-color: rgb(140, 192, 255) transparent;
        /* Firefox */
    }

    .scroll-wrapper::-webkit-scrollbar {
        height: 6px;
    }

    .scroll-wrapper::-webkit-scrollbar-track {
        background: transparent;
    }

    .scroll-wrapper::-webkit-scrollbar-thumb {
        background-color: #d4af37;
        border-radius: 10px;
        border: 2px solid transparent;
        background-clip: content-box;
    }

    .scroll-content {
        min-width: 800px;
        /* atur sesuai lebar chart */
        margin: 0 auto;
    }

    .mystic-tokenomics-table {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        border-collapse: collapse;
        background: #1e293b;
        border-radius: 8px;
        overflow: hidden;
        font-size: 1rem;
    }

    /* Header */
    .mystic-tokenomics-table thead tr {
        background: #374151;
    }

    .mystic-tokenomics-table th,
    .mystic-tokenomics-table td {
        padding: 8px;
        text-align: left;
        color: white;
    }

    /* Specific column */
    .mystic-tokenomics-table th {
        color: #fbbf24;
    }

    /* Border row separation */
    .mystic-tokenomics-table td {
        border-top: 1px solid #334155;
    }

    @media (max-width: 500px) {
        .mystic-tokenomics-table {
            font-size: 0.75rem;
            max-width: 100%;
        }

        .mystic-tokenomics-table th,
        .mystic-tokenomics-table td {
            padding: 5px;
        }
    }

    .progress-section {
        max-width: 600px;
        margin: 2rem auto;
        padding: 1.5rem;
        border: 1px solid #4b3b6b;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.04);
        box-shadow: 0 0 20px rgba(151, 97, 255, 0.15);
    }

    /* Button */
    .btn-progress-toggle {
        width: 100%;
        padding: 0.75rem 1rem;
        background: #8f6eff;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-progress-toggle:hover {
        background: #a78fff;
    }

    .btn-progress-toggle:disabled {
        opacity: 0.8;
        cursor: not-allowed;
    }

    /* Spinner Loader */
    .spinner {
        border: 4px solid rgba(255, 255, 255, 0.2);
        border-top: 4px solid #ffffff;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        margin: 1rem auto;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Progress List */
    .progress-list {
        margin-top: 1.5rem;
    }

    /* Progress Item */
    .progress-item {
        margin-bottom: 1.5rem;
    }

    .progress-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 1rem;
        color: #e0d4ff;
    }

    .progress-bar {
        width: 100%;
        height: 14px;
        background-color: #2a1a42;
        border-radius: 12px;
        overflow: hidden;
        /* Penting untuk menjaga fill di dalam! */
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.5);
    }

    .progress-fill {
        height: 100%;
        width: 0%;
        transition: width 1s ease-in-out;
        background: linear-gradient(90deg, #89f7fe, #66a6ff);
        box-shadow: 0 0 10px rgba(89, 246, 255, 0.5);
        animation: mist-glow 3s ease-in-out infinite alternate;
        border-radius: 12px 0 0 12px;
    }
</style>
<div class="token-info">


    <div class="timeline-container">
        <img src="{{ asset('images/koin.png') }}" alt="Mystic Nusa Logo" class="token-logo">

        <h2>Informasi Token – Mystic Nusa ($MYNU)</h2>

        <div class="token-table-wrapper">
            <table class="token-table">
                <tr>
                    <th>Nama Token</th>
                    <td>Mystic Nusa</td>
                </tr>
                <tr>
                    <th>Simbol</th>
                    <td>$MYNU</td>
                </tr>
                <tr>
                    <th>Blockchain</th>
                    <td>Solana</td>
                </tr>
                <tr>
                    <th>Token Standard</th>
                    <td>SPL Token</td>
                </tr>
                <tr>
                    <th>Alamat Contract</th>
                    <td>
                        <a href="https://solana.fm/address/2F2jZge67xrD4xhH2eVXyBqhVkrUfdvfmeTyDT57tt25?cluster=mainnet-alpha" target="_blank">
                            2F2jZge67xrD4xhH2eVXyBqhVkrUfdvfmeTyDT57tt25
                        </a>
                    </td>
                </tr>
                <tr>
                    <th>Total Supply</th>
                    <td>400.000.000 MYNU</td>
                </tr>
                <tr>
                    <th>Desimal</th>
                    <td>6</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>Aktif — On progress listing DEX</td>
                </tr>
                <tr>
                    <th>Utilitas</th>
                    <td>Trading, Staking, Airdrop, GameFi, merch Swap, Voting komunitas</td>
                </tr>
            </table>

            </br>
            <h2>Distribusi Tokenomics</h2>
            <table class="mystic-tokenomics-table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th style="text-align: right;">Alokasi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>🎁 Airdrop Komunitas</td>
                        <td style="text-align: right;">20%</td>
                    </tr>
                    <tr>
                        <td>🔒 Staking Reward</td>
                        <td style="text-align: right;">10%</td>
                    </tr>
                    <tr>
                        <td>🧙 Reward Holder Program</td>
                        <td style="text-align: right;">15%</td>
                    </tr>
                    <tr>
                        <td>🎮 Mini Games & Utilitas</td>
                        <td style="text-align: right;">5%</td>
                    </tr>
                    <tr>
                        <td>💰 Likuiditas DEX & CEX</td>
                        <td style="text-align: right;">15%</td>
                    </tr>
                    <tr>
                        <td>🌱 Ekosistem & Pengembangan</td>
                        <td style="text-align: right;">10%</td>
                    </tr>
                    <tr>
                        <td>💰📈 Marketing & Kemitraan</td>
                        <td style="text-align: right;">10%</td>
                    </tr>
                    <tr>
                        <td>🧙 Tim & Kontributor Awal</td>
                        <td style="text-align: right;">10%</td>
                    </tr>
                    <tr>
                        <td>🏛️ Treasury & Governance</td>
                        <td style="text-align: right;">5%</td>
                    </tr>
                </tbody>
            </table>
            <div class="pie-container">
                <div style="max-width: 240px; min-width:100px;margin: auto;">
                    <canvas id="pieChart"></canvas>
                </div>
                </br>
                <h2>Timeline Vesting</h2>
                <div class="scroll-wrapper">
                    <div class="scroll-content" style="min-width: 500px;max-width: 700px;">
                        <canvas id="vestingChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- @php
            $data = [
            ['label' => 'Airdrop Komunitas', 'percent' => 20, 'bg-color' => 'bg-skywhisper'],
            ['label' => 'Staking Reward', 'percent' => 10, 'bg-color' => 'bg-crystalmist'],
            ['label' => 'Reward Holder Program', 'percent' => 15, 'bg-color' => 'bg-oceanbloom'],
            ['label' => 'Mini Games & Utilitas ', 'percent' => 5, 'bg-color' => 'bg-violetstorm'],
            ['label' => 'Likuiditas DEX & CEX', 'percent' => 15, 'bg-color' => 'bg-royalember'],
            ['label' => 'Ekosistem & Pengembangan', 'percent' => 10, 'bg-color' => 'bg-glowmist'],
            ['label' => 'Marketing & Kemitraan', 'percent' => 10, 'bg-color' => 'bg-deepsteel'],
            ['label' => 'Tim & Kontributor ', 'percent' => 10, 'bg-color' => 'bg-pearlveil'],
            ['label' => 'Treasury & Governance', 'percent' => 5, 'bg-color' => 'bg-moonstone'],
            ];
            @endphp

            @foreach($data as $item)
            <div class="unlock-item">
                <div class="unlock-label"><span class="legend-color {{ $item['bg-color'] }}"></span>{{ $item['label'] }}</div>
                <div class="unlock-bar">
                    <div class="unlock-fill" style="width: {{ $item['percent'] }}%;"></div>
                </div>
                <div class="unlock-percent">{{ $item['percent'] }}% dari total supply</div>
            </div>
            @endforeach -->
            <div id="unlock-items-container">
            </div>
            <h2>📈 Token Unlock Progress</h2>
            <div class="progress-section">
                <button id="loadProgressBtn" class="btn-progress-toggle">
                    📊 Lihat Progress
                </button>
                <div id="progress-loading" class="spinner" style="display: none;"></div>
                <div id="progress-list" class="progress-list"></div>
                <!-- <div class="progress-fill" style="background-color: ${item.warna || '#8f6eff'}; width: 0"></div> -->

            </div>

        </div>
    </div>


    <script>
        const dataunlock = [{
                'label': 'Airdrop Komunitas',
                'percent': 20,
                'bg-color': 'bg-skywhisper'
            },
            {
                'label': 'Staking Reward',
                'percent': 10,
                'bg-color': 'bg-crystalmist'
            },
            {
                'label': 'Reward Holder Program',
                'percent': 15,
                'bg-color': 'bg-oceanbloom'
            },
            {
                'label': 'Mini Games & Utilitas ',
                'percent': 5,
                'bg-color': 'bg-violetstorm'
            },
            {
                'label': 'Likuiditas DEX & CEX',
                'percent': 15,
                'bg-color': 'bg-royalember'
            },
            {
                'label': 'Ekosistem & Pengembangan',
                'percent': 10,
                'bg-color': 'bg-glowmist'
            },
            {
                'label': 'Marketing & Kemitraan',
                'percent': 10,
                'bg-color': 'bg-deepsteel'
            },
            {
                'label': 'Tim & Kontributor ',
                'percent': 10,
                'bg-color': 'bg-pearlveil'
            },
            {
                'label': 'Treasury & Governance',
                'percent': 5,
                'bg-color': 'bg-moonstone'
            },
        ];

        // 2. Dapatkan elemen kontainer tempat item akan ditampilkan
        const container = document.getElementById('unlock-items-container');

        // 3. Lakukan perulangan melalui array data
        dataunlock.forEach(item => {
            // 4. Buat string HTML untuk setiap item menggunakan template literal
            const htmlContent = `
    <div class="unlock-item">
      <div class="unlock-label">
        <span class="legend-color ${item['bg-color']}"></span>
        ${item.label}
      </div>
      <div class="unlock-bar">
        <div class="unlock-fill" style="width: ${item.percent}%;"></div>
      </div>
      <div class="unlock-percent">${item.percent}% dari total supply</div>
    </div>
  `;

            // 5. Tambahkan HTML yang dibuat ke dalam kontainer
            container.innerHTML += htmlContent;
        });
        const labels = [
            'Month 0', 'Month 3', 'Month 6', 'Month 9', 'Month 12',
            'Month 15', 'Month 18', 'Month 21', 'Month 24', 'Month 27',
            'Month 30', 'Month 33', 'Month 36', 'Month 39', 'Month 42', 'Month 45', 'Month 48', 'Month 51', 'Month 54', 'Month 57', 'Month 60'
        ];

        const data = {
            labels,
            datasets: [{
                    label: 'Airdrop',
                    data: labels.map((_, i) => (i <= 8 ? (i + 1) * (20 / 9) : 20)),
                    backgroundColor: '#a1c4fd'
                },
                {
                    label: 'Staking',
                    data: labels.map((_, i) => i <= 20 ? (i + 1) * (10 / 21) : 10),
                    backgroundColor: '#c2e9fb'
                },
                {
                    label: 'Reward Holder',
                    data: labels.map((_, i) => i >= 8 ? (i - 8 + 1) * (15 / 13) : 0),
                    backgroundColor: '#8ec5fc'
                },
                {
                    label: 'Utilitas',
                    data: labels.map((_, i) => i <= 4 ? (i + 1) * (5 / 5) : 5),
                    backgroundColor: '#736ced'
                },
                {
                    label: 'Likuiditas',
                    data: labels.map((_, i) => {
                        if (i === 0) return 1.25;
                        if (i === 1) return 2.5;
                        if (i === 2) return 3.75;
                        if (i === 3) return 5;
                        if (i === 4) return 5;
                        if (i === 5) return 10;
                        if (i >= 6 && i < 8) return 10;
                        if (i >= 8) return 15;
                    }),
                    backgroundColor: '#3d348b'
                },
                {
                    label: 'Ekosistem',
                    data: labels.map((_, i) => i >= 8 ? (i - 8 + 1) * (10 / 13) : 0),
                    backgroundColor: '#5ddcff'
                },
                {
                    label: 'Marketing',
                    data: labels.map((_, i) => i <= 12 ? (i + 1) * (10 / 13) : 10),
                    backgroundColor: '#2b5876'
                },
                {
                    label: 'Tim & Kontributor',
                    data: labels.map((_, i) => i >= 12 ? (i - 11) * (10 / 9) : 0),
                    backgroundColor: '#cbd5e1'
                },
                {
                    label: 'Treasury',
                    data: labels.map((_, i) => i >= 12 ? (i - 11) * (5 / 9) : 0),
                    backgroundColor: '#8E8C84'
                }
            ]
        };
        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    title: {
                        display: false,
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    },
                    legend: {
                        labels: {
                            color: '#ffffff'
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        ticks: {
                            color: '#ffffff'
                        }
                    },
                    y: {
                        stacked: true,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            },
                            color: '#ffffff'
                        }
                    }
                }
            },
        };

        new Chart(
            document.getElementById('vestingChart'),
            config
        );

        // Load Pie Chart once
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('pieChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: [
                        'Airdrop', 'Staking', 'Reward Holder', 'Utilitas',
                        'Likuiditas', 'Ekosistem', 'Marketing ', 'Tim & Kontributor', 'Treasury'
                    ],
                    datasets: [{
                        data: [20, 10, 15, 5, 15, 10, 10, 10, 5],
                        backgroundColor: [
                            '#a1c4fd', '#c2e9fb', '#8ec5fc', '#736ced',
                            '#3d348b', '#5ddcff', '#2b5876', '#cbd5e1', '#8E8C84'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>

    <script>
        document.getElementById('loadProgressBtn').addEventListener('click', async function() {
            const btn = this;
            const container = document.getElementById('progress-list');
            const loading = document.getElementById('progress-loading');

            // Kosongkan container & tampilkan spinner
            container.innerHTML = '';
            loading.style.display = 'block';

            // ✅ Disable button dan ubah teksnya
            btn.disabled = true;
            btn.innerText = '⏳ Memuat...';

            try {
                const response = await fetch('/api/token-unlocks');
                const result = await response.json();

                loading.style.display = 'none';

                if (result.status && result.data.length > 0) {
                    container.innerHTML = '';
                    result.data.forEach(item => {
                        const percent = parseFloat(item.percentage) > 100 ? 100 : parseFloat(item.percentage);
                        const formattedAmount = parseInt(item.amount_token).toLocaleString('id-ID');
                        const formattedUnlocked = parseInt(item.unlock_token).toLocaleString('id-ID');

                        const wrapper = document.createElement('div');
                        wrapper.className = 'progress-item';

                        wrapper.innerHTML = `
                    <div class="progress-label">
                        ${item.kategori} — <strong>${formattedUnlocked}</strong> / <strong>${formattedAmount}</strong> MYNU (${item.percentage}%)
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="background-color: ${item.warna};"></div>
                    </div>
                `;

                        container.appendChild(wrapper);
                        btn.innerText = 'Update';
                        btn.style.display = 'none';
                        // Delay untuk animasi progress-fill agar terlihat naik
                        setTimeout(() => {
                            const fill = wrapper.querySelector('.progress-fill');
                            if (fill) {
                                fill.style.width = percent + '%';
                            }
                        }, 10);

                    });

                } else {
                    container.innerHTML = '<p class="text-gray-400 text-center">Tidak ada data unlock token tersedia.</p>';
                    btn.style.display = 'none';
                }
            } catch (error) {
                container.innerHTML = `<p class="text-red-400 text-center">⚠️ Gagal memuat data: ${error.message}</p>`;
                loading.style.display = 'none';
                btn.disabled = false;
                btn.innerText = 'Coba lagi';
            }

        });
    </script>



    @endsection