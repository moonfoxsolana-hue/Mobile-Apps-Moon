@extends('layouts.trivia')

@section('content')
<style>
    /* 🎴 trivia Game CSS (Isolated) */
    .trivia-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        min-height: 75vh;
        position: relative;
        z-index: 2;
    }

    .trivia-card {
        background: rgba(20, 20, 20, 0.8);
        border: 1px solid rgba(55, 199, 212, 0.3);
        box-shadow: 0 0 15px rgba(55, 207, 212, 0.2);
        border-radius: 18px;
        padding: 30px;
        width: 90%;
        max-width: 600px;
        text-align: center;
        animation: trivia-fadeIn 0.6s ease;
    }

    .trivia-title {
        font-family: 'Cinzel', serif;
        font-size: 1.8rem;
        color: #37c7d4ff;
        margin-bottom: 14px;
    }

    .trivia-desc {
        font-size: 1rem;
        color: #ccc;
        margin-bottom: 24px;
        line-height: 1.6;
    }

    .trivia-btn {
        background: rgba(20, 20, 20, 0.8);
        border: 1px solid #37cfd4ff;
        color: #37d1d4ff;
        padding: 10px 24px;
        margin: 4px;
        border-radius: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .trivia-btn:hover {
        background: rgba(55, 204, 212, 0.3);
        box-shadow: 0 0 15px rgba(55, 207, 212, 0.5);
        transform: translateY(-2px);
    }

    .trivia-btn-refresh {
        background: rgba(20, 20, 20, 0.8);
        border: 1px solid #37cfd4ff;
        color: #37d1d4ff;
        padding: 4px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .trivia-btn-refresh:hover {
        background: rgba(55, 204, 212, 0.3);
        box-shadow: 0 0 15px rgba(55, 207, 212, 0.5);
        transform: translateY(-2px);
    }

    .trivia-question {
        color: #eee;
        font-size: 1.2rem;
        margin: 20px 0;
    }

    .trivia-answers {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-top: 20px;
    }

    .trivia-answer {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(212, 175, 55, 0.2);
        padding: 12px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.25s ease;
        color: #ddd;
    }

    .trivia-answer:hover {
        background: rgba(55, 178, 212, 0.2);
        border-color: #3792d4ff;
        color: #fff;
    }

    .trivia-answer.selected {
        background: rgba(55, 212, 201, 0.35);
        border-color: #37cfd4ff;
        color: #fff;
    }

    .trivia-answer.correct {
        background: rgba(55, 212, 73, 0.35);
        border-color: #37e571ff;
        color: #fff;
    }

    .trivia-answer.disabled {
        pointer-events: none;
        opacity: 0.6;
    }

    .trivia-progress-bar {
        width: 100%;
        height: 6px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
        margin-bottom: 16px;
    }

    #trivia-progress {
        width: 0%;
        height: 6px;
        background: #37d4d4ff;
        border-radius: 4px;
        transition: width 0.4s ease;
    }

    .trivia-result-title {
        font-family: 'Cinzel', serif;
        color: #37c7d4ff;
        font-size: 1.6rem;
        margin-bottom: 20px;
    }

    .trivia-category {
        font-family: 'Cinzel', serif;
        font-size: 1.2rem;
        color: #37d4ccff;
    }

    .trivia-input {
        width: 100%;
        max-width: 300px;
        padding: 10px;
        border: 1px solid #37cfd4ff;
        border-radius: 8px;
        background: rgba(20, 20, 20, 0.8);
        color: #eee;
        font-size: 1rem;
        margin-bottom: 12px;
        text-align: center;
    }

    .trivia-input::placeholder {
        color: #bbb;
    }

    .trivia-input:focus {
        outline: none;
        border-color: #ffe600ff;
        box-shadow: 0 0 8px rgba(55, 154, 212, 0.5);
    }

    small {
        color: #bbb;
        display: block;
        font-size: 8px;
        margin-top: 8px;
        margin-left: 8px;
        text-align: left;
    }

    .room-item {
        background: rgba(255, 255, 255, 0.05);
        padding: 10px 15px;
        border-radius: 12px;
        margin: 12px 12px;
        transition: all 0.3s ease;
        color: #fff;
        cursor: pointer;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .room-item:hover {
        background: rgba(255, 215, 0, 0.15);
        border-color: rgba(255, 215, 0, 0.4);
        transform: translateY(-2px);
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.2);
        color: #ffd700;
    }

    @keyframes trivia-fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .trivia-waiting {
        display: none;
        text-align: center;
        margin-top: 16px;
        color: #ffc107;
        font-size: 14px;
    }

    .trivia-loader {
        width: 36px;
        height: 36px;
        margin: 0 auto 8px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top-color: #ffc107;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .trivia-finished {
        color: #00ff99;
        font-weight: bold;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.6;
        }

        100% {
            opacity: 1;
        }
    }

    strong {
        color: #37d4ccff;
        font-size: 1.1rem;
    }

    #countdown-bar {
        width: 60%;
        height: 15px;
        margin: 20px auto;
        background-color: #222;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 10px #111;
        display: none;
    }

    #progress {
        width: 100%;
        height: 100%;
        background: linear-gradient(to left, rgba(107, 183, 255, 1), rgba(125, 85, 255, 1), rgba(189, 89, 255, 1));
        transition: width 1s linear;
    }

    #room-countdown-bar {
        width: 60%;
        height: 15px;
        margin: 20px auto;
        background-color: #222;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 10px #111;
        display: none;
    }

    #room-progress {
        width: 100%;
        height: 100%;
        background: linear-gradient(to left, rgba(107, 183, 255, 1), rgba(125, 85, 255, 1), rgba(189, 89, 255, 1));
        transition: width 1s linear;
    }

    @media (max-width: 768px) {

        #countdown-bar {
            width: 85%;
        }

        #room-countdown-bar {
            width: 85%;
        }
    }

    @media (max-width: 480px) {
        #countdown-bar {
            width: 90%;
            height: 12px;
        }

        #room-countdown-bar {
            width: 90%;
            height: 12px;
        }
    }
