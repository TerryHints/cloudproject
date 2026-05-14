<?php

$query = $_GET['q'] ?? '';

function fetchApi($url) {

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

// Decide endpoint
if (trim($query) !== '') {

    // SINGLE CARD / NAME SEARCH
    $url = "https://api.riftcodex.com/cards/name?query=" .
           urlencode($query) .
           "&dir=1&page=1&size=50";

} else {

    // BROWSE MODE
    $url = "https://api.riftcodex.com/cards/search?query=&dir=1&page=1&size=50";
}

$data = fetchApi($url);

$cards = $data['items'] ?? [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>RiftCodex Search</title>

<style>

body {
    margin:0;
    padding:20px;
    font-family: Arial;
    background:#0f172a;
    color:white;
}

input {
    width:100%;
    padding:12px;
    font-size:16px;
    border-radius:10px;
    border:none;
    margin-bottom:15px;
}

.grid {
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(240px,1fr));
    gap:16px;
}

.card {
    background:#111827;
    border-radius:12px;
    overflow:hidden;
}

.card img {
    width:100%;
    display:block;
}

.content {
    padding:10px;
}

.name {
    font-weight:bold;
    margin-bottom:6px;
}

.badge {
    display:inline-block;
    background:#1f2937;
    padding:4px 8px;
    border-radius:999px;
    font-size:12px;
    margin:2px;
}

.searchbar {
    margin-bottom:10px;
}

button {
    padding:10px 14px;
    border:none;
    border-radius:8px;
    background:#2563eb;
    color:white;
    cursor:pointer;
}

button:hover {
    background:#1d4ed8;
}

small {
    color:#94a3b8;
}

</style>
</head>
<body>

<h1>RiftCodex Card Search</h1>
<nav>
    <a href="cardsearch.php">LOAD 50</a>
    <a href="logout.php">Logout</a>
    <a href="search.php">Search</a>
    <a href="#">TBD</a>
</nav>
<form method="GET" class="searchbar">
    <input type="text" name="q" placeholder="Search cards..."
           value="<?php echo htmlspecialchars($query); ?>">
    <button type="submit">Search</button>
</form>

<small>
<?php echo $query ? "Searching for: " . htmlspecialchars($query) : "Showing all cards"; ?>
</small>

<br><br>

<div class="grid">

<?php if (!empty($cards)): ?>

    <?php foreach ($cards as $card): ?>

        <div class="card">

            <img src="<?php echo $card['media']['image_url'] ?? ''; ?>">

            <div class="content">

                <div class="name">
                    <?php echo htmlspecialchars($card['name'] ?? 'Unknown'); ?>
                </div>

                <div>
                    <span class="badge">
                        <?php echo $card['classification']['type'] ?? 'Type'; ?>
                    </span>

                    <span class="badge">
                        <?php echo $card['classification']['rarity'] ?? 'Rarity'; ?>
                    </span>

                    <span class="badge">
                        <?php echo $card['set']['label'] ?? 'Set'; ?>
                    </span>
                </div>

            </div>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <p>No cards found.</p>

<?php endif; ?>

</div>

</body>
</html>