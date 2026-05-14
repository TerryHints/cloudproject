<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RiftCodex Search</title>

<style>
body {
    margin: 0;
    padding: 20px;
    font-family: Arial;
    background: #0f172a;
    color: white;
}

h1 {
    margin-bottom: 10px;
}

.search-box {
    margin-bottom: 20px;
}

input {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: none;
    outline: none;
    font-size: 16px;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 18px;
}

.card {
    background: #111827;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.08);
}

.card img {
    width: 100%;
    display: block;
}

.content {
    padding: 12px;
}

.name {
    font-weight: bold;
    margin-bottom: 8px;
}

.badge {
    font-size: 12px;
    background: #1f2937;
    padding: 4px 8px;
    border-radius: 999px;
    display: inline-block;
    margin-right: 6px;
    margin-bottom: 6px;
}

.text {
    font-size: 13px;
    color: #cbd5e1;
    margin-top: 10px;
    line-height: 1.4;
}

#status {
    margin-bottom: 15px;
    color: #94a3b8;
}
</style>
</head>
<body>

<h1>RiftCodex Card Search</h1>

<div class="search-box">
    <input type="text" id="search" placeholder="Search cards (e.g. Ashe, Unit, Rare...)">
</div>

<div id="status">Loading...</div>

<div class="grid" id="grid"></div>

<script>
let timeout = null;
const searchInput = document.getElementById("search");

searchInput.addEventListener("input", function () {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        loadCards(searchInput.value);
    }, 300);
});

// Sample card data for demonstration
let allCards = [
    {
        name: "Ashe",
        classification: { type: "Champion", rarity: "Common" },
        set: { label: "OGN" },
        media: { image_url: "https://example.com/ashe.jpg" },
        text: { plain: "Frost Archer - Deal 1 damage to any unit." }
    },
    {
        name: "Miss Fortune",
        classification: { type: "Champion", rarity: "Rare" },
        set: { label: "OGN" },
        media: { image_url: "https://example.com/mf.jpg" },
        text: { plain: "Bounty Hunter - Attack a unit or player." }
    },
    {
        name: "Acceptable Losses",
        classification: { type: "Unit", rarity: "Common" },
        set: { label: "OGN" },
        media: { image_url: "https://example.com/losses.jpg" },
        text: { plain: "Deal 1 damage to a unit." }
    },
    {
        name: "Flash Freeze",
        classification: { type: "Spell", rarity: "Epic" },
        set: { label: "OGN" },
        media: { image_url: "https://example.com/freeze.jpg" },
        text: { plain: "Stun a unit for 1 turn." }
    }
];

function filterCards(query = "") {
    if (!query.trim()) {
        return allCards;
    }
    const lowerQuery = query.toLowerCase();
    return allCards.filter(card => {
        return (
            (card.name && card.name.toLowerCase().includes(lowerQuery)) ||
            (card.classification?.type && card.classification.type.toLowerCase().includes(lowerQuery)) ||
            (card.classification?.rarity && card.classification.rarity.toLowerCase().includes(lowerQuery)) ||
            (card.set?.label && card.set.label.toLowerCase().includes(lowerQuery)) ||
            (card.text?.plain && card.text.plain.toLowerCase().includes(lowerQuery))
        );
    });
}

async function loadCards(query = "") {
    console.log("loadCards called with query:", query);
    try {
        const filteredCards = filterCards(query);
        document.getElementById("status").innerText =
            query ? `Found ${filteredCards.length} cards matching "${query}"` : `Showing ${filteredCards.length} sample cards`;

        const grid = document.getElementById("grid");
        grid.innerHTML = "";

        filteredCards.forEach(card => {
            const html = `
                <div class="card">
                    <img src="${card.media?.image_url || ''}" onerror="this.style.display='none'">
                    <div class="content">
                        <div class="name">
                            ${card.name || "Unknown"}
                        </div>
                        <div>
                            <span class="badge">
                                ${card.classification?.type || "Type"}
                            </span>
                            <span class="badge">
                                ${card.classification?.rarity || "Rarity"}
                            </span>
                            <span class="badge">
                                ${card.set?.label || "Set"}
                            </span>
                        </div>
                        <div class="text">
                            ${card.text?.plain || ""}
                        </div>
                    </div>
                </div>
            `;
            grid.innerHTML += html;
        });

    } catch (err) {
        console.error(err);
        document.getElementById("status").innerText = "Failed to load cards";
    }
}

// initial load
loadCards("");
</script>

</body>
</html>