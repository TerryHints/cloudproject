<?php
require 'vendor/autoload.php';
require 'db_config.php';
use Aws\DynamoDb\DynamoDbClient;

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    echo "Username and password are required.";
    exit();
}

$username = $_POST['username'];

try {
    $result = $client->getItem([
        'TableName' => 'Users',
        'Key' => ['username' => ['S' => $username]]
    ]);

    if (isset($result['Item'])) {
        $storedHash = $result['Item']['password']['S'];
        if (password_verify($_POST['password'], $storedHash)) {
            session_start();
            $_SESSION['username'] = $username;
            header("Location: home2.html");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage();
}
?>