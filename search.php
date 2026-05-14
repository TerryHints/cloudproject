<?php

$query = $_GET['q'] ?? '';

function fetchCards($query) {

    $url = "https://api.riftcodex.com/cards/search?query=" .
           urlencode($query) .
           "&dir=1&page=1&size=50";

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

    $data = json_decode($res, true);

    return $data['items'] ?? [];
}

$cards = fetchCards($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RiftCodex PHP Search</title>

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
    border-radius: 10px;
    border: none;
    font-size: 16px;
    margin-bottom: 15px;
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
}

.card img {
    width: 100%;
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
</style>
</head>

<body>

<h1>RiftCodex Card Search</h1>

<form method="GET">
    <input type="text"
           name="q"
           placeholder="Search cards..."
           value="<?php echo htmlspecialchars($query); ?>">
</form>

<p>
Showing <?php echo count($cards); ?> cards
</p>

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