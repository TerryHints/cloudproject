<?php

// ---------------------------
// API LAYER (FIXED)
// ---------------------------

function apiRequest($url) {

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "User-Agent: Mozilla/5.0"
        ]
    ]);

    $res = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($res === false || $http !== 200) {
        return [];
    }

    return json_decode($res, true);
}


// ---------------------------
// API FUNCTIONS
// ---------------------------

// Browse cards
function getCards($page = 1, $size = 50) {

    $url = "https://api.riftcodex.com/cards?page=$page&size=$size&dir=1";

    $data = apiRequest($url);

    return $data['items'] ?? [];
}

// Search cards (fuzzy)
function searchCards($query, $page = 1, $size = 50) {

    $url = "https://api.riftcodex.com/cards/name?fuzzy=" .
           urlencode($query) .
           "&page=$page&size=$size";

    $data = apiRequest($url);

    return $data['items'] ?? $data['cards'] ?? [];
}


// ---------------------------
// MAIN LOGIC
// ---------------------------

$query = $_GET['q'] ?? '';

if (!empty($query)) {
    $cards = searchCards($query, 1, 50);
} else {
    $cards = getCards(1, 50);
}

?>

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
    font-family: Arial;
    background: #0f172a;
    color: white;
}

h1 {
    margin-bottom: 10px;
}

form {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

input {
    flex: 1;
    padding: 12px;
    font-size: 16px;
    border-radius: 10px;
    border: none;
}

button {
    padding: 12px 18px;
    border: none;
    border-radius: 10px;
    background: #3b82f6;
    color: white;
    cursor: pointer;
}

button:hover {
    background: #2563eb;
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
    margin: 2px;
}

.text {
    font-size: 13px;
    color: #cbd5e1;
    margin-top: 10px;
}

.status {
    margin-bottom: 15px;
    color: #94a3b8;
}

</style>
</head>

<body>

<h1>RiftCodex Card Search</h1>

<form method="GET">
    <input type="text"
           name="q"
           placeholder="Search cards (Ashe, Unit, Rare...)"
           value="<?php echo htmlspecialchars($query); ?>">
    <button type="submit">Search</button>
</form>

<div class="status">
    Showing <?php echo count($cards); ?> cards
    <?php if (!empty($query)): ?>
        for "<?php echo htmlspecialchars($query); ?>"
    <?php endif; ?>
</div>

<div class="grid">

<?php if (!empty($cards)): ?>

    <?php foreach ($cards as $card): ?>

        <div class="card">

            <img src="<?php echo htmlspecialchars($card['media']['image_url'] ?? ''); ?>">

            <div class="content">

                <div class="name">
                    <?php echo htmlspecialchars($card['name'] ?? 'Unknown'); ?>
                </div>

                <div>
                    <span class="badge">
                        <?php echo htmlspecialchars($card['classification']['type'] ?? 'Type'); ?>
                    </span>

                    <span class="badge">
                        <?php echo htmlspecialchars($card['classification']['rarity'] ?? 'Rarity'); ?>
                    </span>

                    <span class="badge">
                        <?php echo htmlspecialchars($card['set']['label'] ?? 'Set'); ?>
                    </span>
                </div>

                <div class="text">
                    <?php echo htmlspecialchars($card['text']['plain'] ?? ''); ?>
                </div>

            </div>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <p style="color:#94a3b8;">No cards found.</p>

<?php endif; ?>

</div>

</body>
</html>