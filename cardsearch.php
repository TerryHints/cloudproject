<?php

// API URL
$apiUrl = "https://api.riftcodex.com/api/cards?limit=20&page=1&set_id=ogn";

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

// Execute request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    die("cURL Error: " . curl_error($ch));
}

// Get HTTP status code
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

// Check if API returned success
if ($httpCode !== 200) {
    die("Failed to load card data. HTTP Code: " . $httpCode);
}

// Decode JSON
$data = json_decode($response, true);

if (!$data) {
    die("Invalid JSON response.");
}

// Extract data
$total = $data['total'] ?? 0;
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

        h1 {
            color: #333;
        }

        .card-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .card-table th,
        .card-table td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        .card-table th {
            background: #222;
            color: white;
        }

        .card-table tr:nth-child(even) {
            background: #f9f9f9;
        }
    </style>
</head>
<body>

<h1>Origins Set Cards</h1>

<p><strong>Total Cards:</strong> <?php echo htmlspecialchars($total); ?></p>

<table class="card-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Set</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($items)): ?>
            <?php foreach ($items as $card): ?>
                <tr>
                    <td><?php echo htmlspecialchars($card['name'] ?? 'Unknown'); ?></td>
                    <td><?php echo htmlspecialchars($card['set'] ?? 'Unknown'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">No cards found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>