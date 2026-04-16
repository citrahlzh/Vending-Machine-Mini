<?php

namespace App\Console\Commands;

use App\Services\TransactionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MqttSubscribe extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Subscribe MQTT Topic';

    public function handle(TransactionService $transactionService): int
    {
        $server = 'mqtt.posmap.id'; // broker MQTT
        $port = 1883;
        $clientId = 'vendmac-shollu-01';
        $username = 'vendmac-shollu-01';
        $password = '1234shollu';
        $mqtt_version = MqttClient::MQTT_3_1;

        $connectionSettings = (new ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password)
            ->setKeepAliveInterval(60)
            ->setLastWillTopic('vendingshollu01')
            ->setLastWillQualityOfService(1);

        $mqtt = new MqttClient($server, $port, $clientId, $mqtt_version);

        // $connectionSettings = (new ConnectionSettings)
        //     ->setKeepAliveInterval(60);

        // $mqtt = new MqttClient($server, $port, $clientId);

        $mqtt->connect($connectionSettings, true);

        $this->info("Connected to MQTT broker!");

        $mqtt->subscribe('vendingshollu01', function ($topic, $message) use ($transactionService) {
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

            $sale = $transactionService->handleMqttNotification($payload);

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
        }, 0);

        $mqtt->loop(true);

        return self::SUCCESS;
    }
}
