<?php
require 'vendor/autoload.php';
require 'db_config.php';
use Aws\DynamoDb\DynamoDbClient;

//$client = new DynamoDbClient(['region' => 'us-east-1', 'version' => 'latest']);

//Form handling:
$username = filter_var($_POST['username'], FILTER_SANITIZE_SPECIAL_CHARS);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

$client->putItem([
    'TableName' => 'Users',
    'Item' => [
        'username' => ['S' => $username],
        'password' => ['S' => $password],
        'email'    => ['S' => $email],
    ]
    
]);
?>
