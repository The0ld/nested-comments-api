<?php

namespace Database\Seeders;

use App\Models\Comment;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 100 top-level comments
        Comment::factory(100)->create()->each(function ($firstLevel) {
            // For each top-level comment, create 1-3 replies in the second level
            Comment::factory(rand(1, 3))->create(['parent_id' => $firstLevel->id])->each(function ($secondLevel) {
                // For each second-level, create 1-3 nested replies in the third level
                Comment::factory(rand(1, 3))->create(['parent_id' => $secondLevel->id])->each(function ($thirdLevel) {
                    // For each third-level, create 1-3 nested replies in the fourth level
                    Comment::factory(rand(1, 3))->create(['parent_id' => $thirdLevel->id])->each(function ($fourthLevel) {
                        // For each fourth-level, create 1-3 nested replies in the fifth level
                        Comment::factory(rand(1, 3))->create(['parent_id' => $fourthLevel->id]);
                    });
                });
            });
        });
    }
}
