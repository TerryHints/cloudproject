<?php

$apiUrl = "https://api.riftcodex.com/cards";

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => [
        "Accept: application/json",
        "User-Agent: Mozilla/5.0"
    ]
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    die("cURL Error: " . curl_error($ch));
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($httpCode !== 200) {
    die("Failed to load card data. HTTP Code: " . $httpCode);
}

$data = json_decode($response, true);

if (!$data) {
    die("Invalid JSON response.");
}

$items = $data['items'] ?? [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RiftCodex Cards</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            vertical-align: top;
        }

        th {
            background: #222;
            color: white;
        }

        img {
            width: 120px;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<h1>RiftCodex Cards</h1>

<p>Total Loaded: <?php echo count($items); ?></p>

<table>
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Type</th>
            <th>Rarity</th>
            <th>Set</th>
        </tr>
    </thead>

    <tbody>

    <?php foreach ($items as $card): ?>

        <tr>

            <td>
                <?php if (!empty($card['set']['media']['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($card['set']['media']['image_url']); ?>">
                <?php endif; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($card['name'] ?? 'Unknown'); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($card['classification']['type'] ?? 'Unknown'); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($card['classification']['rarity'] ?? 'Unknown'); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($card['set']['label'] ?? 'Unknown'); ?>
            </td>

        </tr>

    <?php endforeach; ?>

    </tbody>
</table>

</body>
</html>