<?php
session_start();

require 'vendor/autoload.php';
require 'db_config.php';
use Aws\DynamoDb\DynamoDbClient;

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    echo "<!DOCTYPE html><html><head><title>Login Error</title></head><body><p>Username and password are required.</p><a href='login.html'>Back to Login</a></body></html>";
    exit();
}

$username = filter_var($_POST['username'], FILTER_SANITIZE_SPECIAL_CHARS);

try {
    $result = $client->scan([
        'TableName' => 'Users',
        'FilterExpression' => 'username = :username',
        'ExpressionAttributeValues' => [
            ':username' => ['S' => $username]
        ]
    ]);

    if (count($result['Items']) > 0) {
        $item = $result['Items'][0];
        $storedHash = $item['password']['S'];
        if (password_verify($_POST['password'], $storedHash)) {
            $_SESSION['username'] = $username;
            header("Location: home2.php");
            exit();
        } else {
            echo "<!DOCTYPE html><html><head><title>Login Error</title></head><body><p>Invalid password.</p><a href='login.html'>Back to Login</a></body></html>";
        }
    } else {
        echo "<!DOCTYPE html><html><head><title>Login Error</title></head><body><p>User not found.</p><a href='login.html'>Back to Login</a></body></html>";
    }
} catch (Exception $e) {
    echo "<!DOCTYPE html><html><head><title>Login Error</title></head><body><p>Database Error: " . htmlspecialchars($e->getMessage()) . "</p><a href='login.html'>Back to Login</a></body></html>";
}
?>