</style>
<div class="trivia-container">
    <div class="trivia-card" id="trivia-intro">
        <h2 class="trivia-title">Selamat Datang di Arcane of Trivia</h2>
        <p class="trivia-desc">“Setiap pengetahuan menyimpan jejak masa lampau…
            Uji ingatan dan intuisi dalam dunia trivia Mystic Nusa.
            Tak sekadar menjawab benar — tapi menembus kabut pengetahuan yang terlupakan.”</p>
        <input class="trivia-input" id="category" placeholder="Masukkan kategori" required maxlength="50" /></br>
        <button class="trivia-btn" id="trivia-start-btn">Mulai Ujian</button>
        <p style="margin: 12px 0;">Atau </p>
        <button class="trivia-btn" id="trivia-room-btn">Main Bersama</button>
    </div>
    <div class="trivia-card" id="trivia-question-card" style="display:none;">
        <div class="trivia-progress-bar">
            <div id="trivia-progress"></div>
        </div>
        <h3 class="trivia-question" id="trivia-question-text">Pertanyaan akan muncul di sini...</h3>
        <div class="trivia-answers" id="trivia-answers-container"></div>
        <small style="color: #bbb;display:none" id="trivia-ai-label"></small>
        <div id="countdown-bar">
            <div id="progress"></div>
        </div>
        <button class="trivia-btn" id="trivia-next-btn" style="display:none;margin-top: 20px;">Pertanyaan Selanjutnya</button>
    </div>

    <div class="trivia-card" id="trivia-result-card" style="display:none;">
        <h2 class="trivia-result-title">✨ Hasil Ujian Trivia ✨</h2>
        <p><strong>Total Score:</strong> <span id="trivia-total-point">0</span></p>
        <p><strong>Streak:</strong> <span id="trivia-streak">0</span></p>
        <strong>
            <p id="trivia-category" class="trivia-category"></p>
        </strong>
        <p id="trivia-message"></p>
        <button class="trivia-btn" id="trivia-restart-btn">Main Lagi</button>
    </div>


    <!-- TRIVIA ROOM CARD -->
    <div class="trivia-card" id="trivia-room-intro" style="display:none;">
        <h2 class="trivia-title">Arcane of Trivia</h2>
        <h3>Ruang Tersedia:</h3>
        <button class="trivia-btn-refresh" id="trivia-room-refresh-btn">Refresh</button>
        <div id="listRooms"></div>
        <div>
            <button class="trivia-btn" id="trivia-create-room-modal-btn">Buat Ruang</button>
            <button class="trivia-btn" id="trivia-exit-room-modal-btn">Kembali</button>
        </div>
    </div>

    <div class="trivia-card" id="trivia-room" style="display:none;">
        <h2 class="trivia-title">Arcane of Trivia</h2>
        <div id="roomInfo" style="margin-bottom: 12px;"></div>
        <div id="listPlayers"></div>
        <button class="trivia-btn" id="trivia-ready-room-btn" value='1' style="display:none;">Siap</button>
        <button class="trivia-btn" id="trivia-not-ready-room-btn" value='0' style="display:none;">Batal</button>
        <button class="trivia-btn" id="trivia-start-room-btn" style="display:none;">Mulai bermain</button>
        <div id="trivia-room-message" style="margin-top: 12px;font-size: 12px;color:red;"></div>
    </div>

    <div class="trivia-card" id="trivia-room-question-card" style="display:none;">
        <div class="trivia-room-progress-bar">
            <div id="trivia-room-progress"></div>
        </div>
        <h3 class="trivia-question" id="trivia-room-question-text">Pertanyaan akan muncul di sini...</h3>
        <small style="color: #bbb;display:none" id="trivia-room-ai-label"></small>
        <div class="trivia-answers" id="trivia-room-answers-container"></div>
        <div id="room-countdown-bar">
            <div id="room-progress"></div>
        </div>
        <button class="trivia-btn" id="trivia-room-next-btn" style="display:none;margin-top: 20px;">Pertanyaan Selanjutnya</button>
    </div>

    <div class="trivia-card" id="trivia-room-result-card" style="display:none;">
        <h2 class="trivia-title">Hasil Permainan</h2>
        <h3 class="trivia-subtitle">🏆 Leaderboard</h3>
        <div id="trivia-room-leaderboard" class="trivia-leaderboard">
            <!-- Leaderboard akan dirender di sini -->
        </div>
        <!-- 🔮 Loader Animasi -->
        <div id="trivia-waiting-animation" class="trivia-waiting">
            <div class="trivia-loader"></div>
            <p>Menunggu semua pemain menyelesaikan permainan...</p>
        </div>
        <div id="trivia-room-result-message" style="margin-top: 12px;font-size: 12px;color:yellow;"></div>
        <div class="trivia-actions">
            <button class="trivia-btn" onclick="roomIntroTriviaGame();" id="trivia-room-quit-btn">Keluar</button>
        </div>
    </div>


    <div id="createRoomModal" class="mystic-modal" style="display:none;">
        <div class="mystic-modal-content">
            <span class="close-modal" onclick="closeModal('createRoomModal')">&times;</span>
            <div style="text-align: center;">
                <h2>Buat Ruang Baru</h2>
                <input class="trivia-input" id="name-room" placeholder="Nama ruang" required maxlength="50" /></br>
                <input class="trivia-input" id="category-room" placeholder="Kategori" required maxlength="50" /></br>
                <input class="trivia-input numeric-only" id="max-player-room" placeholder="Jumlah maksimal pemain" required maxlength="2" /></br>
                <input class="trivia-input numeric-only" id="join-code-room" placeholder="Kode bergabung (optional)" minlength="6" maxlength="6" /></br>
                <check style="color: #bbb; font-size: 12px; margin-bottom: 12px;">
                    <input type="checkbox" id="logic-mode-room" />
                    <label for="logic-mode-room">Mode Logika (Soal berbasis logika dan penalaran)</label>
                </check></br>
                <button class="trivia-btn" id="trivia-create-room-btn">Buat Ruang</button>
                <div id="roomMessage" style="margin-top: 12px;font-size: 12px;"></div>
            </div>
        </div>
    </div>
    <div id="joinRoomModal" class="mystic-modal" style="display:none;">
        <div class="mystic-modal-content">
            <span class="close-modal" onclick="closeModal('joinRoomModal')">&times;</span>
            <div style="text-align: center;">
                <h2>Bergabung dengan Ruang</h2>
                <h5> Materi Tentang : </h5>
                <p id="join-room-category"></p>
                <input class="trivia-input numeric-only" id="join-code" placeholder="Masukkan kode ruang" required maxlength="6" /></br>
                <button class="trivia-btn" id="trivia-join-room-btn">Bergabung</button>
                <div id="roomJoinMessage" style="margin-top: 12px;font-size: 12px;"></div>
            </div>
        </div>
    </div>

    <audio id="success-sound" src="/sound/sfx/success.mp3"></audio>
    <audio id="great-success-sound" src="/sound/sfx/great-success.mp3"></audio>
    <audio id="wrong-sound" src="/sound/sfx/wrong.mp3"></audio>
    <audio id="finish-room-sound" src="/sound/sfx/finish.mp3"></audio>


    <script>
        // const token = localStorage.getItem('token');
        let triviaRoomId = null;
        let triviaSessionId = null;
        let triviaQuestions = [];
        let triviaRoomQuestions = [];
        let triviaQuestionText = document.getElementById('trivia-question-text');
        let triviaRoomQuestionText = document.getElementById('trivia-room-question-text');
        let roomCheckInterval = null;
        let playerId = null;
        let roundTimer = null;
        let timeLeft = 10;
        const waitDiv = document.getElementById('trivia-waiting-animation');
        const msgDiv = document.getElementById('trivia-room-result-message');
        const quitBtn = document.getElementById('trivia-room-quit-btn');
        const leaderboardDiv = document.getElementById('trivia-room-leaderboard');
        const triviaReadyRoomBtn = document.getElementById('trivia-ready-room-btn');
        const triviaNotReadyRoomBtn = document.getElementById('trivia-not-ready-room-btn');

        const successSound = document.getElementById('success-sound');
        const greatSuccessSound = document.getElementById('great-success-sound');
        const wrongSound = document.getElementById('wrong-sound');
        const finishRoomSound = document.getElementById('finish-room-sound');

        document.getElementById('trivia-start-btn').addEventListener('click', startTriviaGame);
        document.getElementById('trivia-room-btn').addEventListener('click', roomIntroTriviaGame);
        document.getElementById('trivia-room-refresh-btn').addEventListener('click', roomIntroTriviaGame);
        document.getElementById('trivia-create-room-btn').addEventListener('click', createRoomTrivia);
        document.getElementById('trivia-join-room-btn').addEventListener('click', joinRoomTrivia);
        document.getElementById('trivia-ready-room-btn').addEventListener('click', readyRoomTrivia, 1);
        document.getElementById('trivia-not-ready-room-btn').addEventListener('click', readyRoomTrivia, 0);
        document.getElementById('trivia-start-room-btn').addEventListener('click', startRoomTrivia);
        document.getElementById('trivia-create-room-modal-btn').addEventListener('click', async () => {
            openModal('createRoomModal');
        });
        document.getElementById('trivia-exit-room-modal-btn').addEventListener('click', exitRoomTrivia);

        document.querySelectorAll('.numeric-only').forEach(input => {
            input.addEventListener('keypress', e => {
                if (e.key === 'e' || e.key === '.' || e.key === '-' || isNaN(e.key)) {
                    e.preventDefault();
                }
            });

            input.addEventListener('paste', e => {
                const pasteData = e.clipboardData.getData('text');
                if (isNaN(pasteData) || parseFloat(pasteData) <= 0) {
                    e.preventDefault();
                }
            });
        });

        function exitRoomTrivia() {
            document.getElementById('trivia-intro').style.display = 'block';
            document.getElementById('trivia-room').style.display = 'none';
            document.getElementById('trivia-room-intro').style.display = 'none';

        }
        async function startTriviaGame() {
            category = document.getElementById('category').value;
            if (!category) {
                alert('Silakan masukkan kategori terlebih dahulu.');
                return;
            }
            document.getElementById('trivia-intro').style.display = 'none';
            document.getElementById('trivia-result-card').style.display = 'none';
            document.getElementById('trivia-question-card').style.display = 'block';
            const container = document.getElementById('trivia-answers-container');
            container.innerHTML = '';
            triviaQuestionText.innerText = 'Memulai permainan...';
            document.getElementById('trivia-next-btn').innerText = 'Pertanyaan Selanjutnya';
            document.getElementById('trivia-next-btn').style.display = 'none';
            document.getElementById('trivia-progress').style.width = '0%';
            document.getElementById('progress').style.width = '100%';


            triviaSessionId = null;
            triviaQuestions = [];
            const res = await fetch('/api/trivia/start', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    category: category,
                    question_count: 10
                })
            });
            const data = await res.json();
            if (data.status == 'success') {
                triviaSessionId = data.session_id;
                triviaQuestions = data.question;
                if (data.current_question && data.total_question) {
                    const progress = (data.current_question / data.total_question) * 100;
                    document.getElementById('trivia-progress').style.width = `${progress}%`;
                }
                showTriviaQuestion();
            } else {
                if (data.status == 'error') {
                    triviaQuestionText.innerText = data.message || 'Gagal memulai permainan. Silakan coba lagi.';
                    setTimeout(() => {
                        triviaQuestionText.innerHTML = "";
                    }, 3000);
                    return;
                }
                triviaQuestionText.innerText = 'Terjadi kesalahan tak terduga. Silakan coba lagi.';
            }

        };

        async function showTriviaQuestion() {
            const q = triviaQuestions;
            if (!q) {
                showTriviaResult();
                return;
            }

            document.getElementById('trivia-next-btn').style.display = 'none';
            document.getElementById('countdown-bar').style.display = 'block';
            document.getElementById("progress").style.width = "100%";

            triviaQuestionText.innerText = q.question;
            if (q.created_by_ai == true && q.ai_version) {
                document.getElementById('trivia-ai-label').style.display = 'block';
                document.getElementById('trivia-ai-label').innerText = 'dibuat oleh AI model : ' + q.ai_version;
            } else {
                document.getElementById('trivia-ai-label').style.display = 'none';
                document.getElementById('trivia-ai-label').innerText = '';
            }
            const container = document.getElementById('trivia-answers-container');
            container.innerHTML = '';

            q.answers.forEach(answer => {
                const div = document.createElement('div');
                div.classList.add('trivia-answer');
                div.id = answer;
                div.innerHTML = answer;
                div.addEventListener('click', async () => {
                    document.querySelectorAll('.trivia-answer').forEach(x => {
                        x.classList.add('disabled');
                        x.classList.remove('selected');
                    });
                    div.classList.add('selected');
                    clearInterval(roundTimer);
                    document.getElementById('countdown-bar').style.display = 'none';
                    await sendTriviaAnswer(q.id, answer);
                });

                container.appendChild(div);
            });
            startCountdown(q.id);
        }

        async function sendTriviaAnswer(questionId, answer) {
            document.querySelectorAll('.trivia-answer').forEach(x => {
                x.classList.add('disabled');
            });
            document.getElementById('countdown-bar').style.display = 'none';
            const res = await fetch('/api/trivia/answer', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    session_id: triviaSessionId,
                    question_id: questionId,
                    selected_answer: answer
                })
            });
            const data = await res.json();
            if (data.status == 'error') {
                triviaQuestionText.innerText = data.message || 'Gagal menjawab pertanyaan. Silakan coba lagi.';
                setTimeout(() => {
                    triviaQuestionText.innerHTML = "";
                }, 3000);
                return;
            }
            if (data.correct_answer) {
                document.getElementById(data.correct_answer).classList.add('correct');
            }
            if (data.is_correct == true) {
                if (data.streak >= 5) {
                    greatSuccessSound.volume = 0.3;
                    greatSuccessSound.currentTime = 0;
                    greatSuccessSound.play();
                } else {
                    successSound.volume = 0.5;
                    successSound.currentTime = 0;
                    successSound.play();
                }
            } else if (data.is_correct == false) {
                wrongSound.volume = 0.5;
                wrongSound.currentTime = 0;
                wrongSound.play();
            }
            document.getElementById('trivia-next-btn').style.display = 'inline-block';

            document.getElementById('trivia-next-btn').onclick = () => {
                if (data.complete == false) {
                    if (data.current_question && data.total_question) {
                        const progress = (data.current_question / data.total_question) * 100;
                        document.getElementById('trivia-progress').style.width = `${progress}%`;
                        if (data.current_question == data.total_question) {
                            document.getElementById('trivia-next-btn').innerText = 'Lihat Hasil';
                        }
                    }
                    triviaQuestions = data.next_question;
                    showTriviaQuestion();
                } else {
                    showTriviaResult();
                }
            };
        }

        function startCountdown(questionId) {
            let progress = 100;
            timeLeft = 10; // ubah durasi ke 10 detik
            clearInterval(roundTimer);
            roundTimer = setInterval(() => {
                progress -= 10; // 100 / 10 detik = 10% per detik
                timeLeft--;
                document.getElementById("progress").style.width = `${progress}%`;
                if (timeLeft <= 0) {
                    clearInterval(roundTimer);
                    setTimeout(() => sendTriviaAnswer(questionId, 'tidak menjawab'), 1000);
                }
            }, 1000);
        }

        async function showTriviaResult() {
            document.getElementById('trivia-question-card').style.display = 'none';
            document.getElementById('trivia-result-card').style.display = 'block';
            const res = await fetch(`/api/trivia/finish`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    session_id: triviaSessionId
                })
            });
            const data = await res.json();
            if (data.status == 'error') {
                triviaQuestionText.innerText = data.message || 'Gagal melihat hasil permainan. Silakan coba lagi.';
                return;
            }
            finishRoomSound.volume = 0.3;
            finishRoomSound.currentTime = 0;
            finishRoomSound.play();
            document.getElementById('trivia-total-point').innerText = data.score;
            document.getElementById('trivia-streak').innerText = data.streak;
            document.getElementById('trivia-category').innerText = data.category;
            document.getElementById('trivia-message').innerText = data.duration_seconds ? `Kamu menyelesaikan dalam ${data.duration_seconds} detik!` : '';
        }

        document.getElementById('trivia-restart-btn').addEventListener('click', () => {
            document.getElementById('trivia-intro').style.display = 'block';
            document.getElementById('trivia-result-card').style.display = 'none';
        });


        // === Room ===
        async function roomIntroTriviaGame() {
            const listRoomsDiv = document.getElementById('listRooms');
            listRoomsDiv.innerHTML = '';
            document.getElementById('trivia-intro').style.display = 'none';
            document.getElementById('trivia-result-card').style.display = 'none';
            document.getElementById('trivia-question-card').style.display = 'none';
            document.getElementById('trivia-room-intro').style.display = 'block';
            document.getElementById('trivia-room').style.display = 'none';
            document.getElementById('trivia-room-question-card').style.display = 'none';
            document.getElementById('trivia-room-result-card').style.display = 'none';
            triviaReadyRoomBtn.style.display = 'none';
            triviaNotReadyRoomBtn.style.display = 'none';
            leaderboardDiv.innerHTML = '';
            msgDiv.innerHTML = '';
            quitBtn.style.display = 'in-line-block';
            waitDiv.style.display = 'none';
            triviaRoomId = null;
            triviaRoomQuestions = null

            const res = await fetch('/api/trivia/room/list', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });
            const data = await res.json();
            if (data.status == 'success') {
                playerId = data.player_id;
                if (data.rooms.length == 0) {
                    listRoomsDiv.innerHTML += '<p style="margin: 6px 0;">Tidak ada ruang yang tersedia.</p>';
                    if (data.room_detail != null) {
                        triviaRoomId = data.room_detail.id;
                        continueRoomTrivia(data.room_detail);
                    }
                } else {
                    data.rooms.forEach(room => {
                        listRoomsDiv.innerHTML += `
                <p id="room-${room.id}" class="room-item" style="cursor:pointer;">
                    🧩 Ruang: <b>${room.name}</b> (${room.players_count}/${room.max_players} - ${room.status})
                </p>`;
                    });
                    if (data.room_detail != null) {
                        triviaRoomId = data.room_detail.id;
                        continueRoomTrivia(data.room_detail);
                    }
                }

                // tambahkan event click untuk buka modal
                document.querySelectorAll('.room-item').forEach(btn => {
                    btn.addEventListener('click', () => {
                        triviaRoomId = btn.id.split('-')[1];
                        document.getElementById('join-room-category').innerText = data.rooms.find(r => r.id == triviaRoomId).category;
                        openModal('joinRoomModal');
                    });
                });

            } else {
                listRoomsDiv.innerHTML += `<p style="color: red;">Gagal memuat ruang: ${data.message}</p>`;
            }
        }



        async function createRoomTrivia() {
            const name = document.getElementById('name-room').value;
            const category = document.getElementById('category-room').value;
            const maxPlayers = parseInt(document.getElementById('max-player-room').value)
            const joinCode = document.getElementById('join-code-room').value;
            if (!name || !category || !maxPlayers) {
                document.getElementById('roomMessage').innerHTML = `<p style="color: red; font-size: 12px;">Nama ruang, kategori, dan jumlah maksimal pemain harus diisi.</p>`;
                return;
            }
            if (maxPlayers < 2 || maxPlayers > 99) {
                document.getElementById('roomMessage').innerHTML = `<p style="color: red; font-size: 12px;">Jumlah maksimal pemain harus antara 2 dan 99.</p>`;
                return;
            }
            if (joinCode && joinCode.length != 6) {
                document.getElementById('roomMessage').innerHTML = `<p style="color: red; font-size: 12px;">Kode bergabung harus terdiri dari 6 karakter.</p>`;
                return;
            }
            const logicMode = document.getElementById('logic-mode-room').checked;

            const res = await fetch('/api/trivia/room/create', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    category: category,
                    max_players: maxPlayers,
                    join_code: joinCode,
                    logic_mode: logicMode
                })
            });
            const data = await res.json();
            if (data.status == 'error') {
                document.getElementById('roomMessage').innerHTML = `<p style="color: red; font-size: 12px;">Gagal membuat room: ${data.message}</p>`;
                setTimeout(() => {
                    document.getElementById('roomMessage').innerHTML = "";
                }, 3000);
                return;
            }
            closeModal('createRoomModal');
            playerId = data.player_id;
            triviaRoomId = data.room_detail.id;
            continueRoomTrivia(data.room_detail);

        }

        async function joinRoomTrivia() {
            const code = document.getElementById('join-code').value;
            const res = await fetch('/api/trivia/room/join', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    room_id: triviaRoomId,
                    join_code: code
                })
            });
            const data = await res.json();
            if (data.status == 'error') {
                document.getElementById('roomJoinMessage').innerHTML = `<p style="color: red;">${data.message}</p>`;
                setTimeout(() => {
                    document.getElementById('roomJoinMessage').innerHTML = "";
                }, 3000);
                return;
            }
            triviaRoomId = data.room_detail.id;
            closeModal('joinRoomModal');
            continueRoomTrivia(data.room_detail);
        }

        async function continueRoomTrivia(data) {
            if (!triviaRoomId || triviaRoomId <= 0) {
                alert('Room tidak ditemukan');
                return;
            }

            document.getElementById('trivia-room-intro').style.display = 'none';
            document.getElementById('trivia-room').style.display = 'block';
            const roomInfoDiv = document.getElementById('roomInfo');
            roomInfoDiv.innerHTML = '<h5>Materi Tentang : </br>' + data.category + '</h5>';
            const playersDiv = document.getElementById('listPlayers');
            playersDiv.innerHTML = '<h5>Pemain dalam Ruang:</h5>';
            data.players.forEach(p => {
                const isHost = p.player_id === data.host_id; // siapa host room-nya
                const isSelf = p.player_id === playerId; // siapa user yang sedang login
                const isCurrentUserHost = playerId === data.host_id; // apakah user login adalah host

                const hostTag = isHost ? ' 👑' : '';
                const hostQuitTag = isHost && isSelf ?
                    `<button class='btn btn-sm btn-danger' style='font-size:10px;padding:2px 4px;' onclick='exitRoom()'>Keluar</button>` :
                    '';

                const readyTag = p.is_ready ? ' ✅' : '';

                // Tombol kick hanya muncul jika user yg login adalah host dan target player bukan host
                const kickBtn = isCurrentUserHost && !isHost ?
                    `<button class='btn btn-sm btn-danger' style='font-size: 10px;padding:2px;padding-top:1px;padding-bottom:1px;' onclick='kickPlayer(${p.player_id})'>Kick</button>` :
                    '';

                playersDiv.innerHTML += `
        <p style="margin-bottom:6px;margin-top:6px;">
            🧩 ${p.player.name}${hostTag}${hostQuitTag}${readyTag} ${kickBtn}
        </p>`;
            });

            triviaRoomId = data.id;
            if (playerId === data.host_id) {
                document.getElementById('trivia-start-room-btn').style.display = 'inline-block';
            } else {
                document.getElementById('trivia-ready-room-btn').style.display = 'inline-block';
            }
            startPolling();
        }



        async function pollRoomStatus() {
            if (!triviaRoomId) return;

            try {
                const response = await fetch('/api/trivia/room/active-room', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });
                const result = await response.json();

                if (result.status !== 'success') {
                    triviaRoomId = null;
                    stopPolling();
                    alert('Anda di-kick atau Ruang telah dihapus.');
                    roomIntroTriviaGame();
                    return;
                }


                const room = result.room_detail;
                const playersDiv = document.getElementById('listPlayers');
                playersDiv.innerHTML = '<h5>Pemain dalam Ruang:</h5>';

                room.players.forEach(p => {
                    const isHost = p.player_id === room.host_id; // siapa host room-nya
                    const isSelf = p.player_id === playerId; // siapa user yang sedang login
                    const isCurrentUserHost = playerId === room.host_id; // apakah user login adalah host

                    const hostTag = isHost ? ' 👑' : '';
                    const hostQuitTag = isHost && isSelf ?
                        `<button class='btn btn-sm btn-danger' style='font-size:10px;padding:2px 4px;' onclick='exitRoom()'>Keluar</button>` :
                        '';

                    const readyTag = p.is_ready ? ' ✅' : '';

                    // Tombol kick hanya muncul jika user yg login adalah host dan target player bukan host
                    const kickBtn = isCurrentUserHost && !isHost ?
                        `<button class='btn btn-sm btn-danger' style='font-size: 10px;padding:2px;padding-top:1px;padding-bottom:1px;' onclick='kickPlayer(${p.player_id})'>Kick</button>` :
                        '';

                    playersDiv.innerHTML += `
        <p style="margin-bottom:6px;margin-top:6px;">
            🧩 ${p.player.name}${hostTag}${hostQuitTag}${readyTag} ${kickBtn}
        </p>`;
                });

                // update status teks

                if (playerId === room.host_id) {
                    document.getElementById('trivia-start-room-btn').style.display = 'inline-block';
                } else {
                    if (triviaNotReadyRoomBtn.style.display === 'none') {
                        triviaReadyRoomBtn.style.display = 'inline-block';
                    }
                }
                if (result.state === 'finished') {
                    stopPolling();
                    showTriviaRoomResult();
                    return;
                }
                if (result.question) {
                    stopPolling();
                    triviaRoomQuestions = result.question;
                    if (result.logic_mode == true) {
                        showTriviaRoomLogicQuestion();
                    } else {
                        showTriviaRoomQuestion();
                    }
                }

            } catch (error) {
                console.error('Gagal memuat status room:', error);
            }
        }

        function startPolling() {
            if (roomCheckInterval) clearInterval(roomCheckInterval);
            roomCheckInterval = setInterval(pollRoomStatus, 3000);
        }

        function stopPolling() {
            if (roomCheckInterval) clearInterval(roomCheckInterval);
        }
        async function exitRoom() {
            if (!confirm('Yakin ingin keluar dari Ruang Trivia?')) return;

            await fetch('/api/trivia/room/exit', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    room_id: triviaRoomId
                })
            });
            await pollRoomStatus(); // refresh manual setelah keluar
        }
        async function kickPlayer(playerId) {
            if (!confirm('Yakin ingin mengeluarkan pemain ini?')) return;

            await fetch('/api/trivia/room/kick', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    room_id: triviaRoomId,
                    player_id: playerId
                })
            });

            await pollRoomStatus(); // refresh manual setelah kick
        }

        async function readyRoomTrivia(isReady) {
            readyVal = isReady.target.value;
            const res = await fetch('/api/trivia/room/ready', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    room_id: triviaRoomId,
                    is_ready: readyVal
                })
            });
            const data = await res.json();

            if (data.status == 'error') {
                document.getElementById('trivia-room-message').innerText = data.message || 'Gagal menandai siap. Silakan coba lagi.';
                setTimeout(() => {
                    document.getElementById('trivia-room-message').innerText = "";
                }, 3000);
                return;
            }
            if (readyVal == 1) {
                triviaReadyRoomBtn.style.display = 'none';
                triviaNotReadyRoomBtn.style.display = 'inline-block';
                return;
            } else {
                triviaReadyRoomBtn.style.display = 'inline-block';
                triviaNotReadyRoomBtn.style.display = 'none';
            }
        }
        async function startRoomTrivia() {
            stopPolling();
            const res = await fetch('/api/trivia/room/start', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    room_id: triviaRoomId,
                    question_count: 10
                })
            });
            const data = await res.json();
            if (data.status == 'error') {
                startPolling();
                document.getElementById('trivia-room-message').innerText = data.message || 'Gagal memulai permainan. Silakan coba lagi.';
                setTimeout(() => {
                    document.getElementById('trivia-room-message').innerText = "";
                }, 3000);
                return;
            }
            triviaRoomQuestions = data.questions;
            if (data.logic_mode == true) {
                showTriviaRoomLogicQuestion();
            } else {
                showTriviaRoomQuestion();
            }
        }

        async function sendTriviaAnswerRoom(questionId, answer) {
            document.querySelectorAll('.trivia-answer').forEach(x => {
                x.classList.add('disabled');
            });
            document.getElementById('room-countdown-bar').style.display = 'none';

            const res = await fetch('/api/trivia/room/answer', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    room_id: triviaRoomId,
                    question_id: questionId,
                    selected_answer: answer
                })
            });
            const data = await res.json();
            if (data.status == 'error') {
                triviaQuestionText.innerText = data.message || 'Gagal menjawab pertanyaan. Silakan coba lagi.';
                return;
            }
            if (data.correct_answer) {
                document.getElementById(data.correct_answer).classList.add('correct');
            }
            if (data.is_correct == true) {
                if (data.streak >= 5) {
                    greatSuccessSound.volume = 0.3;
                    greatSuccessSound.currentTime = 0;
                    greatSuccessSound.play();
                } else {
                    successSound.volume = 0.5;
                    successSound.currentTime = 0;
                    successSound.play();
                }
            } else if (data.is_correct == false) {
                wrongSound.volume = 0.5;
                wrongSound.currentTime = 0;
                wrongSound.play();
            }
            document.getElementById('trivia-room-next-btn').style.display = 'inline-block';

            document.getElementById('trivia-room-next-btn').onclick = () => {
                if (data.complete == false) {
                    if (data.current_question && data.total_question) {
                        const progress = (data.current_question / data.total_question) * 100;
                        document.getElementById('trivia-room-progress').style.width = `${progress}%`;
                        if (data.current_question == data.total_question) {
                            document.getElementById('trivia-room-next-btn').innerText = 'Lihat Hasil';
                        }
                    }
                    triviaRoomQuestions = data.next_question;
                    if (data.logic_mode == true) {
                        showTriviaRoomLogicQuestion();
                    } else {
                        showTriviaRoomQuestion();
                    }
                } else {
                    showTriviaRoomResult();
                }
            };
        }
        // === General Question Display ===
        async function showTriviaRoomQuestion() {
            document.getElementById('trivia-room-intro').style.display = 'none';
            document.getElementById('trivia-room').style.display = 'none';
            document.getElementById('trivia-room-question-card').style.display = 'block';
            document.getElementById('room-countdown-bar').style.display = 'block';
            document.getElementById("room-progress").style.width = "100%";

            const q = triviaRoomQuestions;
            if (!q) {
                showTriviaRoomResult();
                return;
            }

            document.getElementById('trivia-room-next-btn').style.display = 'none';

            triviaRoomQuestionText.innerText = q.question;
            if (q.created_by_ai == true && q.ai_version) {
                document.getElementById('trivia-room-ai-label').style.display = 'block';
                document.getElementById('trivia-room-ai-label').innerText = 'dibuat oleh AI model : ' + q.ai_version;
            } else {
                document.getElementById('trivia-room-ai-label').style.display = 'none';
                document.getElementById('trivia-room-ai-label').innerText = '';
            }
            const container = document.getElementById('trivia-room-answers-container');
            container.innerHTML = '';

            q.answers.forEach(answer => {
                const div = document.createElement('div');
                div.classList.add('trivia-answer');
                div.id = answer;
                div.innerHTML = answer;
                div.addEventListener('click', async () => {
                    document.querySelectorAll('.trivia-answer').forEach(x => {
                        x.classList.add('disabled');
                        x.classList.remove('selected');
                    });
                    div.classList.add('selected');
                    clearInterval(roundTimer);
                    document.getElementById('room-countdown-bar').style.display = 'none';
                    await sendTriviaAnswerRoom(q.id, answer);
                });

                container.appendChild(div);
            });
            startRoomCountdown(q.id);
        }

        async function showTriviaRoomLogicQuestion() {
            document.getElementById('trivia-room-intro').style.display = 'none';
            document.getElementById('trivia-room').style.display = 'none';
            document.getElementById('trivia-room-question-card').style.display = 'block';
            document.getElementById('room-countdown-bar').style.display = 'none';
            document.getElementById("room-progress").style.width = "100%";

            const q = triviaRoomQuestions;
            if (!q) {
                showTriviaRoomResult();
                return;
            }

            document.getElementById('trivia-room-next-btn').style.display = 'none';

            triviaRoomQuestionText.innerText = q.question;
            if (q.created_by_ai == true && q.ai_version) {
                document.getElementById('trivia-room-ai-label').style.display = 'block';
                document.getElementById('trivia-room-ai-label').innerText = 'dibuat oleh AI model : ' + q.ai_version;
            } else {
                document.getElementById('trivia-room-ai-label').style.display = 'none';
                document.getElementById('trivia-room-ai-label').innerText = '';
            }
            const container = document.getElementById('trivia-room-answers-container');
            container.innerHTML = '';

            q.answers.forEach(answer => {
                const div = document.createElement('div');
                div.classList.add('trivia-answer');
                div.id = answer;
                div.innerHTML = answer;
                div.addEventListener('click', async () => {
                    document.querySelectorAll('.trivia-answer').forEach(x => {
                        x.classList.add('disabled');
                        x.classList.remove('selected');
                    });
                    div.classList.add('selected');
                    clearInterval(roundTimer);
                    document.getElementById('room-countdown-bar').style.display = 'none';
                    await sendTriviaAnswerRoom(q.id, answer);
                });

                container.appendChild(div);
            });
        }
        // Jalankan polling setelah submit terakhir pemain
        function showTriviaRoomResult() {
            document.getElementById('trivia-room-question-card').style.display = 'none';
            document.getElementById('trivia-room-result-card').style.display = 'block';
            startTriviaPolling();
        }

        function startRoomCountdown(questionId) {
            let progress = 100;
            timeLeft = 10; // ubah durasi ke 10 detik
            clearInterval(roundTimer);
            roundTimer = setInterval(() => {
                progress -= 10; // 100 / 10 detik = 10% per detik
                timeLeft--;
                document.getElementById("room-progress").style.width = `${progress}%`;
                if (timeLeft <= 0) {
                    clearInterval(roundTimer);
                    setTimeout(() => sendTriviaAnswerRoom(questionId, 'tidak menjawab'), 1000);
                }
            }, 1000);
        }
        let triviaPollingInterval = null;


        async function pollTriviaRoomResult() {
            try {
                const res = await fetch(`/api/trivia/room/finish`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        room_id: triviaRoomId
                    })
                });

                const data = await res.json();
                if (data.status !== 'success') return;

                if (data.room_finished !== true) {
                    // Tampilkan animasi loading
                    quitBtn.disabled = true;
                } else {
                    leaderboardDiv.innerHTML = data.leaderboard.map((p, i) =>
                        `<p>${i + 1}. ${p.name} — ${p.score} poin (${p.duration}s)</p>`
                    ).join('');
                    // Semua sudah selesai
                    waitDiv.style.display = 'none';
                    msgDiv.innerHTML = `<span class="trivia-finished">✨ Pemenang pertama adalah : <strong>${data.leaderboard[0].name}</strong>! ✨</span>`;
                    quitBtn.disabled = false;
                    finishRoomSound.volume = 0.3;
                    finishRoomSound.currentTime = 0;
                    finishRoomSound.play();
                    clearInterval(triviaPollingInterval);
                }

            } catch (err) {
                console.error('Polling error:', err);
            }
        }

        // Mulai polling 5 detik sekali
        function startTriviaPolling() {
            if (triviaPollingInterval) clearInterval(triviaPollingInterval);
            waitDiv.style.display = 'block';
            triviaPollingInterval = setInterval(pollTriviaRoomResult, 3000);
        }
    </script>
    @endsection