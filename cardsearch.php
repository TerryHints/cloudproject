<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RiftCodex Cards</title>

    <style>

        body {
            margin: 0;
            padding: 20px;
            background: #111827;
            font-family: Arial, sans-serif;
            color: white;
        }

        h1 {
            margin-bottom: 10px;
        }

        #status {
            margin-bottom: 20px;
            color: #9ca3af;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }

        .card {
            background: #1f2937;
            border-radius: 14px;
            overflow: hidden;
            transition: 0.2s ease;
            box-shadow: 0 4px 14px rgba(0,0,0,0.35);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
        }

        .card img {
            width: 100%;
            display: block;
        }

        .card-content {
            padding: 15px;
        }

        .card-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            margin-right: 6px;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .type {
            background: #2563eb;
        }

        .rarity-common {
            background: #6b7280;
        }

        .rarity-uncommon {
            background: #059669;
        }

        .rarity-rare {
            background: #7c3aed;
        }

        .rarity-epic {
            background: #dc2626;
        }

        .set {
            background: #d97706;
        }

        .text {
            margin-top: 12px;
            color: #d1d5db;
            line-height: 1.5;
            font-size: 14px;
        }

    </style>
</head>
<body>

<h1>RiftCodex Cards</h1>

<div id="status">Loading cards...</div>

<div class="card-grid" id="cardGrid"></div>

<script>

async function loadCards() {

    try {

        const response = await fetch(
            'https://api.riftcodex.com/cards'
        );

        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }

        const data = await response.json();

        const items = data.items || [];

        document.getElementById('status').innerText =
            'Loaded ' + items.length + ' cards';

        const grid = document.getElementById('cardGrid');

        items.forEach(card => {

            const rarity =
                card.classification?.rarity?.toLowerCase() || 'common';

            const html = `
                <div class="card">

                    <img src="${card.set?.media?.image_url || ''}">

                    <div class="card-content">

                        <div class="card-title">
                            ${card.name || 'Unknown'}
                        </div>

                        <span class="badge type">
                            ${card.classification?.type || 'Unknown'}
                        </span>

                        <span class="badge rarity-${rarity}">
                            ${card.classification?.rarity || 'Common'}
                        </span>

                        <span class="badge set">
                            ${card.set?.label || 'Unknown Set'}
                        </span>

                        <div class="text">
                            ${card.plain || 'No description available.'}
                        </div>

                    </div>

                </div>
            `;

            grid.innerHTML += html;
        });

    } catch (error) {

        console.error(error);

        document.getElementById('status').innerText =
            'Failed to load cards: ' + error.message;
    }
}

loadCards();

</script>

</body>
</html>