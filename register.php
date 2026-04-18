<?php
require 'vendor/autoload.php';
require 'db_config.php';
use Aws\DynamoDb\DynamoDbClient;

//$client = new DynamoDbClient(['region' => 'us-east-1', 'version' => 'latest']);

//Form handling:
$username = filter_var($_POST['username'], FILTER_SANITIZE_SPECIAL_CHARS);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

try {
    error_log("[register.php] putItem starting for username={$username}, email={$email}");

    $result = $client->putItem([
        'TableName' => 'Users',
        'Item' => [
            "UserID" => ["S" => "unique-user-id-123"],
            "CreationDate" => ["S" => "2026-04-16T23:55:00Z"],
            'username' => ['S' => $username],
            'password' => ['S' => $password],
            'email'    => ['S' => $email],
        ]
    ]);

    error_log("[register.php] putItem succeeded: " . json_encode($result));
    
    
    echo "Registration successful! Redirecting...";
    header("Location: login.html");
    exit();

} catch (Exception $e) {
    error_log("[register.php] putItem failed: " . $e->getMessage());
    
    echo "Database Error: " . $e->getMessage();
}
?>
