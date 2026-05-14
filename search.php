<script>

let timeout = null;

const searchInput = document.getElementById("search");

searchInput.addEventListener("input", function () {

    clearTimeout(timeout);

    timeout = setTimeout(() => {
        const q = searchInput.value.trim();

        // 🔥 FIX 1: prevent useless single-letter searches
        if (q.length < 2) {
            document.getElementById("grid").innerHTML = "";
            document.getElementById("status").innerText = "Type at least 2 characters...";
            return;
        }

        loadCards(q);

    }, 300);

});

async function loadCards(query = "") {

    try {

        document.getElementById("status").innerText = "Loading...";

        let url;

        // 🔥 FIX 2: try EXACT match first
        url = "https://api.riftcodex.com/cards/name?exact=" + encodeURIComponent(query);

        let res = await fetch(url);

        if (!res.ok) {
            throw new Error("HTTP " + res.status);
        }

        let data = await res.json();

        let items = data.items || [];

        // 🔥 FIX 3: fallback to fuzzy search if exact returns nothing
        if (items.length === 0) {

            url = "https://api.riftcodex.com/cards/search?query=" + encodeURIComponent(query) +
                  "&dir=1&page=1&size=50";

            res = await fetch(url);

            if (!res.ok) {
                throw new Error("HTTP " + res.status);
            }

            data = await res.json();

            items = data.items || [];
        }

        document.getElementById("status").innerText =
            `Found ${items.length} cards`;

        const grid = document.getElementById("grid");
        grid.innerHTML = "";

        // 🔥 FIX 4: better sorting (optional but improves UX)
        items.sort((a, b) => {
            const q = query.toLowerCase();

            const aMatch = (a.name || "").toLowerCase().startsWith(q);
            const bMatch = (b.name || "").toLowerCase().startsWith(q);

            return bMatch - aMatch;
        });

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
loadCards("a"); // optional: avoids empty state

</script>