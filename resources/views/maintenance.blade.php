<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Mystic Nusa — Maintenance</title>
  <style>
    :root{
      --bg1:#0b0712;
      --bg2:#1b1530;
      --accent:#7c4dff;
      --muted:#bfb8d6;
      --glass: rgba(255,255,255,0.04);
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family: Inter,ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial;
      background: radial-gradient(1200px 600px at 10% 10%, rgba(124,77,255,0.10), transparent),
                  radial-gradient(800px 400px at 90% 90%, rgba(255,200,80,0.03), transparent),
                  linear-gradient(180deg,var(--bg1),var(--bg2));
      color: #e9e6f7;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:24px;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      overflow:hidden;
    }

    /* floating orbs */
    .orb{position:absolute;border-radius:999px;filter:blur(30px);opacity:0.35;mix-blend-mode:screen}
    .orb.o1{width:380px;height:380px;left:-80px;top:-60px;background:linear-gradient(135deg,#5f3bd6,#b07bff)}
    .orb.o2{width:260px;height:260px;right:-60px;bottom:-40px;background:linear-gradient(135deg,#ffb86b,#ff6b9b);opacity:0.15}
    .orb.o3{width:160px;height:160px;left:20%;bottom:10%;background:linear-gradient(135deg,#49d1e0,#7cf5ff);opacity:0.08}

    /* card */
    .card{
      width:100%;
      max-width:980px;
      background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      border-radius:18px;
      padding:36px;
      box-shadow: 0 8px 40px rgba(3,2,10,0.6);
      backdrop-filter: blur(6px) saturate(1.1);
      position:relative;
      border: 1px solid rgba(255,255,255,0.03);
      display:flex;
      gap:28px;
      align-items:center;
    }

    .left{
      flex:1 1 360px;
      min-width:260px;
    }
    .right{
      width:320px;
      min-width:220px;
      display:flex;
      flex-direction:column;
      gap:14px;
    }

    h1{margin:0 0 6px 0;font-size:28px;letter-spacing:0.6px}
    p.lead{margin:0;color:var(--muted);line-height:1.6}

    .symbol{
      width:164px;height:84px;border-radius:16px;display:grid;place-items:center;font-weight:700;font-size:20px;
      background: linear-gradient(135deg, rgba(124,77,255,0.14), rgba(255,255,255,0.02));
      border:1px solid rgba(124,77,255,0.15);
      box-shadow: inset 0 -6px 20px rgba(0,0,0,0.25), 0 8px 30px rgba(124,77,255,0.06);
    }

    .moon{
      width:140px;height:140px;border-radius:50%;display:grid;place-items:center;background:conic-gradient(from 200deg at 50% 50%, rgba(255,255,255,0.06), rgba(0,0,0,0.02));
      box-shadow: 0 12px 50px rgba(6,4,24,0.6), inset 0 -6px 18px rgba(255,255,255,0.02);
      position:relative;overflow:hidden;
    }
    .moon svg{width:88%;height:88%;opacity:0.95}

    .status{
      display:flex;gap:10px;align-items:center;font-size:14px;color:var(--muted)
    }
    .dot{width:10px;height:10px;border-radius:50%;background:linear-gradient(180deg,#9b6bff,#5f3bd6);box-shadow:0 4px 14px rgba(95,59,214,0.25)}

    .btn{
      display:inline-block;padding:10px 16px;border-radius:10px;background:linear-gradient(90deg,var(--accent),#b07bff);color:#0b0712;text-decoration:none;font-weight:700;border:0;cursor:pointer;
      box-shadow: 0 6px 20px rgba(124,77,255,0.14);
    }

    small{color:var(--muted);display:block}

    .meta{font-size:13px;color:var(--muted);line-height:1.5}

    /* subtle star field */
    .stars{position:absolute;inset:0;pointer-events:none}
    .stars::after{content:"";position:absolute;inset:0;background-image:radial-gradient(#fff 1px, transparent 1px);background-size:3px 3px;opacity:0.02;transform:translateZ(0)}

    /* animations */
    @keyframes floaty{0%{transform:translateY(0px)}50%{transform:translateY(-10px)}100%{transform:translateY(0px)}}
    @keyframes drift{0%{transform:translateX(0px)}50%{transform:translateX(8px)}100%{transform:translateX(0px)}}
    .moon{animation:floaty 6s ease-in-out infinite}
    .orb.o1{animation:drift 12s ease-in-out infinite}

    /* responsive */
    @media (max-width:820px){
      .card{flex-direction:column;align-items:center;text-align:center;padding:28px}
      .right{width:100%}
    }
  </style>
</head>
<body>
  <div class="stars" aria-hidden="true"></div>
  <div class="orb o1" aria-hidden="true"></div>
  <div class="orb o2" aria-hidden="true"></div>
  <div class="orb o3" aria-hidden="true"></div>

  <main class="card" role="main" aria-labelledby="title">
    <section class="left">
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:14px">
        <div class="symbol"><img src="/images/logo.png" alt="Mystic Nusa Logo" style="width:100%;height:100%;border-radius:16px;"></div>
        <div>
          <h1 id="title">Mystic Nusa sedang maintenance</h1>
          <p class="lead">Terima kasih sudah berkunjung. Tim kami sedang melakukan pemeliharaan agar pengalaman kamu lebih baik. Kami akan segera kembali.</p>
        </div>
      </div>

      <div style="display:flex;gap:12px;align-items:center;margin-top:8px">
        <div class="status"><span class="dot" aria-hidden></span><span>Perkiraan downtime singkat</span></div>
        <div style="flex:1"></div>
        <a class="btn" href="#" onclick="location.reload();return false">Refresh</a>
      </div>

      <div style="margin-top:18px" class="meta">
        <small>Butuh bantuan atau ingin memberi tahu kami tentang sesuatu?</small>
        <small>Email: <strong>mysticnusa@gmail.com</strong> &nbsp;•&nbsp; Twitter: <strong>@mysticnusa</strong></small>
      </div>
    </section>

    <aside class="right" aria-hidden="false">
      <div style="display:flex;align-items:center;justify-content:center">
        <div class="moon" aria-hidden="true">
          <!-- simple moon SVG -->
          <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <defs>
              <linearGradient id="g1" x1="0" x2="1">
                <stop offset="0" stop-color="#fff" stop-opacity="0.9"/>
                <stop offset="1" stop-color="#d6c8ff" stop-opacity="0.9"/>
              </linearGradient>
            </defs>
            <circle cx="32" cy="32" r="20" fill="url(#g1)" />
            <g fill="rgba(11,7,18,0.12)">
              <circle cx="40" cy="24" r="3"/>
              <circle cx="28" cy="40" r="2"/>
              <circle cx="20" cy="26" r="1.6"/>
            </g>
          </svg>
        </div>
      </div>

      <div style="padding:8px 6px;text-align:center">
        <strong style="display:block">Status</strong>
        <div style="margin-top:8px" class="meta">Sedang dalam proses Migrasi Server</div>
      </div>

    </aside>
  </main>

</body>
</html>
