<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RiftCodex Gallery</title>

<style>

body{
    margin:0;
    padding:20px;
    background:#0f172a;
    color:white;
    font-family:Arial,sans-serif;
}

h1{
    margin-bottom:10px;
}

#status{
    color:#94a3b8;
    margin-bottom:25px;
}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
    gap:24px;
}

.card{
    background:#111827;
    border-radius:18px;
    overflow:hidden;
    transition:.2s;
    box-shadow:0 8px 24px rgba(0,0,0,.35);
    border:1px solid rgba(255,255,255,.06);
}

.card:hover{
    transform:translateY(-6px);
    box-shadow:0 14px 32px rgba(0,0,0,.5);
}

.card-image{
    position:relative;
}

.card-image img{
    width:100%;
    display:block;
}

.rarity{
    position:absolute;
    top:12px;
    right:12px;
    padding:6px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:bold;
    backdrop-filter:blur(6px);
}

.common{ background:#6b7280; }
.uncommon{ background:#10b981; }
.rare{ background:#3b82f6; }
.epic{ background:#a855f7; }
.showcase{ background:#f59e0b; }

.content{
    padding:18px;
}

.name{
    font-size:20px;
    font-weight:bold;
    margin-bottom:10px;
}

.meta{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    margin-bottom:12px;
}

.badge{
    background:#1e293b;
    color:#cbd5e1;
    padding:5px 10px;
    border-radius:999px;
    font-size:12px;
}

.stats{
    display:flex;
    gap:14px;
    margin-bottom:14px;
    font-size:14px;
}

.stat{
    background:#0f172a;
    padding:8px 12px;
    border-radius:10px;
}

.text{
    color:#d1d5db;
    line-height:1.5;
    font-size:14px;
}

.flavor{
    margin-top:12px;
    color:#94a3b8;
    font-style:italic;
    font-size:13px;
}

</style>
</head>
<body>

<h1>RiftCodex Cards</h1>

<div id="status">Loading...</div>

<div class="grid" id="grid"></div>

<script>

async function loadCards(){

    try{

        const response = await fetch(
            'https://api.riftcodex.com/cards?dir=1&page=1&size=50'
        );

        if(!response.ok){
            throw new Error('HTTP ' + response.status);
        }

        const data = await response.json();

        const items = data.items || [];

        document.getElementById('status').innerText =
            `Loaded ${items.length} cards`;

        const grid = document.getElementById('grid');

        items.forEach(card => {

            const rarity =
                (card.classification?.rarity || 'common').toLowerCase();

            const energy =
                card.attributes?.energy ?? '-';

            const might =
                card.attributes?.might ?? '-';

            const power =
                card.attributes?.power ?? '-';

            const html = `

                <div class="card">

                    <div class="card-image">

                        <img src="${card.media?.image_url || ''}">

                        <div class="rarity ${rarity}">
                            ${card.classification?.rarity || 'Common'}
                        </div>

                    </div>

                    <div class="content">

                        <div class="name">
                            ${card.name || 'Unknown'}
                        </div>

                        <div class="meta">

                            <div class="badge">
                                ${card.classification?.type || 'Unknown'}
                            </div>

                            <div class="badge">
                                ${card.set?.label || 'Unknown Set'}
                            </div>

                            <div class="badge">
                                ${(card.classification?.domain || []).join(', ')}
                            </div>

                        </div>

                        <div class="stats">

                            <div class="stat">
                                ⚡ ${energy}
                            </div>

                            <div class="stat">
                                ⚔️ ${might}
                            </div>

                            <div class="stat">
                                🔥 ${power}
                            </div>

                        </div>

                        <div class="text">
                            ${card.text?.plain || 'No text available'}
                        </div>

                        ${
                            card.text?.flavour
                            ? `<div class="flavor">
                                "${card.text.flavour}"
                               </div>`
                            : ''
                        }

                    </div>

                </div>
            `;

            grid.innerHTML += html;
        });

    }catch(error){

        console.error(error);

        document.getElementById('status').innerText =
            'Failed to load cards';
    }
}

loadCards();

</script>

</body>
</html>