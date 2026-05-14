<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riftbound Cards</title>
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
      align-items: center;
    }

    #set-select {
      padding: 10px 14px;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      flex: 1;
      max-width: 300px;
    }

    #message {
      padding: 12px 16px;
      margin-bottom: 20px;
      background: #f0fdf4;
      border-left: 4px solid #22c55e;
      border-radius: 4px;
      color: #166534;
      font-size: 0.95rem;
    }

    .card-list {
      display: grid;
      gap: 16px;
    }

    .card-item {
      display: grid;
      grid-template-columns: 90px 1fr;
      gap: 16px;
      padding: 16px;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      background: #f9fafb;
      transition: box-shadow 0.2s;
    }

    .card-item:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .card-art {
      width: 90px;
      height: 120px;
      border-radius: 6px;
      background: #e5e7eb;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .card-art img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .card-details {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .card-name {
      font-size: 1.05rem;
      font-weight: 700;
      color: #0f172a;
    }

    .card-meta {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    .badge {
      display: inline-block;
      padding: 4px 10px;
      background: #dbeafe;
      color: #1e40af;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .badge.rarity {
      background: #fef3c7;
      color: #b45309;
    }

    .badge.faction {
      background: #ddd6fe;
      color: #5b21b6;
    }

    .card-stats {
      font-size: 0.9rem;
      color: #475569;
    }

    .card-desc {
      font-size: 0.9rem;
      color: #64748b;
      line-height: 1.5;
      margin-top: 4px;
    }

    @media (max-width: 768px) {
      .page {
        margin-top: 90px;
        padding: 20px;
      }

      .card-item {
        grid-template-columns: 1fr;
      }

      .card-art {
        width: 100%;
        height: 150px;
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
    <h1>Riftbound Cards</h1>
    <p class="lead">Browse all cards from the Riftbound game. Select a set to view cards.</p>

    <div class="controls">
      <select id="set-select">
        <option value="">-- Loading sets --</option>
      </select>
    </div>

    <p id="message">Loading content...</p>
    <div id="card-list" class="card-list"></div>
  </div>

  <script>
    const message = document.getElementById('message');
    const cardList = document.getElementById('card-list');
    const setSelect = document.getElementById('set-select');
    let allSets = [];

    async function fetchContent() {
      const url = 'https://api.riftcodex.com/riftbound/content/v1/content';
      console.log('Fetching from:', url);
      
      try {
        const response = await fetch(url, {
          headers: {
            'X-Riot-Token': 'RGAPI-9af98452-0f95-4b24-a5de-0c8a91112c6c'
          }
        });

        console.log('Response status:', response.status, response.statusText);

        if (!response.ok) {
          const errorText = await response.text();
          console.error('Error response:', errorText);
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        console.log('Data received:', data);
        
        allSets = data.sets || [];

        if (!allSets.length) {
          message.textContent = 'No sets found.';
          return;
        }

        setSelect.innerHTML = '<option value="">-- Select a set --</option>';
        allSets.forEach((set, index) => {
          const option = document.createElement('option');
          option.value = index;
          option.textContent = `${set.name} (${set.cards?.length || 0} cards)`;
          setSelect.appendChild(option);
        });

        message.textContent = `Loaded ${allSets.length} sets. Select one to view cards.`;
      } catch (error) {
        console.error('Fetch error:', error);
        message.textContent = `Error: ${error.message} — Check browser console for details.`;
      }
    }

    function displaySetCards(setIndex) {
      const set = allSets[setIndex];
      if (!set) return;

      cardList.innerHTML = '';
      const cards = set.cards || [];

      if (!cards.length) {
        message.textContent = `No cards in ${set.name}.`;
        return;
      }

      message.textContent = `Showing ${cards.length} cards from ${set.name}`;

      for (const card of cards) {
        const item = document.createElement('div');
        item.className = 'card-item';

        const artHtml = card.art?.thumbnailURL
          ? `<div class="card-art"><img src="${card.art.thumbnailURL}" alt="${card.name}" loading="lazy"></div>`
          : `<div class="card-art"></div>`;

        const badges = [];
        if (card.type) badges.push(`<span class="badge">${card.type}</span>`);
        if (card.rarity) badges.push(`<span class="badge rarity">${card.rarity}</span>`);
        if (card.faction) badges.push(`<span class="badge faction">${card.faction}</span>`);

        const stats = card.stats;
        const statsHtml = stats
          ? `<div class="card-stats">Cost: ${stats.cost || 0} | Power: ${stats.power || 0} | Might: ${stats.might || 0}</div>`
          : '';

        item.innerHTML = artHtml + `
          <div class="card-details">
            <div class="card-name">${card.name || 'Unnamed'}</div>
            <div class="card-meta">${badges.join('')}</div>
            ${statsHtml}
            ${card.description ? `<div class="card-desc">${card.description}</div>` : ''}
          </div>
        `;

        cardList.appendChild(item);
      }
    }

    setSelect.addEventListener('change', (e) => {
      if (e.target.value === '') {
        cardList.innerHTML = '';
        message.textContent = 'Select a set to view cards.';
      } else {
        displaySetCards(parseInt(e.target.value));
      }
    });

    // Load on page load
    fetchContent();
  </script>
</body>
</html>
      if (currentPage < totalPages.value) fetchCards(currentPage + 1);
    });
  </script>
</body>
</html>
