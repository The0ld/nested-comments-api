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

            $baseCommentsPerLevel = intdiv($totalComments, $levels);
            $extraComments = $totalComments % $levels;

            $parentIds = [null];
            $commentsToInsert = [];

            $faker = \Faker\Factory::create();

            for ($level = 1; $level <= $levels; $level++) {
                $commentsPerLevel = $baseCommentsPerLevel + ($level <= $extraComments ? 1 : 0);

                for ($i = 0; $i < $commentsPerLevel; $i++) {
                    $parentId = $parentIds[array_rand($parentIds)];

                    $commentsToInsert[] = [
                        'comment'    => $faker->sentence(),
                        'user_id'    => $userIds[array_rand($userIds)],
                        'parent_id'  => $parentId,
                        'created_at' => $faker->dateTimeBetween('-1 years', 'now'),
                        'updated_at' => now(),
                    ];
                }

                $currentTotal = count($commentsToInsert);
                $parentIds = range(1, $currentTotal);
            }

            DB::transaction(function () use ($commentsToInsert) {
                $chunks = array_chunk($commentsToInsert, 1000);

                foreach ($chunks as $chunk) {
                    Comment::insert($chunk);
                }
            });

        });
    }
}
