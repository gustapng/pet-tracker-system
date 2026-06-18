<?php

declare(strict_types=1);

namespace App\Amqp\Producer;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;

#[Producer(exchange: 'pet_tracking_exchange', routingKey: 'pet.location.update')]
class TrackLocationProducer extends ProducerMessage
{
    public function __construct(array $locationData)
    {
        $this->payload = $locationData;
    }
}