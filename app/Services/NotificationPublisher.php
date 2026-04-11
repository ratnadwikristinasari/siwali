<?php

namespace App\Services;

use Illuminate\Support\Facades\Queue;

class NotificationPublisher
{
    public function send(array $payload): void
    {
        Queue::connection('rabbitmq')
            ->pushRaw(json_encode($payload), 'notification_queue');
    }
}
