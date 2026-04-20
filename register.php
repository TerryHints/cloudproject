<?php
require 'vendor/autoload.php';
require 'db_config.php';
use Aws\DynamoDb\DynamoDbClient;

//$client = new DynamoDbClient(['region' => 'us-east-1', 'version' => 'latest']);

//Form handling:
$username = trim(filter_var($_POST['username'], FILTER_SANITIZE_SPECIAL_CHARS));
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
$email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<!DOCTYPE html><html><head><title>Registration Error</title></head><body><p>Invalid email format.</p><a href='register.html'>Back to Register</a></body></html>";
    exit();
}



//Current workaround for unique ID's... I know php's uniqid isn't industry standard but it'll do
$userID = uniqid('user_', true); 
$creationDate = date('c'); 

try {
    $usernameCheck = $client->scan([
        'TableName' => 'Users',
        'FilterExpression' => 'username = :username',
        'ExpressionAttributeValues' => [':username' => ['S' => $username]]
    ]);
    
    if (count($usernameCheck['Items']) > 0) {
        echo "<!DOCTYPE html><html><head><title>Registration Error</title></head><body><p>Username already exists.</p><a href='register.html'>Back to Register</a></body></html>";
        exit();
    }
    
    // Check if email already exists
    $emailCheck = $client->scan([
        'TableName' => 'Users',
        'FilterExpression' => 'email = :email',
        'ExpressionAttributeValues' => [':email' => ['S' => $email]]
    ]);
    
    if (count($emailCheck['Items']) > 0) {
        echo "<!DOCTYPE html><html><head><title>Registration Error</title></head><body><p>Email already registered.</p><a href='register.html'>Back to Register</a></body></html>";
        exit();
    }
    
    error_log("[register.php] putItem starting for username={$username}, email={$email}");

    $result = $client->putItem([
        'TableName' => 'Users',
        'Item' => [
            "UserID" => ["S" => $userID],
            "CreationDate" => ["S" => $creationDate],
            'username' => ['S' => $username],
            'password' => ['S' => $password],
            'email'    => ['S' => $email],
        ]
    ]);

    error_log("[register.php] putItem succeeded: " . json_encode($result));
    
    echo "<!DOCTYPE html><html><head><title>Registration Success</title></head><body><p>Registration successful! Redirecting...</p></body></html>";
    header("Location: login.html");
    exit();

} catch (Exception $e) {
    error_log("[register.php] putItem failed: " . $e->getMessage());
    
    echo "<!DOCTYPE html><html><head><title>Registration Error</title></head><body><p>Database Error: " . htmlspecialchars($e->getMessage()) . "</p><a href='register.html'>Back to Register</a></body></html>";
}
?>
