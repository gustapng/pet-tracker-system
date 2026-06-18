<?php

declare(strict_types=1);

namespace App\Controller;

use App\Amqp\Producer\TrackLocationProducer;
use Hyperf\Amqp\Producer;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\RequestInterface;

#[Controller(prefix: "api/location")]
class LocationController extends AbstractController
{
    #[Inject]
    private Producer $producer;

    #[PostMapping(path: "update")]
    public function update(RequestInterface $request)
    {
        $data = [
            'pet_id' => $request->input('pet_id'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'timestamp' => time(),
        ];

        $message = new TrackLocationProducer($data);

        $this->producer->produce($message);

        return [
            'status' => 'success',
            'message' => 'Location queued for processing'
        ];
    }
}