<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>AI for Astraotoshop</title>
  <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }

    form {
      margin-bottom: 20px;
    }

    #loading {
      display: none;
      color: green;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 350px;
      max-height: 80vh;
      overflow-y: auto;
      background-color: #ffffff;
      border: 1px solid #ddd;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
      z-index: 1000;
      animation: fadeIn 0.3s ease-in-out;
    }

    .modal-header {
      background-color: rgb(35, 60, 142);
      color: white;
      padding: 12px 15px;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-weight: bold;
    }

    .modal-body {
      padding: 15px;
      background-color: #f9f9f9;
    }

    .modal-close {
      background: none;
      border: none;
      color: white;
      font-size: 18px;
      cursor: pointer;
    }

    .modal-close:hover {
      color: #ffdddd;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .response {
      margin-top: 10px;
    }

    .modalai {
      display: none;
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 150px;
      max-height: 80vh;
      overflow-y: auto;
      background-color: rgb(35, 60, 142);
      color: white;
      border-radius: 8px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
      z-index: 1000;
      animation: fadeIn 0.3s ease-in-out;
      align-items: center;
      text-align: center;
      padding: 10px;
      cursor: pointer;
    }
  </style>
  <!-- Tambahkan library marked.js via CDN untuk parsing Markdown -->
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>

<body>
  <h1>Halaman AI Prompt</h1>

  <p>Masukkan API Key Anda untuk Groq (atau OpenAI-compatible API). Dapatkan di <a href="https://console.groq.com/keys">console.groq.com</a>.</p>
  <input type="text" id="api-key" placeholder="Masukkan API Key (e.g., gsk_...)" style="width: 300px;" value="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">
  <br><br>

  <form id="ai-form">
    <label for="page">Pilih Halaman:</label>
    <select name="page" id="page">
      <option value="login">Login (Gagal)</option>
      <option value="login">Login (Gagal)</option>
      <option value="pdp">PDP (Product Detail)</option>
      <option value="cart">Cart (Diskon & Pengiriman)</option>
      <option value="checkout">Checkout</option>
    </select>
    <br><br>
    <label for="user_message">User Message (opsional, default example):</label><br>
    <textarea name="user_message" id="user_message" rows="3" cols="50"></textarea>
    <br><br>
    <button type="submit">Generate Response</button>
  </form>

  <div id="loading">Memproses...</div>

  <!-- Modal Element -->
  <div id="modal" class="modal">
    <div class="modal-header">
      <span id="modal-title"></span>
      <button class="modal-close" onclick="closemodal()">&times;</button>
    </div>
    <div class="modal-body">
      <div id="modal-response" class="response"></div>
    </div>
  </div>
  <div id="modalai" class="modalai" onclick="openmodal()">AISY</div>

  <script>
    function closemodal() {
      document.getElementById('modal').style.display = 'none';
      document.getElementById('modalai').style.display = 'block';
    }

    function openmodal() {
      document.getElementById('modalai').style.display = 'none';
      document.getElementById('modal').style.display = 'block';
    }
    // Definisikan prompt berdasarkan halaman
    const prompts = {
      'login': {
        'system': "You are a concise customer service AI for e-commerce login page. Only handle login failures: explain common errors (e.g., wrong password, account not found), guide to reset password or register new account. Keep responses very short (under 100 words), empathetic, and direct user to retry or proceed to shopping after fix. Use line breaks or bullet points for readability. Do not discuss other topics.",
        'example_user': "Login saya gagal, kata sandi salah."
      },
      'pdp': {
        'system': "You are an efficient product explainer AI on e-commerce PDP. Provide brief, accurate explanations of product features, specs, reviews, or comparisons. Always suggest adding to cart or checking similar items to speed up shopping. Avoid off-topic responses; focus on guiding to cart.",
        'example_user': "Jelaskan fitur dari smartphone ini."
      },
      'cart': {
        'system': "You are a smart cart optimizer AI for e-commerce. Suggest the best discounts (e.g., apply codes, bundles), and recommend fastest or cheapest shipping based on user preference. Responses must be concise; calculate totals if possible, then urge to proceed to checkout. Only handle cart-related queries. response with indonesian language",
        'example_user': "Ada diskon apa di cart saya? Saya mau pengiriman tercepat."
      },
      'checkout': {
        'system': "You are a quick checkout assistant AI for e-commerce. Guide through payment methods, address confirmation, and order summary. Handle issues like payment failures briefly. Keep it short and positive; end with order confirmation and next steps (e.g., tracking). No other topics allowed.",
        'example_user': "Bagaimana cara checkout dengan kartu kredit?"
      }
    };

    document.getElementById('ai-form').addEventListener('submit', async function(e) {
      e.preventDefault();

      const apiKey = document.getElementById('api-key').value;
      if (!apiKey) {
        alert('Masukkan API Key terlebih dahulu!');
        return;
      }

      const page = document.getElementById('page').value;
      const userMessage = document.getElementById('user_message').value;
      const userContent = userMessage || prompts[page].example_user;

      const messages = [{
          role: 'system',
          content: prompts[page].system
        },
        {
          role: 'user',
          content: userContent
        }
      ];

      document.getElementById('loading').style.display = 'block';

      try {
        const response = await fetch('https://api.groq.com/openai/v1/chat/completions', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${apiKey}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            model: 'openai/gpt-oss-120b', // Ganti dengan model lain jika diinginkan, e.g., 'mixtral-8x7b-32768'
            messages: messages,
            temperature: 0.7,
            max_tokens: 500
          })
        });

        if (!response.ok) {
          throw new Error('Error dari API: ' + response.statusText);
        }

        const data = await response.json();
        const aiResponse = data.choices[0].message.content;

        // Gunakan marked.js untuk render Markdown ke HTML
        const renderedResponse = marked.parse(aiResponse);

        // Tampilkan di modal
        document.getElementById('modal-title').textContent = `AISY (AI Assistant for Astraotoshop)`;
        document.getElementById('modal-response').innerHTML = renderedResponse;
        document.getElementById('modal').style.display = 'block';
      } catch (error) {
        alert(`Error: ${error.message}`);
      } finally {
        document.getElementById('loading').style.display = 'none';
      }
    });
  </script>
</body>

</html>