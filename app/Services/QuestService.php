<?php

namespace App\Services;

use App\Models\Quest;
use Illuminate\Support\Facades\Storage;

class QuestService
{
    public function create(array $data)
    {
        $imagePath = null;

        /*
        =========================
        UPLOAD IMAGE
        =========================
        */

        if (isset($data['image_url']) && $data['image_url']) {
            $imagePath = $data['image_url']->store('quests', 'public');
        }

        /*
        =========================
        OPTION JSON
        =========================
        */

        $options = null;

        if ($data['type'] === 'multiple_choice' && isset($data['option'])) {

            $options = [];

            foreach ($data['option'] as $key => $text) {

                if (!$text) {
                    continue;
                }

                $options[] = [
                    'key' => $key,
                    'text' => $text
                ];
            }
        }

        /*
        =========================
        ANSWER JSON
        =========================
        */

        $answer = [
            'correct_answer' => $data['correct_answer']
        ];

        /*
        =========================
        CREATE QUEST
        =========================
        */

        return Quest::create([
            'type' => $data['type'],
            'game_type' => $data['game_type'],
            'prompt' => $data['prompt'],
            'option' => $options,
            'answer' => $answer,
            'image_url' => $imagePath,
            'is_active' => true
        ]);
    }

    public function update(Quest $quest, array $data)
    {
        $payload = [];

        if (array_key_exists('type', $data)) {
            $payload['type'] = $data['type'];
        }

        if (array_key_exists('game_type', $data)) {
            $payload['game_type'] = $data['game_type'];
        }

        if (array_key_exists('prompt', $data)) {
            $payload['prompt'] = $data['prompt'];
        }

        if (array_key_exists('is_active', $data)) {
            $payload['is_active'] = (bool) $data['is_active'];
        }

        if (isset($data['image_url']) && $data['image_url']) {
            if ($quest->image_url) {
                Storage::disk('public')->delete($quest->image_url);
            }

            $payload['image_url'] = $data['image_url']->store('quests', 'public');
        }

        if (array_key_exists('correct_answer', $data)) {
            $payload['answer'] = [
                'correct_answer' => $data['correct_answer']
            ];
        }

        if (array_key_exists('type', $data) && $data['type'] !== 'multiple_choice') {
            $payload['option'] = null;
        } elseif (isset($data['option'])) {
            $options = [];

            foreach ($data['option'] as $key => $text) {
                if (!$text) {
                    continue;
                }

                $options[] = [
                    'key' => $key,
                    'text' => $text
                ];
            }

            $payload['option'] = $options ?: null;
        }

        $quest->update($payload);

        return $quest;
    }
}
