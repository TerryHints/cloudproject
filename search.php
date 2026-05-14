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

input {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border-radius: 10px;
    border: none;
    margin-bottom: 15px;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 16px;
}

.card {
    background: #111827;
    border-radius: 12px;
    overflow: hidden;
}

.card img {
    width: 100%;
}

.content {
    padding: 10px;
}

.name {
    font-weight: bold;
    margin-bottom: 6px;
}

.badge {
    display: inline-block;
    background: #1f2937;
    padding: 4px 8px;
    border-radius: 999px;
    font-size: 12px;
    margin: 2px;
}

#status {
    margin-bottom: 10px;
    color: #94a3b8;
}
</style>
</head>
<body>

<h1>Card Search</h1>

<input type="text" id="search" placeholder="Search cards...">

<div id="status">Loading...</div>

<div class="grid" id="grid"></div>

<script>

let cards = [];

const grid = document.getElementById("grid");
const status = document.getElementById("status");
const search = document.getElementById("search");

// LOAD DATA
async function loadCards() {

    try {

        const res = await fetch(
            "https://api.riftcodex.com/cards/search?query=&dir=1&page=1&size=200"
        );

        const data = await res.json();

        cards = data.items || [];

        status.innerText = "Loaded " + cards.length + " cards";

        render(cards);

    } catch (err) {

        console.error(err);

        status.innerText = "Failed to load cards";
    }
}

// SIMPLE SEARCH (NO COMPLEX SCORING)
function filterCards(query) {

    query = query.toLowerCase().trim();

    if (!query) return cards;

    return cards.filter(c => {

        return (
            (c.name || "").toLowerCase().includes(query) ||
            (c.classification?.type || "").toLowerCase().includes(query) ||
            (c.classification?.rarity || "").toLowerCase().includes(query) ||
            (c.set?.label || "").toLowerCase().includes(query) ||
            (c.text?.plain || "").toLowerCase().includes(query) ||
            (c.tags || []).join(" ").toLowerCase().includes(query)
        );

    });
}

// RENDER
function render(list) {

    grid.innerHTML = "";

    status.innerText = "Found " + list.length + " cards";

    list.forEach(card => {

        grid.innerHTML += `
            <div class="card">

                <img src="${card.media?.image_url || ''}">

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

                </div>

            </div>
        `;
    });
}

// SEARCH INPUT
search.addEventListener("input", () => {

    const filtered = filterCards(search.value);

    render(filtered);
});

loadCards();

</script>

</body>
</html>