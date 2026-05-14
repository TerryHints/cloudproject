<?php
// Handle search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$cards = [];

if (!empty($query)) {
    // Make API call server-side to bypass Cloudflare
    $apiUrl = "https://api.riftcodex.com/api/cards?limit=100&page=1";

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'timeout' => 10
        ]
    ]);

    $response = file_get_contents($apiUrl, false, $context);

    if ($response !== false) {
        $data = json_decode($response, true);
        if ($data && isset($data['items'])) {
            $allCards = $data['items'];

            // Filter cards based on search query
            $lowerQuery = strtolower($query);
            $cards = array_filter($allCards, function($card) use ($lowerQuery) {
                return (
                    (isset($card['name']) && stripos($card['name'], $lowerQuery) !== false) ||
                    (isset($card['classification']['type']) && stripos($card['classification']['type'], $lowerQuery) !== false) ||
                    (isset($card['classification']['rarity']) && stripos($card['classification']['rarity'], $lowerQuery) !== false) ||
                    (isset($card['set']['label']) && stripos($card['set']['label'], $lowerQuery) !== false) ||
                    (isset($card['text']['plain']) && stripos($card['text']['plain'], $lowerQuery) !== false)
                );
            });

            // Limit to 50 results
            $cards = array_slice($cards, 0, 50);
        }
    }
}
?>

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

h1 {
    margin-bottom: 10px;
}

.search-box {
    margin-bottom: 20px;
}

form {
    display: flex;
    gap: 10px;
}

input[type="text"] {
    flex: 1;
    padding: 12px;
    border-radius: 10px;
    border: none;
    outline: none;
    font-size: 16px;
}

button {
    padding: 12px 20px;
    border-radius: 10px;
    border: none;
    background: #3b82f6;
    color: white;
    font-size: 16px;
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

nav {
    margin-bottom: 20px;
}

nav a {
    color: #3b82f6;
    text-decoration: none;
    margin-right: 15px;
}

nav a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>
    <nav>
    <a href="home2.php">Home</a>
    <a href="logout.php">Logout</a>
    <a href="#">TBD</a>
    <a href="#">TBD</a>
</nav>

<h1>RiftCodex Card Search</h1>

<form method="GET" action="">
    <input type="text" name="q" placeholder="Search cards (e.g. Ashe, Unit, Rare...)" value="<?php echo htmlspecialchars($query); ?>">
    <button type="submit">Search</button>
</form>

<div id="status">
    <?php if (!empty($query)): ?>
        Found <?php echo count($cards); ?> cards matching "<?php echo htmlspecialchars($query); ?>"
    <?php else: ?>
        Enter a search term to find cards
    <?php endif; ?>
</div>

<div class="grid">
    <?php foreach ($cards as $card): ?>
        <div class="card">
            <img src="<?php echo htmlspecialchars($card['media']['image_url'] ?? ''); ?>" onerror="this.style.display='none'">
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

<?php if (empty($query)): ?>
    <div style="text-align: center; margin-top: 40px; color: #94a3b8;">
        <p>Search for Riftbound cards by name, type, rarity, or description.</p>
        <p>Examples: "Ashe", "Champion", "Rare", "damage"</p>
    </div>
<?php endif; ?>
</body>
</html>