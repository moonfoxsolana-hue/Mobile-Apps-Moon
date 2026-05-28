@extends('layouts.logical')

@section('content')
<style>
  /* 🎴 Logical Game CSS (Isolated) */
  .logical-container {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    min-height: 65vh;
    position: relative;
    z-index: 2;
  }

  .logical-card {
    background: rgba(20, 20, 20, 0.8);
    border: 1px solid rgba(212, 175, 55, 0.3);
    box-shadow: 0 0 15px rgba(212, 175, 55, 0.2);
    border-radius: 18px;
    padding: 30px;
    width: 90%;
    max-width: 600px;
    text-align: center;
    animation: logical-fadeIn 0.6s ease;
  }

  .logical-title {
    font-family: 'Cinzel', serif;
    font-size: 1.8rem;
    color: #d4af37;
    margin-bottom: 14px;
  }

  .logical-desc {
    font-size: 1rem;
    color: #ccc;
    margin-bottom: 24px;
    line-height: 1.6;
  }

  .logical-btn {
    background: rgba(20, 20, 20, 0.8);
    border: 1px solid #d4af37;
    color: #d4af37;
    padding: 10px 24px;
    border-radius: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .logical-btn:hover {
    background: rgba(212, 175, 55, 0.3);
    box-shadow: 0 0 15px rgba(212, 175, 55, 0.5);
    transform: translateY(-2px);
  }

  .logical-question {
    color: #eee;
    font-size: 1.2rem;
    margin: 20px 0;
  }

  .logical-answers {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-top: 20px;
  }

  .logical-answer {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(212, 175, 55, 0.2);
    padding: 12px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.25s ease;
    color: #ddd;
  }

  .logical-answer:hover {
    background: rgba(212, 175, 55, 0.2);
    border-color: #d4af37;
    color: #fff;
  }

  .logical-answer.selected {
    background: rgba(212, 175, 55, 0.35);
    border-color: #d4af37;
    color: #fff;
  }

  .logical-answer.disabled {
    pointer-events: none;
    opacity: 0.6;
  }

  .logical-progress-bar {
    width: 100%;
    height: 6px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    margin-bottom: 16px;
  }

  #logical-progress {
    width: 0%;
    height: 6px;
    background: #d4af37;
    border-radius: 4px;
    transition: width 0.4s ease;
  }

  .logical-result-title {
    font-family: 'Cinzel', serif;
    color: #d4af37;
    font-size: 1.6rem;
    margin-bottom: 20px;
  }
  .logical-category {
    font-family: 'Cinzel', serif;
    font-size: 1.2rem;
    color: #d4af37;
  }
  @keyframes logical-fadeIn {
    from {
      opacity: 0;
      transform: scale(0.95);
    }

    to {
      opacity: 1;
      transform: scale(1);
    }
  }
</style>
<div class="logical-container">
  <div class="logical-card" id="logical-intro">
    <h2 class="logical-title">Selamat Datang di Mystical Logic of Minds</h2>
    <p class="logical-desc">Uji logika dan intuisi tersembunyi di balik dunia mistik. Jawablah 10 pertanyaan untuk melihat seberapa tajam pikiranmu.</p>
    <button class="logical-btn" id="logical-start-btn">Mulai Ujian</button>
  </div>

  <div class="logical-card" id="logical-question-card" style="display:none;">
    <div class="logical-progress-bar">
      <div id="logical-progress"></div>
    </div>
    <h3 class="logical-question" id="logical-question-text">Pertanyaan akan muncul di sini...</h3>

    <div class="logical-answers" id="logical-answers-container"></div>

    <button class="logical-btn" id="logical-next-btn" style="display:none;margin-top: 20px;">Pertanyaan Selanjutnya</button>
  </div>

  <div class="logical-card" id="logical-result-card" style="display:none;">
    <h2 class="logical-result-title">✨ Hasil Ujian Logika ✨</h2>
    <p><strong>Total Poin:</strong> <span id="logical-total-point">0</span></p>
    <p><strong>Nilai IQ:</strong> <span id="logical-iq">0</span></p>
    <strong>
      <p id="logical-category" class="logical-category"></p>
    </strong>
    <p id="logical-message"></p>
    <button class="logical-btn" id="logical-restart-btn">Main Lagi</button>
  </div>
