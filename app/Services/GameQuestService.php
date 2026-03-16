<?php

namespace App\Services;

use App\Models\Quest;
use Illuminate\Support\Facades\Storage;

class GameQuestionService
{
    public function create(array $data)
    {
        $options = null;
        $answer = null;

        // MULTIPLE CHOICE
        if ($data['type'] === 'multiple_choice') {

            $options = collect($data['option'] ?? [])
                ->filter()
                ->map(function ($text, $key) {
                    return [
                        'key' => $key,
                        'text' => $text
                    ];
                })
                ->values()
                ->toArray();

            $answer = [
                'correct_answer' => $data['correct_answer']
            ];
        }

        // TEXT QUESTION
        if ($data['type'] === 'text') {

            $answer = [
                'correct_answer' => $data['correct_answer']
            ];
        }

        $imagePath = null;

        if (!empty($data['image_url'])) {
            $imagePath = $data['image_url']->store('quests', 'public');
        }

        return Quest::create([
            'type' => $data['type'],
            'prompt' => $data['prompt'],
            'option' => $options,
            'answer' => $answer,
            'image_url' => $imagePath,
            'is_active' => true
        ]);
    }

    public function update(Quest $quest, array $data)
    {
        $options = $quest->option;
        $answer = $quest->answer;
        $imagePath = $quest->image_url;

        if (isset($data['type']) && $data['type'] === 'multiple_choice') {

            if (isset($data['option'])) {
                $options = collect($data['option'])
                    ->filter()
                    ->map(function ($text, $key) {
                        return [
                            'key' => $key,
                            'text' => $text
                        ];
                    })
                    ->values()
                    ->toArray();
            }

            if (isset($data['correct_answer'])) {
                $answer = [
                    'correct_answer' => $data['correct_answer']
                ];
            }
        }

        if (isset($data['type']) && $data['type'] === 'text') {

            if (isset($data['correct_answer'])) {
                $answer = [
                    'correct_answer' => $data['correct_answer']
                ];
            }

            $options = null;
        }

        if (!empty($data['image_url'])) {

            if ($quest->image_url) {
                Storage::disk('public')->delete($quest->image_url);
            }

            $imagePath = $data['image_url']->store('quests', 'public');
        }

        $quest->update([
            'type' => $data['type'] ?? $quest->type,
            'prompt' => $data['prompt'] ?? $quest->prompt,
            'option' => $options,
            'answer' => $answer,
            'image_url' => $imagePath,
            'is_active' => $data['is_active'] ?? $quest->is_active
        ]);

        return $quest;
    }
}
