<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'test', 'test');
$channel = $connection->channel();

$channel->exchange_declare('logs', 'fanout', false, false, false);

$msg = new AMQPMessage('Log!');
$channel->basic_publish($msg, 'logs');

echo " [x] Sent log!\n";

$channel->close();

try {
    $connection->close();
}
catch (Exception $exception) {
    echo $exception->getMessage();
}