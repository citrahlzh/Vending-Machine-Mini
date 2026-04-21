<?php

namespace App\Console\Commands;

use App\Services\TransactionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;

class MqttSubscribe extends Command
{
    protected $signature = 'mqtt:subscribe
                            {--topic= : Override topic MQTT payment}
                            {--qos=0 : QoS subscription}';
    protected $description = 'Subscribe MQTT topic for payment notifications';

    public function handle(TransactionService $transactionService): int
    {
        $server = (string) config('mqtt-client.connections.default.host');
        $port = (int) config('mqtt-client.connections.default.port', 1883);
        $clientId = (string) (config('mqtt-client.connections.default.client_id') ?: ('vending-machine-' . php_uname('n')));
        $username = config('mqtt-client.connections.default.connection_settings.auth.username');
        $password = config('mqtt-client.connections.default.connection_settings.auth.password');
        $mqttVersion = (string) config('mqtt-client.connections.default.protocol', MqttClient::MQTT_3_1);
        $topic = (string) ($this->option('topic') ?: env('MQTT_PAYMENT_TOPIC', 'midtrans/payment'));
        $qos = (int) $this->option('qos');
        $keepAlive = (int) config('mqtt-client.connections.default.connection_settings.keep_alive_interval', 60);
        $lastWillTopic = env('MQTT_LAST_WILL_TOPIC');
        $lastWillMessage = env('MQTT_LAST_WILL_MESSAGE');
        $lastWillQos = (int) env('MQTT_LAST_WILL_QUALITY_OF_SERVICE', 0);
        $useCleanSession = filter_var(config('mqtt-client.connections.default.use_clean_session', true), FILTER_VALIDATE_BOOL);

        if ($server === '') {
            $this->error('MQTT host belum dikonfigurasi.');
            return self::FAILURE;
        }

        $connectionSettings = (new ConnectionSettings)
            ->setKeepAliveInterval($keepAlive);

        if ($username !== null && $username !== '') {
            $connectionSettings = $connectionSettings->setUsername((string) $username);
        }

        if ($password !== null && $password !== '') {
            $connectionSettings = $connectionSettings->setPassword((string) $password);
        }

        if ($lastWillTopic) {
            $connectionSettings = $connectionSettings
                ->setLastWillTopic((string) $lastWillTopic)
                ->setLastWillMessage((string) $lastWillMessage)
                ->setLastWillQualityOfService($lastWillQos);
        }

        $mqtt = new MqttClient($server, $port, $clientId, $mqttVersion);

        $mqtt->connect($connectionSettings, $useCleanSession);

        $this->info("Connected to MQTT broker {$server}:{$port}");
        $this->info("Subscribed topic: {$topic}");

        $mqtt->subscribe($topic, function ($topic, $message) use ($transactionService) {
            $this->line("Message received on [{$topic}]: {$message}");

            $payload = json_decode($message, true);
            if (!is_array($payload)) {
                Log::warning('MQTT payment notification ignored: invalid JSON payload', [
                    'topic' => $topic,
                    'message' => $message,
                ]);
                return;
            }

            Log::info('MQTT payment notification received', [
                'topic' => $topic,
                'payload' => $payload,
            ]);

            try {
                $sale = $transactionService->handleMqttNotification($payload);
            } catch (\Throwable $throwable) {
                Log::error('MQTT payment notification processing failed', [
                    'topic' => $topic,
                    'payload' => $payload,
                    'error' => $throwable->getMessage(),
                ]);

                return;
            }

            if (!$sale) {
                Log::warning('MQTT payment notification did not match any sale', [
                    'topic' => $topic,
                    'payload' => $payload,
                ]);
                return;
            }

            Log::info('MQTT payment notification applied to sale', [
                'topic' => $topic,
                'sale_id' => $sale->id,
                'idempotency_key' => $sale->idempotency_key,
                    'status' => $sale->status,
                    'dispense_status' => $sale->dispense_status,
                ]);
        }, $qos);

        $mqtt->loop(true);

        return self::SUCCESS;
    }
}
