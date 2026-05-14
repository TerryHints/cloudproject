<?php
// Fetch cards once
$url = "https://api.riftcodex.com/cards/search?query=&dir=1&page=1&size=200";

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER => [
        "Accept: application/json",
        "User-Agent: Mozilla/5.0"
    ]
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

$cards = $data['items'] ?? [];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>RiftCodex Live Search</title>

<style>

body {
    font-family: Arial;
    background: #0f172a;
    color: white;
    padding: 20px;
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

.hidden {
    display: none;
}

</style>
</head>
<body>

<h1>Card Search</h1>

<input type="text" id="search" placeholder="Search cards...">

<p id="count">Showing <?php echo count($cards); ?> cards</p>

<div class="grid">

<?php foreach ($cards as $card): ?>

    <div class="card searchable">

        <img src="<?php echo $card['media']['image_url'] ?? ''; ?>">

        <div class="content">

            <div class="name">
                <?php echo htmlspecialchars($card['name'] ?? 'Unknown'); ?>
            </div>

            <div class="meta">

                <span class="badge type">
                    <?php echo $card['classification']['type'] ?? ''; ?>
                </span>

                <span class="badge rarity">
                    <?php echo $card['classification']['rarity'] ?? ''; ?>
                </span>

                <span class="badge set">
                    <?php echo $card['set']['label'] ?? ''; ?>
                </span>

            </div>

        </div>

    </div>

<?php endforeach; ?>

</div>

<script>

const input = document.getElementById("search");
const cards = document.querySelectorAll(".searchable");
const count = document.getElementById("count");

input.addEventListener("input", function () {

    const q = this.value.toLowerCase().trim();

    let visible = 0;

    cards.forEach(card => {

        const text = card.innerText.toLowerCase();

        if (text.includes(q)) {
            card.classList.remove("hidden");
            visible++;
        } else {
            card.classList.add("hidden");
        }

    });

    count.innerText = "Showing " + visible + " cards";

});

</script>

</body>
</html>