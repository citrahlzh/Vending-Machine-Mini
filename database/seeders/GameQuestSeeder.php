<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Quest;
use App\Models\GameQuest;

class GameQuestSeeder extends Seeder
{
    public function run(): void
    {
        $quizGame = Game::firstOrCreate(
            [
                'name' => 'Quiz Pengetahuan Umum',
                'type' => 'quiz',
            ],
            [
                'config_json' => [
                    'question_count' => 5,
                    'time_limit' => 60,
                ],
                'is_active' => true,
                'start_date' => null,
                'end_date' => null,
            ]
        );

        $guessGame = Game::firstOrCreate(
            [
                'name' => 'Tebak Gambar Seru',
                'type' => 'guess_image',
            ],
            [
                'config_json' => [
                    'question_count' => 3,
                    'time_limit' => 60,
                ],
                'is_active' => true,
                'start_date' => null,
                'end_date' => null,
            ]
        );

        $quizQuests = [
            [
                'type' => 'multiple_choice',
                'game_type' => 'quiz',
                'prompt' => 'Siapa Presiden pertama Indonesia?',
                'option' => [
                    ['key' => 'A', 'text' => 'Soekarno'],
                    ['key' => 'B', 'text' => 'Soeharto'],
                    ['key' => 'C', 'text' => 'B. J. Habibie'],
                    ['key' => 'D', 'text' => 'Abdurrahman Wahid'],
                ],
                'answer' => ['correct_answer' => 'A'],
            ],
            [
                'type' => 'multiple_choice',
                'game_type' => 'quiz',
                'prompt' => 'Ibukota Indonesia adalah?',
                'option' => [
                    ['key' => 'A', 'text' => 'Bandung'],
                    ['key' => 'B', 'text' => 'Jakarta'],
                    ['key' => 'C', 'text' => 'Surabaya'],
                    ['key' => 'D', 'text' => 'Yogyakarta'],
                ],
                'answer' => ['correct_answer' => 'B'],
            ],
            [
                'type' => 'multiple_choice',
                'game_type' => 'quiz',
                'prompt' => 'Planet terdekat dengan Matahari adalah?',
                'option' => [
                    ['key' => 'A', 'text' => 'Venus'],
                    ['key' => 'B', 'text' => 'Bumi'],
                    ['key' => 'C', 'text' => 'Merkurius'],
                    ['key' => 'D', 'text' => 'Mars'],
                ],
                'answer' => ['correct_answer' => 'C'],
            ],
            [
                'type' => 'text',
                'game_type' => 'quiz',
                'prompt' => 'Sebutkan nama proklamator Indonesia (satu nama saja).',
                'option' => null,
                'answer' => ['correct_answer' => 'Soekarno'],
            ],
            [
                'type' => 'text',
                'game_type' => 'quiz',
                'prompt' => 'Berapa jumlah hari dalam satu minggu?',
                'option' => null,
                'answer' => ['correct_answer' => '7'],
            ],
        ];

        $guessQuests = [
            [
                'type' => 'text',
                'game_type' => 'guess_image',
                'prompt' => 'Gambar ini adalah hewan apa?',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/7/74/Komodo_dragon_with_tongue.jpg',
                'answer' => ['correct_answer' => 'Komodo'],
            ],
            [
                'type' => 'text',
                'game_type' => 'guess_image',
                'prompt' => 'Bangunan ikonik ini bernama?',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/1/19/Eiffel_Tower_at_night.jpg',
                'answer' => ['correct_answer' => 'Menara Eiffel'],
            ],
            [
                'type' => 'multiple_choice',
                'game_type' => 'guess_image',
                'prompt' => 'Buah pada gambar ini adalah?',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/8/8a/Banana-Single.jpg',
                'option' => [
                    ['key' => 'A', 'text' => 'Apel'],
                    ['key' => 'B', 'text' => 'Pisang'],
                    ['key' => 'C', 'text' => 'Anggur'],
                    ['key' => 'D', 'text' => 'Jeruk'],
                ],
                'answer' => ['correct_answer' => 'B'],
            ],
        ];

        $quizQuestIds = [];
        foreach ($quizQuests as $payload) {
            $quest = Quest::firstOrCreate(
                [
                    'prompt' => $payload['prompt'],
                    'game_type' => $payload['game_type'],
                ],
                [
                    'type' => $payload['type'],
                    'option' => $payload['option'] ?? null,
                    'answer' => $payload['answer'] ?? null,
                    'image_url' => $payload['image_url'] ?? null,
                    'is_active' => true,
                ]
            );
            $quizQuestIds[] = $quest->id;
        }

        $guessQuestIds = [];
        foreach ($guessQuests as $payload) {
            $quest = Quest::firstOrCreate(
                [
                    'prompt' => $payload['prompt'],
                    'game_type' => $payload['game_type'],
                ],
                [
                    'type' => $payload['type'],
                    'option' => $payload['option'] ?? null,
                    'answer' => $payload['answer'] ?? null,
                    'image_url' => $payload['image_url'] ?? null,
                    'is_active' => true,
                ]
            );
            $guessQuestIds[] = $quest->id;
        }

        foreach ($quizQuestIds as $index => $questId) {
            GameQuest::updateOrCreate(
                [
                    'game_id' => $quizGame->id,
                    'quest_id' => $questId,
                ],
                [
                    'order' => $index + 1,
                ]
            );
        }

        foreach ($guessQuestIds as $index => $questId) {
            GameQuest::updateOrCreate(
                [
                    'game_id' => $guessGame->id,
                    'quest_id' => $questId,
                ],
                [
                    'order' => $index + 1,
                ]
            );
        }
    }
}
