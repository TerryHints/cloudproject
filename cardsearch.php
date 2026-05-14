<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RiftCodex Cards</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        h1 {
            margin-bottom: 10px;
        }

        #status {
            margin-bottom: 20px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
        }

        th {
            background: #222;
            color: white;
        }

        img {
            width: 120px;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<h1>RiftCodex Cards</h1>

<div id="status">Loading cards...</div>

<table>
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Type</th>
            <th>Rarity</th>
            <th>Set</th>
        </tr>
    </thead>

    <tbody id="cards"></tbody>
</table>

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

        const table = document.getElementById('cards');

        items.forEach(card => {

            let image = '';

            if (
                card.set &&
                card.set.media &&
                card.set.media.image_url
            ) {
                image = `<img src="${card.set.media.image_url}">`;
            }

            const row = `
                <tr>
                    <td>${image}</td>
                    <td>${card.name || 'Unknown'}</td>
                    <td>${card.classification?.type || 'Unknown'}</td>
                    <td>${card.classification?.rarity || 'Unknown'}</td>
                    <td>${card.set?.label || 'Unknown'}</td>
                </tr>
            `;

            table.innerHTML += row;
        });

    } catch (error) {

        console.error(error);

        document.getElementById('status').innerText =
            'Failed to load card data: ' + error.message;
    }
}

loadCards();
</script>

</body>
</html>