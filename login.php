<?php
require 'vendor/autoload.php';
require 'db_config.php';
use Aws\DynamoDb\DynamoDbClient;

$username = $_POST['username'];

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
}
?>