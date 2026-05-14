<?php

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

function fetchCards() {

    $url = "https://api.riftcodex.com/cards?size=200&page=1";

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'User-Agent: Mozilla/5.0',
            'timeout' => 15
        ]
    ]);

    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        return [];
    }

    $data = json_decode($response, true);

    return $data['items'] ?? [];
}

$allCards = fetchCards();

$cards = $allCards;

// SEARCH FILTER
if (!empty($query)) {

    $q = strtolower($query);

    $cards = array_filter($allCards, function($card) use ($q) {

        return (
            stripos($card['name'] ?? '', $q) !== false ||
            stripos($card['classification']['type'] ?? '', $q) !== false ||
            stripos($card['classification']['rarity'] ?? '', $q) !== false ||
            stripos($card['set']['label'] ?? '', $q) !== false ||
            stripos($card['text']['plain'] ?? '', $q) !== false ||
            stripos(implode(' ', $card['tags'] ?? []), $q) !== false
        );

    });

    $cards = array_slice($cards, 0, 50);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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

form {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

input {
    flex: 1;
    padding: 12px;
    border-radius: 10px;
    border: none;
    font-size: 16px;
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
    <input type="text" name="q"
           placeholder="Search cards (Ashe, Unit, Rare, damage...)"
           value="<?php echo htmlspecialchars($query); ?>">
    <button type="submit">Search</button>
</form>

<div class="status">
    <?php if (!empty($query)): ?>
        Found <?php echo count($cards); ?> results for "<?php echo htmlspecialchars($query); ?>"
    <?php else: ?>
        Showing all cards
    <?php endif; ?>
</div>

<div class="grid">

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

</div>

</body>
</html>