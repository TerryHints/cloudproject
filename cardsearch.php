<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riftcodex Card Browser</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f6fb;
      color: #1f2937;
    }

    nav {
      width: 100%;
      background: #333;
      padding: 1rem;
      position: fixed;
      top: 0;
      left: 0;
      display: flex;
      justify-content: center;
      gap: 20px;
      z-index: 100;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.2s;
    }

    nav a:hover {
      color: #D13639;
    }

    .page {
      width: 100%;
      max-width: 1000px;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
      padding: 28px;
      margin: 80px auto 40px;
    }

    h1 {
      margin-bottom: 12px;
      font-size: 2rem;
      color: #0f172a;
    }

    .lead {
      margin-bottom: 24px;
      color: #64748b;
      line-height: 1.6;
    }

    .controls {
      display: flex;
      gap: 12px;
      margin-bottom: 24px;
      flex-wrap: wrap;
      align-items: center;
    }

    .controls select,
    .controls button {
      border: 1px solid #d1d5db;
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 1rem;
    }

    .controls button {
      background: #1d4ed8;
      color: #ffffff;
      cursor: pointer;
      border-color: transparent;
      font-weight: 600;
    }

    .controls button:disabled {
      opacity: 0.55;
      cursor: default;
    }

    #message {
      padding: 14px 16px;
      margin-bottom: 20px;
      background: #f8fafc;
      border-left: 4px solid #3b82f6;
      border-radius: 8px;
      color: #334155;
      line-height: 1.5;
    }

    .card-list {
      display: grid;
      gap: 16px;
    }

    .card-item {
      display: grid;
      gap: 12px;
      padding: 18px;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      background: #ffffff;
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }

    .card-name {
      font-size: 1.05rem;
      font-weight: 700;
      color: #0f172a;
    }

    .meta-row {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      color: #475569;
      font-size: 0.9rem;
    }

    .meta-pill {
      background: #eef2ff;
      color: #3730a3;
      padding: 5px 10px;
      border-radius: 999px;
      font-size: 0.85rem;
    }

    .card-desc {
      color: #475569;
      line-height: 1.6;
      margin-top: 6px;
    }

    @media (max-width: 720px) {
      .page {
        margin: 100px 16px 40px;
        padding: 22px;
      }
    }
  </style>
