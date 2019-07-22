<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'test', 'test');
$channel = $connection->channel();

$channel->exchange_declare('logs', 'fanout', false, false, false);

// create temporary queue and get it's name
list($queue_name, ,) = $channel->queue_declare('', false, false, true, false);

$channel->queue_bind($queue_name, 'logs');

echo 'Queue name: ' . $queue_name . PHP_EOL;
echo ' [*] Waiting for messages. To exit press CTRL+C' . PHP_EOL;

$channel->basic_consume($queue_name, '', false, true, false, false, static function ($msg) {
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