<?php

$apiUrl = "https://api.riftcodex.com/api/cards?limit=20&page=1&set_id=ogn";

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 30,

    // IMPORTANT
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

$total = $data['total'] ?? 0;
$items = $data['items'] ?? [];

?>

<!DOCTYPE html>
<html>
<head>
    <title>RiftCodex Cards</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }

        th {
            background: #222;
            color: white;
        }
    </style>
</head>
<body>

<h1>Origins Cards</h1>

<p>Total: <?php echo $total; ?></p>

<table>
    <tr>
        <th>Name</th>
        <th>Set</th>
    </tr>

    <?php foreach ($items as $card): ?>
        <tr>
            <td><?php echo htmlspecialchars($card['name']); ?></td>
            <td><?php echo htmlspecialchars($card['set']); ?></td>
        </tr>
    <?php endforeach; ?>

</table>

</body>
</html>