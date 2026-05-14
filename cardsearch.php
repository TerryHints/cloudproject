<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riftbound Cards</title>
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
      display: grid;
      grid-template-columns: 80px 1fr;
      gap: 12px;
      align-items: flex-start;
    }
    .card-art {
      width: 80px;
      height: 100px;
      border-radius: 6px;
      background: #e5e7eb;
      overflow: hidden;
    }
    .card-art img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .card-details {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .card-name {
      font-size: 1rem;
      font-weight: 700;
      color: #0f172a;
      margin: 0;
    }
    .card-meta {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      font-size: 0.85rem;
    }
    .badge {
      background: #dbeafe;
      color: #1e40af;
      padding: 4px 8px;
      border-radius: 4px;
      font-weight: 500;
    }
    .badge.rarity {
      background: #fef3c7;
      color: #b45309;
    }
    .badge.faction {
      background: #ddd6fe;
      color: #5b21b6;
    }
    .card-desc {
      font-size: 0.9rem;
      color: #64748b;
      line-height: 1.4;
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
  <nav style="width: 100%; background: #333; padding: 1rem; position: fixed; top: 0; left: 0; display: flex; justify-content: center; gap: 20px; margin: 0;">
    <a href="home2.php" style="color: white; text-decoration: none; font-weight: 500;">Home</a>
    <a href="logout.php" style="color: white; text-decoration: none; font-weight: 500;">Logout</a>
  </nav>
  <div class="page" style="margin-top: 60px;">
    <h1>Riftbound Cards</h1>
    <p class="lead">Browse all cards from the Riftbound game. Select a set to view cards.</p>
    <div class="controls">
      <select id="set-select" style="padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 1rem; cursor: pointer;">
        <option value="">-- Select a set --</option>
      </select>
    </div>
    <p id=message = document.getElementById('message');
    const cardList = document.getElementById('card-list');
    const setSelect = document.getElementById('set-select');
    let allSets = [];

    async function fetchContent() {
      message.textContent = 'Loading content...';
      cardList.innerHTML = '';
      setSelect.disabled = true;

      try {
        const response = await fetch('https://api.riftcodex.com/riftbound/content/v1/content', {
          headers: {
            'X-Riot-Token': 'RGAPI-9af98452-0f95-4b24-a5de-0c8a91112c6c'
          }
        });
        if (!response.ok) {
          throw new Error(`HTTP ${response.status} ${response.statusText}`);
        }

        const data = await response.json();
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
        setSelect.disabled = false;
      } catch (error) {
        message.textContent = `Fetch failed: ${error.message}`;
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
          ? `<div class="card-art"><img src="${escapeHtml(card.art.thumbnailURL)}" alt="${escapeHtml(card.name)}"></div>`
          : `<div class="card-art"></div>`;
        
        const badges = [
          card.type ? `<span class="badge">${escapeHtml(card.type)}</span>` : '',
          card.rarity ? `<span class="badge rarity">${escapeHtml(card.rarity)}</span>` : '',
          card.faction ? `<span class="badge faction">${escapeHtml(card.faction)}</span>` : ''
        ].filter(b => b).join('');

        const statsHtml = card.stats 
          ? `<div style="font-size: 0.85rem; color: #475569;">Cost: ${card.stats.cost || 0} | Power: ${card.stats.power || 0} | Might: ${card.stats.might || 0}</div>`
          : '';

        item.innerHTML = artHtml + `
          <div class="card-details">
            <h3 class="card-name">${escapeHtml(card.name || 'Unnamed')}</h3>
            <div class="card-meta">${badges}</div>
            ${statsHtml}
            ${card.description ? `<p class="card-desc">${escapeHtml(card.description)}</p>` : ''}
          </div>
        `;
        cardList.appendChild(item);
      }
    }

    function escapeHtml(value) {
      const div = document.createElement('div');
      div.textContent = value;
      return div.innerHTML;
    }

    setSelect.addEventListener('change', (e) => {
      if (e.target.value === '') {
        cardList.innerHTML = '';
        message.textContent = 'Select a set to view cards.';
      } else {
        displaySetCards(parseInt(e.target.value));
      }
    });

    // Load content on page load
    fetchContent( if (currentPage > 1) fetchCards(currentPage - 1);
    });
    nextBtn.addEventListener('click', () => {
      if (currentPage < totalPages.value) fetchCards(currentPage + 1);
    });
  </script>
</body>
</html>
