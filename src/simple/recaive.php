<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'test', 'test');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C' . PHP_EOL;

$channel->basic_consume('hello', '', false, true, false, false, static function ($msg) {
    echo ' [x] Received ' . $msg->body . PHP_EOL;
});

while ($channel->is_consuming()) {
    try {
        $channel->wait();
    }
    catch (ErrorException $exception) {
        echo $exception->getMessage();
    }
}

$channel->close();

try {
    $connection->close();
}
catch (Exception $exception) {
    echo $exception->getMessage();
}