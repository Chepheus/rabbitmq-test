<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'test', 'test');
$channel = $connection->channel();

$channel->queue_declare('tasks', false, true, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C' . PHP_EOL;

$channel->basic_qos(null, 1, null);
$channel->basic_consume('tasks', '', false, false, false, false, static function ($msg) {
    echo ' [x] Received ' . $msg->body . PHP_EOL;
    sleep(substr_count($msg->body, '.'));
    echo ' [x] Done' . PHP_EOL;

    /** @var PhpAmqpLib\Channel\AMQPChannel $messageChannel */
    $messageChannel = $msg->delivery_info['channel'];

    $messageChannel->basic_ack($msg->delivery_info['delivery_tag']);
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