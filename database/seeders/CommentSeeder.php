<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comment::withoutEvents(function () {
            $userIds = User::pluck('id')->toArray();

            $levels = 5;
            $totalComments = 10000;

            // Calculate the number of top-level comments
            $topLevelComments = $totalComments / ($levels); // Adjust the divisor to control the ratio

            $commentsToInsert = [];
            $faker = \Faker\Factory::create();

            // Generate top-level comments
            for ($i = 0; $i < $topLevelComments; $i++) {
                $commentsToInsert[] = [
                    'comment'     => $faker->sentence(),
                    'user_id'     => $userIds[array_rand($userIds)],
                    'parent_id'   => null,
                    'created_at' => $faker->dateTimeBetween('-1 years', 'now'),
                    'updated_at' => now(),
                ];
            }

            // Generate replies for each level
            $parentIds = range(1, $topLevelComments);
            $remainingComments = $totalComments - $topLevelComments;

            for ($level = 2; $level <= $levels; $level++) {
                $commentsPerLevel = intdiv($remainingComments, $levels - $level + 1);

                for ($i = 0; $i < $commentsPerLevel; $i++) {
                    $parentId = $parentIds[array_rand($parentIds)];

                    $commentsToInsert[] = [
                        'comment'     => $faker->sentence(),
                        'user_id'     => $userIds[array_rand($userIds)],
                        'parent_id'   => $parentId,
                        'created_at' => $faker->dateTimeBetween('-1 years', 'now'),
                        'updated_at' => now(),
                    ];
                }

                $remainingComments -= $commentsPerLevel;
                $currentTotal = count($commentsToInsert);
                $parentIds = range($topLevelComments + 1, $currentTotal);
            }

            // Insert comments in chunks
            DB::transaction(function () use ($commentsToInsert) {
                $chunks = array_chunk($commentsToInsert, 1000);
                foreach ($chunks as $chunk) {
                    Comment::insert($chunk);
                }
            });
        });
    }
}
