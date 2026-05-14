<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RiftCodex Search</title>

<style>
            body { font-family: Arial, sans-serif; margin: 0; line-height: 1.6; }
        header { background: #333; color: #fff; padding: 1rem; text-align: center; }
        nav { display: flex; justify-content: center; background: #444; padding: 0.5rem; position: relative; }
        nav a { color: white; margin: 0 15px; text-decoration: none; }
        .hero { padding: 50px; text-align: center; background: #f4f4f4; }
        .container { padding: 20px; max-width: 800px; margin: auto; }
        footer { background: #333; color: #fff; text-align: center; padding: 10px; position: fixed; bottom: 0; width: 100%; }
        h1 { margin: 0; }
        h1 { margin: 0; }
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

<h1>Riftbound Card Search</h1>

<nav>
    <a href="home2.php">Home</a>
    <a href="logout.php">Logout</a>
    <a href="search.php">Search</a>
    <a href="#">TBD</a>
</nav>

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

async function loadCards(query = "") {

    try {

        document.getElementById("status").innerText = "Loading...";

        const url =
            "https://api.riftcodex.com/cards/search?query=" +
            encodeURIComponent(query) +
            "&dir=1&page=1&size=50";

        const res = await fetch(url);

        if (!res.ok) {
            throw new Error("HTTP " + res.status);
        }

        const data = await res.json();

        const items = data.items || [];

        document.getElementById("status").innerText =
            `Found ${items.length} cards`;

        const grid = document.getElementById("grid");
        grid.innerHTML = "";

        items.forEach(card => {

            const html = `
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

            grid.innerHTML += html;
        });

    } catch (err) {

        console.error(err);

        document.getElementById("status").innerText =
            "Failed to load cards";
    }
}

// initial load
loadCards("");

</script>

</body>
</html>