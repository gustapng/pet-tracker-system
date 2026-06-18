<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPTimeoutException;

class ConsumePetLocations extends Command
{
    protected $signature = 'rabbitmq:consume-locations';
    
    protected $description = 'Consome as coordenadas de GPS enviadas pelo Hyperf via RabbitMQ';

    public function handle()
    {
        $connection = new AMQPStreamConnection(
            host: 'localhost',
            port: 5672,
            user: 'guest',
            password: 'guest',
            vhost: '/',
            insist: false,
            login_method: 'AMQPLAIN',
            login_response: null,
            locale: 'en_US',
            connection_timeout: 3.0,
            read_write_timeout: 60.0,
            context: null,
            keepalive: false,
            heartbeat: 30
        );

        $channel = $connection->channel();

        $exchange = 'pet_tracking_exchange';
        $queue = 'pet_tracking_queue';
        $routingKey = 'pet.location.update';

        $channel->exchange_declare($exchange, 'topic', false, true, false);
        $channel->queue_declare($queue, false, true, false, false);
        $channel->queue_bind($queue, $exchange, $routingKey);

        $this->info(" [*] Worker Blindado escutando a fila '$queue'. Pressione CTRL+C para sair.");

        $callback = function (AMQPMessage $msg) {
            $payload = json_decode($msg->body, true);
            $this->line(" [x] Nova coordenada! Pet ID: " . $payload['pet_id'] . " | Lat: " . $payload['latitude'] . " | Lng: " . $payload['longitude']);
            
            $channel = $msg->delivery_info['channel'];
            $deliveryTag = $msg->delivery_info['delivery_tag'];
            
            $channel->basic_ack($deliveryTag);
        };

        $channel->basic_consume($queue, '', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            try {
                $channel->wait(null, false, 3);
            } catch (AMQPTimeoutException $e) {

            }
        }

        $channel->close();
        $connection->close();
    }
}
