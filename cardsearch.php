<?php
// API endpoint
$apiUrl = "https://api.riftcodex.com/api/cards?limit=20&page=1&set_id=ogn";

// Fetch API response
$response = file_get_contents($apiUrl);

// Decode JSON response
$data = json_decode($response, true);

// Check for errors
if (!$data || !isset($data['items'])) {
    die("Failed to load card data.");
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
            font-family: Arial, sans-serif;
            background: #111827;
            color: #f3f4f6;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #60a5fa;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #1f2937;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #374151;
            text-align: left;
        }

        th {
            background: #2563eb;
            color: white;
        }

        tr:hover {
            background: #374151;
        }

        .total {
            margin-bottom: 15px;
            font-size: 18px;
        }
    </style>
</head>
<body>

    <h1>Origins Set Cards</h1>

    <div class="total">
        Total Cards: <?php echo htmlspecialchars($data['total']); ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Card Name</th>
                <th>Set</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['items'] as $card): ?>
                <tr>
                    <td><?php echo htmlspecialchars($card['name']); ?></td>
                    <td><?php echo htmlspecialchars($card['set']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>