</div>

<script>
  // const token = localStorage.getItem('token');
  let logicalMatchId = null;
  let logicalQuestions = [];
  let logicalQuestionText = document.getElementById('logical-question-text');

  async function startLogicalGame() {
    document.getElementById('logical-intro').style.display = 'none';
    document.getElementById('logical-result-card').style.display = 'none';
    document.getElementById('logical-question-card').style.display = 'block';
    document.getElementById('logical-next-btn').innerText = 'Pertanyaan Selanjutnya';

    logicalMatchId = null;
    logicalQuestions = [];
    const res = await fetch('/api/logical/start', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });
    const data = await res.json();
    if (data.status == 'success') {
      logicalMatchId = data.match_id;
      logicalQuestions = data.question;
      if (data.current_question && data.total_question) {
        const progress = (data.current_question / data.total_question) * 100;
        document.getElementById('logical-progress').style.width = `${progress}%`;
      }
      showLogicalQuestion();
    } else {
      if (data.status == 'error') {
        logicalQuestionText.innerText = data.message || 'Gagal memulai permainan. Silakan coba lagi.';
        return;
      }
      logicalQuestionText.innerText = 'Terjadi kesalahan tak terduga. Silakan coba lagi.';
    }

  };

  document.getElementById('logical-start-btn').addEventListener('click', startLogicalGame);

  async function showLogicalQuestion() {
    const q = logicalQuestions;
    if (!q) {
      showLogicalResult();
      return;
    }

    document.getElementById('logical-next-btn').style.display = 'none';

    logicalQuestionText.innerText = q.question_text;

    const container = document.getElementById('logical-answers-container');
    container.innerHTML = '';

    q.answers.forEach(a => {
      const div = document.createElement('div');
      div.classList.add('logical-answer');
      div.innerText = a.answer_text;
      div.addEventListener('click', async () => {
        document.querySelectorAll('.logical-answer').forEach(x => x.classList.remove('selected'));
        div.classList.add('selected');
        document.querySelectorAll('.logical-answer').forEach(y => y.classList.add('disabled'));
        await sendLogicalAnswer(q.id, a.id);
      });
      container.appendChild(div);
    });
  }

  async function sendLogicalAnswer(questionId, answerId) {
    const res = await fetch('/api/logical/answer', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        match_id: logicalMatchId,
        question_id: questionId,
        answer_id: answerId
      })
    });
    const data = await res.json();
    if (data.status == 'error') {
      logicalQuestionText.innerText = data.message || 'Gagal menjawab permainan. Silakan coba lagi.';
      return;
    }
    document.getElementById('logical-next-btn').style.display = 'inline-block';

    document.getElementById('logical-next-btn').onclick = () => {
      if (data.complete == false) {
        if (data.current_question && data.total_question) {
          const progress = (data.current_question / data.total_question) * 100;
          document.getElementById('logical-progress').style.width = `${progress}%`;
          if (data.current_question == data.total_question) {
            document.getElementById('logical-next-btn').innerText = 'Lihat Hasil';
          }
        }
        logicalQuestions = data.next_question;
        showLogicalQuestion();
      } else {
        showLogicalResult();
      }
    };
  }

  async function showLogicalResult() {
    document.getElementById('logical-question-card').style.display = 'none';
    document.getElementById('logical-result-card').style.display = 'block';
    const res = await fetch(`/api/logical/finish`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        match_id: logicalMatchId
      })
    });
    const data = await res.json();
    if (data.status == 'error') {
      logicalQuestionText.innerText = data.message || 'Gagal melihat hasil permainan. Silakan coba lagi.';
      return;
    }
    document.getElementById('logical-total-point').innerText = data.total_point;
    document.getElementById('logical-iq').innerText = data.iq;
    document.getElementById('logical-category').innerText = data.category;
    document.getElementById('logical-message').innerText = data.message;
  }

  document.getElementById('logical-restart-btn').addEventListener('click', () => {
    startLogicalGame(); // Tanpa reload halaman
  });
</script>
@endsection