</head>
<body>
  <nav>
    <a href="home2.php">Home</a>
    <a href="logout.php">Logout</a>
  </nav>

  <div class="page">
    <h1>Riftcodex Card Browser</h1>
    <p class="lead">Load set data from <strong>https://api.riftcodex.com</strong> and browse cards by set.</p>

    <div class="controls">
      <select id="set-select">
        <option value="">Loading available sets...</option>
      </select>
      <button id="refresh-sets" type="button">Refresh Sets</button>
    </div>

    <div class="controls">
      <button id="load-cards" type="button" disabled>Load Cards</button>
      <button id="prev-page" type="button" disabled>Previous</button>
      <button id="next-page" type="button" disabled>Next</button>
    </div>

    <p id="message">Select a set and click Load Cards.</p>
    <div id="card-list" class="card-list"></div>
  </div>

  <script>
    const baseUrl = 'https://api.riftcodex.com';
    const setSelect = document.getElementById('set-select');
    const refreshSetsBtn = document.getElementById('refresh-sets');
    const loadCardsBtn = document.getElementById('load-cards');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const message = document.getElementById('message');
    const cardList = document.getElementById('card-list');

    let availableSets = [];
    let currentPage = 1;
    let currentSetId = '';
    let totalCards = 0;
    const pageSize = 20;

    async function fetchJson(url) {
      const response = await fetch(url);
      if (!response.ok) {
        const body = await response.text();
        throw new Error(`HTTP ${response.status} ${response.statusText}: ${body}`);
      }
      return response.json();
    }

    async function fetchSets() {
      const endpoints = [`${baseUrl}/sets`, `${baseUrl}/api/sets`];
      setSelect.innerHTML = '<option value="">Loading available sets...</option>';
      setSelect.disabled = true;
      loadCardsBtn.disabled = true;
      message.textContent = 'Fetching available sets from the API...';
      cardList.innerHTML = '';

      try {
        let data = null;
        let usedUrl = '';

        for (const endpoint of endpoints) {
          try {
            data = await fetchJson(endpoint);
            usedUrl = endpoint;
            break;
          } catch (innerError) {
            console.warn('Set endpoint failed:', endpoint, innerError.message);
          }
        }

        if (!data) {
          throw new Error(`Could not load sets from ${endpoints.join(' or ')}`);
        }

        availableSets = Array.isArray(data) ? data : data.sets || [];
        if (!availableSets.length) {
          setSelect.innerHTML = '<option value="">No sets found</option>';
          message.textContent = `No sets were returned by the API (tried ${usedUrl}).`;
          return;
        }

        setSelect.innerHTML = '<option value="">-- Select a set --</option>';
        availableSets.forEach((set) => {
          const id = set.id || set.set_id || set.key || set.name || '';
          const label = set.name || set.id || set.set_id || '(Unnamed)';
          const count = set.card_count || (Array.isArray(set.cards) ? set.cards.length : '-');
          const option = document.createElement('option');
          option.value = id;
          option.textContent = `${label} (${count} cards)`;
          setSelect.appendChild(option);
        });

        message.textContent = 'Select a set and click Load Cards.';
        setSelect.disabled = false;
      } catch (error) {
        message.textContent = `Failed to load sets: ${error.message}`;
        console.error(error);
      }
    }

    async function fetchCards(page = 1) {
      if (!currentSetId) {
        message.textContent = 'Choose a set first.';
        return;
      }

      cardList.innerHTML = '';
      message.textContent = 'Loading cards...';
      loadCardsBtn.disabled = true;
      prevPageBtn.disabled = true;
      nextPageBtn.disabled = true;

      try {
        const url = `${baseUrl}/api/cards?limit=${pageSize}&page=${page}&set_id=${encodeURIComponent(currentSetId)}`;
        const response = await fetch(url);

        if (!response.ok) {
          const body = await response.text();
          throw new Error(`HTTP ${response.status} ${response.statusText}: ${body}`);
        }

        const data = await response.json();
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
          const cardSet = card.set || currentSetId;
          const badgeList = [];
          if (card.type) badgeList.push(`<span class="meta-pill">${escapeHtml(card.type)}</span>`);
          if (card.rarity) badgeList.push(`<span class="meta-pill">${escapeHtml(card.rarity)}</span>`);
          if (card.faction) badgeList.push(`<span class="meta-pill">${escapeHtml(card.faction)}</span>`);

          item.innerHTML = `
            <div class="card-header">
              <div class="card-name">${escapeHtml(cardName)}</div>
              <div class="meta-row">
                <span class="meta-pill">Set: ${escapeHtml(cardSet)}</span>
                ${badgeList.join('')}
              </div>
            </div>
            ${card.description ? `<div class="card-desc">${escapeHtml(card.description)}</div>` : ''}
          `;

          cardList.appendChild(item);
        });

        prevPageBtn.disabled = currentPage <= 1;
        nextPageBtn.disabled = currentPage >= Math.ceil(totalCards / pageSize);
      } catch (error) {
        message.textContent = `Failed to load cards: ${error.message}`;
        console.error(error);
      } finally {
        loadCardsBtn.disabled = false;
      }
    }

    function escapeHtml(value) {
      const span = document.createElement('span');
      span.textContent = value;
      return span.innerHTML;
    }

    setSelect.addEventListener('change', () => {
      currentSetId = setSelect.value;
      loadCardsBtn.disabled = !currentSetId;
      cardList.innerHTML = '';
      message.textContent = currentSetId ? 'Ready to load cards.' : 'Select a set and click Load Cards.';
    });

    refreshSetsBtn.addEventListener('click', fetchSets);
    loadCardsBtn.addEventListener('click', () => fetchCards(1));
    prevPageBtn.addEventListener('click', () => fetchCards(Math.max(1, currentPage - 1)));
    nextPageBtn.addEventListener('click', () => fetchCards(currentPage + 1));

    fetchSets();
  </script>
</body>
</html>
