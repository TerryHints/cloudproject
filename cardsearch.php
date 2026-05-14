<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riftcodex Origins Card Search</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f6fb;
      color: #1f2937;
      display: flex;
      justify-content: center;
      min-height: 100vh;
      padding: 24px;
    }
    .page {
      width: 100%;
      max-width: 860px;
      background: #ffffff;
      border-radius: 18px;
      box-shadow: 0 18px 45px rgba(25, 29, 38, 0.12);
      padding: 28px;
    }
    h1 {
      margin: 0 0 14px;
      font-size: 2rem;
      letter-spacing: -0.03em;
    }
    p.lead {
      margin: 0 0 24px;
      color: #475569;
      line-height: 1.6;
    }
    .controls {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-bottom: 20px;
    }
    .controls button {
      border: none;
      border-radius: 10px;
      padding: 12px 18px;
      cursor: pointer;
      font-weight: 600;
      background: #1d4ed8;
      color: white;
      transition: transform 0.15s ease, filter 0.15s ease;
    }
    .controls button:disabled {
      opacity: 0.45;
      cursor: default;
      transform: none;
    }
    .controls button:hover:not(:disabled) {
      filter: brightness(1.05);
      transform: translateY(-1px);
    }
    #message {
      margin-bottom: 18px;
      color: #334155;
    }
    .card-list {
      display: grid;
      gap: 14px;
    }
    .card-item {
      padding: 16px 18px;
      border-radius: 14px;
      border: 1px solid #e2e8f0;
      background: #f8fafc;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
    }
    .card-item strong {
      font-size: 1rem;
      color: #0f172a;
    }
    .card-item span {
      color: #475569;
      font-size: 0.95rem;
      white-space: nowrap;
    }
    @media (max-width: 640px) {
      .controls {
        flex-direction: column;
      }
      .card-item {
        flex-direction: column;
        align-items: flex-start;
      }
      .card-item span {
        margin-top: 8px;
      }
    }
  </style>
</head>
<body>
  <div class="page">
    <h1>Origins Set Cards</h1>
    <p class="lead">This page fetches the first 20 cards from the Riftcodex Origins set (<strong>OGN</strong>) and displays them with pagination.</p>
    <div class="controls">
      <button id="load-btn" type="button">Load Origins Cards</button>
      <button id="prev-btn" type="button" disabled>Previous</button>
      <button id="next-btn" type="button" disabled>Next</button>
    </div>
    <p id="message">Click "Load Origins Cards" to fetch the data.</p>
    <div id="card-list" class="card-list"></div>
  </div>
  <script>
    const pageSize = 20;
    let currentPage = 1;
    const totalPages = { value: 1 };
    const message = document.getElementById('message');
    const cardList = document.getElementById('card-list');
    const loadBtn = document.getElementById('load-btn');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    async function fetchCards(page = 1) {
      message.textContent = 'Loading cards from the Origins set...';
      cardList.innerHTML = '';
      prevBtn.disabled = true;
      nextBtn.disabled = true;

      try {
        const response = await fetch(`https://api.riftcodex.com/api/cards?limit=${pageSize}&page=${page}&set_id=ogn`, {
          headers: {
            'X-Riot-Token': 'RGAPI-9af98452-0f95-4b24-a5de-0c8a91112c6c'
          }
        });
        if (!response.ok) {
          throw new Error(`HTTP ${response.status} ${response.statusText}`);
        }

        const data = await response.json();
        const items = data.items || [];
        const total = Number(data.total || 0);
        totalPages.value = Math.max(1, Math.ceil(total / pageSize));
        currentPage = page;

        if (!items.length) {
          message.textContent = 'No cards were returned for this page.';
          return;
        }

        message.textContent = `Showing ${items.length} cards from page ${currentPage} of ${totalPages.value} (total ${total}).`;

        for (const card of items) {
          const item = document.createElement('div');
          item.className = 'card-item';
          item.innerHTML = `<strong>${escapeHtml(card.name || 'Unnamed card')}</strong><span>Set: ${escapeHtml(card.set || 'Unknown')}</span>`;
          cardList.appendChild(item);
        }

        prevBtn.disabled = currentPage <= 1;
        nextBtn.disabled = currentPage >= totalPages.value;
      } catch (error) {
        message.textContent = `Fetch failed: ${error.message}`;
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
      if (currentPage < totalPages.value) fetchCards(currentPage + 1);
    });
  </script>
</body>
</html>
