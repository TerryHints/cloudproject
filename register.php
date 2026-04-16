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
    $client->putItem([
        'TableName' => 'Users',
        'Item' => [
            'username' => ['S' => $username],
            'password' => ['S' => $password],
            'email'    => ['S' => $email],
        ]
    ]);
    
    // If we reach this line, the database call succeeded!
    echo "Registration successful! Redirecting...";
    header("Location: login.html");
    exit();

} catch (Exception $e) {
    // If anything goes wrong, the code jumps here
    echo "Database Error: " . $e->getMessage();
}
?>
