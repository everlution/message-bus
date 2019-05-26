<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Transport;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqTransport implements TransportInterface
{
    private $connection;

    /** @var AMQPChannel */
    private $channel;

    private $exchange;

    private $consumerName;

    private $consumerTag;

    public function __construct(
        AMQPStreamConnection $connection,
        string $exchange,
        string $consumerName
    ) {
        $this->connection = $connection;
        $this->exchange = $exchange;
        $this->consumerName = $consumerName;
        $this->consumerTag = sprintf('%s-%s', $consumerName, mt_rand(0, 10000));
    }

    private function init()
    {
        if (!$this->channel) {
            $this->channel = $this
                ->connection
                ->channel();

            /*
                name: $exchange
                type: fanout
                passive: false // don't check is an exchange with the same name exists
                durable: false // the exchange won't survive server restarts
                auto_delete: true // the exchange will be deleted once the channel is closed.
            */
            $this
                ->channel
                ->exchange_declare($this->exchange, AMQPExchangeType::FANOUT, false, false, false);

            register_shutdown_function([$this, 'shutdown']);
        }
    }

    public function publish(string $payload): void
    {
        $this->init();

        $message = new AMQPMessage($payload, ['content_type' => 'text/plain']);

        $this
            ->channel
            ->basic_publish($message, $this->exchange);
    }

    public function consume(callable $callback, int $numberOfMessages): void
    {
        $this->init();

        $queue = $this->consumerName;

        /*
            name: $queue    // should be unique in fanout exchange.
            passive: false  // don't check if a queue with the same name exists
            durable: false // the queue will not survive server restarts
            exclusive: false // the queue might be accessed by other channels
            auto_delete: true //the queue will be deleted once the channel is closed.
        */
        $this
            ->channel
            ->queue_declare($queue, false, false, false, false);

        $this
            ->channel
            ->queue_bind($queue, $this->exchange);

        /**
         * @param \PhpAmqpLib\Message\AMQPMessage $message
         */
        $function = function ($message) use ($callback) {
            /** @var AMQPChannel $channel */
            $channel = $message->delivery_info['channel'];

            $rawMessage = $message->body;

            try {
                $callback($rawMessage);

                $channel->basic_ack($message->delivery_info['delivery_tag']);
            } catch (\Exception $e) {
                // reenqueues the message
                $channel->basic_reject($message->delivery_info['delivery_tag'], true);
            }
        };

        /*
            queue: Queue from where to get the messages
            consumer_tag: Consumer identifier
            no_local: Don't receive messages published by this consumer.
            no_ack: If set to true, automatic acknowledgement mode will be used by this consumer. See https://www.rabbitmq.com/confirms.html for details.
            exclusive: Request exclusive consumer access, meaning only this consumer can access the queue
            nowait: don't wait for a server response. In case of error the server will raise a channel
                    exception
            callback: A PHP Callback
        */
        $this
            ->channel
            ->basic_consume($queue, $this->consumerTag, false, false, false, false, $function);

        // Loop as long as the channel has callbacks registered
        while ($this->channel->is_consuming()) {
            $this
                ->channel
                ->wait();

            if (-- $numberOfMessages == 0) {
                return;
            }
        }
    }

    public function shutdown(): void
    {
        $this
            ->channel
            ->close();

        $this
            ->connection
            ->close();
    }
}
