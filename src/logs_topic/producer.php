<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * USAGE EXAMPLE: php producer.php "kern.error"
 */

$source_severity = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'any.info';

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'test', 'test');
$channel = $connection->channel();

$channel->exchange_declare('topic_logs', 'topic', false, false, false);

$msg = new AMQPMessage('Log!');
$channel->basic_publish($msg, 'topic_logs', $source_severity);

echo " [x] Sent log!\n";

$channel->close();

try {
    $connection->close();
}
catch (Exception $exception) {
    echo $exception->getMessage();
}