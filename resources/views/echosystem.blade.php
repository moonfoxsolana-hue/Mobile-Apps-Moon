<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mystic Nusa — Board Base (1-100)</title>
  <style>
    :root{
      --cols: 10;
      --rows: 10;
      --board-size: min(880px, 92vw);
      --gap: 6px;
      --tile-bg: rgba(0,0,0,0.45);
      --num-color: #d9b76a;
      --num-shadow: rgba(0,0,0,0.6);
      --font: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
    }
    *{box-sizing:border-box}
    body{
      font-family:var(--font);
      background:#0b0b0b;
      color:#fff;
      margin:20px;
      display:flex;
      gap:20px;
      align-items:flex-start;
      justify-content:center;
      min-height:100vh;
      padding-bottom:40px;
    }

    .panel{
      width:340px;
      max-width:38vw;
      background:linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
      border:1px solid rgba(255,255,255,0.04);
      padding:14px;
      border-radius:10px;
      color: #e6e6e6;
    }

    .board-wrap{
      width:var(--board-size);
      max-width:92vw;
      border-radius:12px;
      padding:10px;
      background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(0,0,0,0.25));
      box-shadow: 0 8px 30px rgba(0,0,0,0.7) inset;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .board{
      width:100%;
      height:100%;
      aspect-ratio: 1/1;
      max-width:var(--board-size);
      display:grid;
      grid-template-columns: repeat(var(--cols), 1fr);
      grid-template-rows: repeat(var(--rows), 1fr);
      gap:var(--gap);
      background: transparent;
      position:relative;
      padding:8px;
      border-radius:8px;
    }

    .tile{
      display:flex;
      align-items:center;
      justify-content:center;
      background:var(--tile-bg);
      border-radius:6px;
      font-weight:700;
      font-size:clamp(12px, 2.2vw, 20px);
      color:var(--num-color);
      text-shadow: 0 2px 0 var(--num-shadow);
      user-select:none;
      position:relative;
      overflow:hidden;
      border: 1px solid rgba(255,255,255,0.03);
    }

    /* When using sliced background image each tile will show a portion of the underlying image */
    .tile.sliced{
      background-repeat:no-repeat;
      background-size: calc(var(--cols) * 100%) calc(var(--rows) * 100%);
      background-position: 0% 0%;
      background-color: transparent;
      color: rgba(255,255,255,0.95);
      mix-blend-mode: screen;
      text-shadow: 0 4px 18px rgba(0,0,0,0.6);
    }

    .controls label{display:block;margin-bottom:6px;font-size:13px}
    .controls input[type="text"], .controls input[type="url"]{width:100%;padding:8px;border-radius:6px;border:1px solid rgba(255,255,255,0.06);background:transparent;color:inherit}
    .controls button{margin-top:8px;padding:10px 12px;border-radius:8px;border:0;background:#b8862b;color:#081018;font-weight:700;cursor:pointer}
    .controls small{display:block;margin-top:8px;color:#aaa}

    .hint{font-size:13px;color:#bdbdbd;margin-top:8px}

    /* responsive adjustments */
    @media (max-width:900px){
      body{flex-direction:column;align-items:center}
      .panel{max-width:92vw}
    }
  </style>
</head>
<body>
  <div class="board-wrap">
    <div id="board" class="board" role="grid" aria-label="Mystic Nusa board"></div>
  </div>

  <aside class="panel">
    <h3>Mystic Nusa — Board Generator</h3>
    <div class="controls">
      <label>Background image URL (leave empty for plain board)</label>
      <input id="bgUrl" type="url" placeholder="https://.../your-image.jpg" />

      <label style="margin-top:10px">Mode</label>
      <select id="mode" style="width:100%;padding:8px;border-radius:6px;background:transparent;color:inherit;border:1px solid rgba(255,255,255,0.06)">
        <option value="plain">Plain tiles (transparent)</option>
        <option value="overlay">One image as board background (not sliced)</option>
        <option value="slice" selected>Sliced image across tiles (stitchable)</option>
      </select>

      <button id="applyBtn">Apply Background</button>
      <button id="resetBtn" style="background:#6f6f6f;margin-left:6px">Reset</button>

      <small class="hint">Mode "Sliced" will slice the image across the 10x10 grid so you can edit each tile later and reassemble into one image if desired.</small>
    </div>

    <div style="margin-top:12px">
      <label>Board size</label>
      <input id="sizeInput" type="text" value="min(880px, 92vw)" />
      <small class="hint">Advanced: change <code>--board-size</code> to adjust output canvas size used for slicing.</small>
    </div>

    <div style="margin-top:12px">
      <label>Export Options</label>
      <button id="exportGrid">Export tiles as PNGs (sliced)</button>
      <small class="hint">Exports each tile as an individual image (works best when using sliced mode).</small>
    </div>

  </aside>

  <script>
    const cols = 10, rows = 10;
    const board = document.getElementById('board');
    const applyBtn = document.getElementById('applyBtn');
    const resetBtn = document.getElementById('resetBtn');
    const modeSelect = document.getElementById('mode');
    const bgUrlInput = document.getElementById('bgUrl');
    const exportBtn = document.getElementById('exportGrid');
    const sizeInput = document.getElementById('sizeInput');

    // generate 1..100 tiles and insert into DOM in classic zig-zag pattern (bottom-left -> top-right)
    function generateTiles(){
      board.innerHTML = '';
      // create array 1..100 mapping to zigzag
      const total = cols * rows;
      // We'll generate by rows from top to bottom but place numbers in classic snake numbering left-to-right on even rows
      // For ease of editing later, we'll visually place numbers 1..100 in increasing order left-to-right bottom-to-top.

      // compute width/height of board in px for export slicing
      for(let i=0;i<total;i++){
        const tile = document.createElement('div');
        tile.className = 'tile';
        const number = i + 1; // 1..100
        tile.textContent = number;
        tile.setAttribute('data-number', number);
        tile.setAttribute('role','gridcell');
        board.appendChild(tile);
      }

      // Re-order tiles to match classic snakes-and-ladders numbering where bottom row is left->right (1..10), next row right->left (11..20), etc.
      // We will physically reorder the grid items to reflect that visual pattern.
      const tiles = Array.from(board.children);
      const reordered = [];
      for(let r=0;r<rows;r++){
        const rowIndex = r; // 0 = top row in DOM order, but we want bottom row to be numbers 1..10
      }
      // To produce the classic board orientation (1 bottom-left), we'll rearrange using CSS grid-auto-flow. Simpler: we'll set the board's flex order via grid-auto-flow by reversing the rows using transform.
      // Instead, apply CSS trick: rotate the board 180deg and also rotate each tile 180deg so visuals remain upright but numbering fills bottom->top.
      board.style.transform = 'rotate(180deg)';
      Array.from(board.children).forEach(t=> t.style.transform = 'rotate(180deg)');
    }

    generateTiles();

    function applyBackground(){
      const url = bgUrlInput.value.trim();
      const mode = modeSelect.value;
      // update CSS variable for board size if changed
      document.documentElement.style.setProperty('--board-size', sizeInput.value || 'min(880px,92vw)');

      const tiles = Array.from(board.children);

      if(!url){
        tiles.forEach(t=>{ t.classList.remove('sliced'); t.style.backgroundImage = ''; });
        board.style.backgroundImage = '';
        return;
      }

      if(mode === 'plain'){
        tiles.forEach(t=>{ t.classList.remove('sliced'); t.style.backgroundImage = '' });
        board.style.backgroundImage = `url(${url})`;
        board.style.backgroundSize = 'cover';
        board.style.backgroundPosition = 'center';
      }

      if(mode === 'overlay'){
        tiles.forEach(t=>{ t.classList.remove('sliced'); t.style.backgroundImage = '' });
        board.style.backgroundImage = `url(${url})`;
        board.style.backgroundSize = 'contain';
        board.style.backgroundPosition = 'center';
        board.style.backgroundTransform = 'rotate(180deg)'; // keep consistent with tile rotation
      }

      if(mode === 'slice'){
        board.style.backgroundImage = '';
        tiles.forEach((tile, idx)=>{
          tile.classList.add('sliced');
          // compute row/col for this tile in visual board (bottom-left is 1)
          const n = idx + 1; // 1..100
          // Determine row and column in classic snakes numbering where bottom row is 1..10 left->right
          const zeroIndex = n - 1;
          const rowFromBottom = Math.floor(zeroIndex / cols); // 0..9
          let colInRow = zeroIndex % cols; // 0..9
          // if rowFromBottom is odd, numbering goes right->left
          if(rowFromBottom % 2 === 1) colInRow = cols - 1 - colInRow;
          // convert to row from top (for computing background position)
          const rowFromTop = (rows - 1) - rowFromBottom;

          // background-size should be (cols*100%) (rows*100%) and background-position goes from 0%..100% across cols-1
          const posX = (colInRow / (cols - 1)) * 100; // percent
          const posY = (rowFromTop / (rows - 1)) * 100; // percent
          tile.style.backgroundImage = `url(${url})`;
          tile.style.backgroundPosition = `${posX}% ${posY}%`;
          tile.style.backgroundSize = `${cols * 100}% ${rows * 100}%`;
        });
      }
    }

    applyBtn.addEventListener('click', ()=>{
      applyBackground();
    });

    resetBtn.addEventListener('click', ()=>{
      bgUrlInput.value = '';
      board.style.backgroundImage = '';
      Array.from(board.children).forEach(t=>{ t.classList.remove('sliced'); t.style.backgroundImage = ''; t.style.backgroundPosition = ''; t.style.backgroundSize = ''; });
    });

    // Export each tile as PNG (works best when sliced mode is active)
    exportBtn.addEventListener('click', async ()=>{
      const tiles = Array.from(board.children);
      const mode = modeSelect.value;
      if(mode !== 'slice'){
        alert('Switch to "Sliced" mode before exporting tiles (so each tile contains a portion of the background image).');
        return;
      }

      // For each tile, render it to canvas and download image
      for(const tile of tiles){
        await exportTile(tile);
      }
      alert('Export finished (tiles downloaded).');
    });

    async function exportTile(tile){
      // Create a canvas sized to tile's layout pixels
      const rect = tile.getBoundingClientRect();
      const style = getComputedStyle(tile);
      const w = Math.round(rect.width);
      const h = Math.round(rect.height);
      const bg = style.backgroundImage; // url("...")
      if(!bg || bg === 'none') return;
      // draw using canvas
      const canvas = document.createElement('canvas');
      canvas.width = w; canvas.height = h;
      const ctx = canvas.getContext('2d');
      // fill transparent background
      ctx.fillStyle = 'rgba(0,0,0,0)';
      ctx.fillRect(0,0,w,h);

      // draw the background image using same positioning logic
      // extract url
      const url = bg.slice(5,-2);
      const img = await loadImage(url);
      // compute source cropping: since we used background-size = cols*100% rows*100% and background-position = posX posY
      const pos = style.backgroundPosition.split(' ');
      const posX = parseFloat(pos[0]);
      const posY = parseFloat(pos[1]);
      const bgW = img.width;
      const bgH = img.height;
      // the tile is showing a region centered at (posX% * bgW, posY% * bgH) of an image scaled to (cols*bgW, rows*bgH)??
      // Simpler (robust) approach: draw scaled image so that image covers (cols * w) by (rows * h) and then copy the correct tile portion
      const scaledW = cols * w;
      const scaledH = rows * h;
      ctx.drawImage(img, 0, 0, scaledW, scaledH);
      // we actually want only the top-left w,h portion corresponding to this tile
      // to achieve correct slice, we'll create an offscreen canvas of scaled size then draw portion
      const off = document.createElement('canvas');
      off.width = scaledW; off.height = scaledH;
      const offCtx = off.getContext('2d');
      offCtx.drawImage(img, 0, 0, scaledW, scaledH);
      // determine tile's col/row indexes with same logic used earlier
      const n = Number(tile.getAttribute('data-number'));
      const zeroIndex = n - 1;
      const rowFromBottom = Math.floor(zeroIndex / cols);
      let colInRow = zeroIndex % cols;
      if(rowFromBottom % 2 === 1) colInRow = cols - 1 - colInRow;
      const rowFromTop = (rows - 1) - rowFromBottom;
      const sx = colInRow * w;
      const sy = rowFromTop * h;
      // copy the section and export
      const tileImgData = offCtx.getImageData(sx, sy, w, h);
      const final = document.createElement('canvas'); final.width = w; final.height = h;
      final.getContext('2d').putImageData(tileImgData, 0, 0);
      // draw the number overlay so exported tiles remain labeled
      const fctx = final.getContext('2d');
      fctx.font = `${Math.round(w*0.18)}px sans-serif`;
      fctx.textAlign = 'center'; fctx.textBaseline = 'middle';
      fctx.fillStyle = 'rgba(255,255,255,0.95)';
      fctx.fillText(tile.getAttribute('data-number'), w/2, h/2);

      const dataUrl = final.toDataURL('image/png');
      const a = document.createElement('a'); a.href = dataUrl; a.download = `tile-${tile.getAttribute('data-number')}.png`; a.click();
    }

    function loadImage(src){
      return new Promise((res, rej)=>{
        const img = new Image(); img.crossOrigin = 'anonymous'; img.onload = ()=>res(img); img.onerror = rej; img.src = src;
      });
    }

    // small helper to auto-apply if user pastes a url
    bgUrlInput.addEventListener('change', ()=>applyBackground());

    // initial apply
    applyBackground();
  </script>
</body>
</html>
