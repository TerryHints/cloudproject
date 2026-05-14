<?php

$query = $_GET['q'] ?? '';

function fetch($url) {

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "User-Agent: Mozilla/5.0"
        ]
    ]);

    $res = curl_exec($ch);
    curl_close($ch);

    return json_decode($res, true);
}

if (trim($query) !== '') {

    // 🔥 PROPER SEARCH MODE
    $url = "https://api.riftcodex.com/cards/name?fuzzy=" .
           urlencode($query);

} else {

    // 📚 BROWSE MODE
    $url = "https://api.riftcodex.com/cards/search?query=&dir=1&page=1&size=200";
}

$data = fetch($url);

$cards = $data['items'] ?? [];

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Card Search</title>

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
}

.badge {
    display: inline-block;
    background: #1f2937;
    padding: 4px 8px;
    border-radius: 999px;
    font-size: 12px;
    margin: 2px;
}
</style>
</head>

<body>

<h1>Card Search</h1>

<form method="GET">
    <input type="text"
           name="q"
           placeholder="Search cards..."
           value="<?php echo htmlspecialchars($query); ?>">
</form>

<p>Found <?php echo count($cards); ?> cards</p>

<div class="grid">

<?php foreach ($cards as $card): ?>

    <div class="card">

        <img src="<?php echo $card['media']['image_url'] ?? ''; ?>">

        <div class="content">

            <div class="name">
                <?php echo htmlspecialchars($card['name'] ?? 'Unknown'); ?>
            </div>

            <div>
                <span class="badge">
                    <?php echo $card['classification']['type'] ?? ''; ?>
                </span>

                <span class="badge">
                    <?php echo $card['classification']['rarity'] ?? ''; ?>
                </span>

                <span class="badge">
                    <?php echo $card['set']['label'] ?? ''; ?>
                </span>
            </div>

        </div>

    </div>

<?php endforeach; ?>

</div>

</body>
</html>