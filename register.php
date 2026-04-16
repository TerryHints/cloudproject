require 'vendor/autoload.php';
use Aws\DynamoDb\DynamoDbClient;

$client = new DynamoDbClient(['region' => 'us-east-1', 'version' => 'latest']);

// In your form handling:
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure hashing!

$client->putItem([
    'TableName' => 'Users',
    'Item' => [
        'username' => ['S' => $username],
        'password' => ['S' => $password],
        'email'    => ['S' => $email],
    ]
]);
