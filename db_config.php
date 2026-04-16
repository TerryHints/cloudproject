<?php
require 'vendor/autoload.php';
use Aws\DynamoDb\DynamoDbClient;

$client = new DynamoDbClient([
    'region'  => 'us-east-1',
    'version' => 'latest'
]);
?>