<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale="1.0">
<title>RiftCodex Card Search</title>

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

input {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: none;
    font-size: 16px;
    margin-bottom: 15px;
}

#status {
    margin-bottom: 15px;
    color: #94a3b8;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 16px;
}

.card {
    background: #111827;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.08);
}

.card img {
    width: 100%;
}

.content {
    padding: 12px;
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

.text {
    font-size: 13px;
    color: #cbd5e1;
    margin-top: 8px;
}
</style>
</head>
<body>
<body>
    <nav>
    <a href="home2.php">Home</a>
    <a href="logout.php">Logout</a>
    <a href="#">TBD</a>
    <a href="#">TBD</a>
</nav>
<h1>Card Search</h1>

<input type="text" id="search" placeholder="Search cards...">

<div id="status">Loading...</div>

<div class="grid" id="grid"></div>

<script>

let cache = [];
let timeout = null;

const searchInput = document.getElementById("search");

searchInput.addEventListener("input", () => {

    clearTimeout(timeout);

    timeout = setTimeout(() => {
        render(searchInput.value);
    }, 250);

});

async function loadCards() {

    const res = await fetch(
        "https://api.riftcodex.com/cards/search?query=&dir=1&page=1&size=200"
    );

    const data = await res.json();

    cache = data.items || [];

    document.getElementById("status").innerText =
        "Loaded " + cache.length + " cards";

    render("");
}

function scoreCard(card, q) {

    let score = 0;

    const name = (card.name || "").toLowerCase();
    const type = (card.classification?.type || "").toLowerCase();
    const rarity = (card.classification?.rarity || "").toLowerCase();
    const set = (card.set?.label || "").toLowerCase();
    const text = (card.text?.plain || "").toLowerCase();
    const tags = (card.tags || []).join(" ").toLowerCase();

    // exact name match = strongest
    if (name === q) score += 100;

    // name contains query
    if (name.includes(q)) score += 50;

    // tags match
    if (tags.includes(q)) score += 30;

    // type / rarity / set
    if (type.includes(q)) score += 20;
    if (rarity.includes(q)) score += 15;
    if (set.includes(q)) score += 15;

    // text match (weak)
    if (text.includes(q)) score += 5;

    return score;
}

function render(query) {

    const q = query.toLowerCase().trim();

    let results = cache;

    if (q.length > 0) {

        results = cache
            .map(card => ({
                card,
                score: scoreCard(card, q)
            }))
            .filter(x => x.score > 0)
            .sort((a, b) => b.score - a.score)
            .map(x => x.card);
    }

    document.getElementById("status").innerText =
        "Found " + results.length + " cards";

    const grid = document.getElementById("grid");
    grid.innerHTML = "";

    results.forEach(card => {

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

                    <div class="text">
                        ${card.text?.plain || ""}
                    </div>

                </div>

            </div>
        `;
    });
}

loadCards();

</script>

</body>
</html>