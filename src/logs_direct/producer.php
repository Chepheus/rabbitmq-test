<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * USAGE EXAMPLE: php producer.php error
 */

$severity = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'info';

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'test', 'test');
$channel = $connection->channel();

$channel->exchange_declare('direct_logs', 'direct', false, false, false);

$msg = new AMQPMessage('Log!');
$channel->basic_publish($msg, 'direct_logs', $severity);

echo " [x] Sent log!\n";

$channel->close();

try {
    $connection->close();
}
catch (Exception $exception) {
    echo $exception->getMessage();
}