<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riftcodex Origins Cards</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #eef2ff;
      color: #0f172a;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 24px;
    }

    .page {
      width: 100%;
      max-width: 1000px;
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
      padding: 28px;
      overflow: hidden;
    }

    h1 {
      margin-bottom: 10px;
      font-size: 2rem;
    }

    .lead {
      margin-bottom: 22px;
      color: #475569;
      line-height: 1.6;
    }

    .controls {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-bottom: 22px;
    }

    .controls button {
      padding: 12px 18px;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      color: #ffffff;
      background: #2563eb;
      transition: transform 0.15s ease, filter 0.15s ease;
    }

    .controls button:disabled {
      background: #93c5fd;
      cursor: not-allowed;
    }

    .controls button:hover:not(:disabled) {
      filter: brightness(1.05);
      transform: translateY(-1px);
    }

    #message {
      padding: 16px 18px;
      margin-bottom: 20px;
      border-left: 5px solid #2563eb;
      background: #eff6ff;
      color: #1e3a8a;
      border-radius: 12px;
      min-height: 56px;
      display: flex;
      align-items: center;
    }

    .card-list {
      display: grid;
      gap: 16px;
    }

    .card-item {
      padding: 18px;
      border-radius: 16px;
      border: 1px solid #e2e8f0;
      background: #f8fafc;
      transition: box-shadow 0.2s ease;
    }

    .card-item:hover {
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
      align-items: flex-start;
    }

    .card-name {
      font-size: 1.1rem;
      font-weight: 700;
      color: #111827;
    }

    .meta-row {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      color: #475569;
      font-size: 0.9rem;
    }

    .meta-pill {
      background: #dbeafe;
      color: #1d4ed8;
      padding: 5px 10px;
      border-radius: 999px;
      font-weight: 600;
    }

    .card-desc {
      margin-top: 12px;
      color: #475569;
      line-height: 1.7;
    }

    @media (max-width: 720px) {
      body {
        padding: 16px;
      }

      .page {
        padding: 20px;
        margin-top: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="page">
    <h1>Riftcodex Origins Cards</h1>
    <p class="lead">This page calls the exact Origins cards endpoint and displays card names, set, and metadata.</p>

    <div class="controls">
      <button id="load-btn" type="button">Load Page 1</button>
      <button id="prev-btn" type="button" disabled>Previous</button>
      <button id="next-btn" type="button" disabled>Next</button>
    </div>

    <div id="message">Ready to fetch Origins cards.</div>
    <div id="card-list" class="card-list"></div>
  </div>

  <script>
    const baseUrl = 'https://api.riftcodex.com/api/cards';
    const pageSize = 20;
    let currentPage = 1;
    let totalCards = 0;

    const message = document.getElementById('message');
    const cardList = document.getElementById('card-list');
    const loadBtn = document.getElementById('load-btn');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    async function fetchCards(page = 1) {
      const url = `${baseUrl}?limit=${pageSize}&page=${page}&set_id=ogn`;
      message.textContent = `Fetching page ${page}...`;
      cardList.innerHTML = '';
      loadBtn.disabled = true;
      prevBtn.disabled = true;
      nextBtn.disabled = true;

      try {
        const response = await fetch(url);
        const text = await response.text();

        if (!response.ok) {
          throw new Error(`HTTP ${response.status} ${response.statusText}: ${text}`);
        }

        const data = JSON.parse(text);
        const items = Array.isArray(data.items) ? data.items : [];
        totalCards = Number(data.total || items.length);
        currentPage = page;

        if (!items.length) {
          message.textContent = 'No cards were returned for this page.';
          return;
        }

        const totalPages = Math.max(1, Math.ceil(totalCards / pageSize));
        message.textContent = `Showing ${items.length} cards from page ${currentPage} of ${totalPages} (${totalCards} total).`;

        items.forEach((card) => {
          const item = document.createElement('div');
          item.className = 'card-item';
          const cardName = card.name || 'Unnamed card';
          const cardSet = card.set || 'OGN';
          const typeBadge = card.type ? `<span class="meta-pill">Type: ${escapeHtml(card.type)}</span>` : '';
          const rarityBadge = card.rarity ? `<span class="meta-pill">Rarity: ${escapeHtml(card.rarity)}</span>` : '';
          const description = card.description ? `<div class="card-desc">${escapeHtml(card.description)}</div>` : '';

          item.innerHTML = `
            <div class="card-header">
              <div class="card-name">${escapeHtml(cardName)}</div>
              <div class="meta-row">
                <span class="meta-pill">Set: ${escapeHtml(cardSet)}</span>
                ${typeBadge}
                ${rarityBadge}
              </div>
            </div>
            ${description}
          `;

          cardList.appendChild(item);
        });

        prevBtn.disabled = currentPage <= 1;
        nextBtn.disabled = currentPage >= Math.ceil(totalCards / pageSize);
      } catch (error) {
        message.textContent = `Fetch failed: ${error.message}`;
        console.error(error);
      } finally {
        loadBtn.disabled = false;
      }
    }

    function escapeHtml(value) {
      const div = document.createElement('div');
      div.textContent = value;
      return div.innerHTML;
    }

    loadBtn.addEventListener('click', () => fetchCards(1));
    prevBtn.addEventListener('click', () => {
      if (currentPage > 1) fetchCards(currentPage - 1);
    });
    nextBtn.addEventListener('click', () => {
      fetchCards(currentPage + 1);
    });
  </script>
</body>
</html>
