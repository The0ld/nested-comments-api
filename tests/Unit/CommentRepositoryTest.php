<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Repositories\CommentRepository;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $commentRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commentRepository = new CommentRepository();
    }

    public function test_can_get_paginated_root_comments_with_user_and_replies()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Comment::factory(3)->create([
            'user_id' => $user1->id,
            'parent_id' => null,
        ]);

        Comment::factory(2)->create([
            'user_id' => $user2->id,
            'parent_id' => Comment::first()->id,
        ]);

        $comments = $this->commentRepository->getRootComments('created_at', 'asc');

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $comments);
        $this->assertCount(3, $comments);

        foreach ($comments as $comment) {
            $this->assertTrue($comment->relationLoaded('user'));
            $this->assertTrue($comment->relationLoaded('replies'));
        }
    }

    public function test_sorts_comments_by_allowed_columns_and_directions()
    {
        $user = User::factory()->create();

        $comment1 = Comment::factory()->create([
            'user_id' => $user->id,
            'parent_id' => null,
            'created_at' => now()->subDays(2),
        ]);

        $comment2 = Comment::factory()->create([
            'user_id' => $user->id,
            'parent_id' => null,
            'created_at' => now()->subDay(),
        ]);

        $comment3 = Comment::factory()->create([
            'user_id' => $user->id,
            'parent_id' => null,
            'created_at' => now(),
        ]);

        $commentsAsc = $this->commentRepository->getRootComments('created_at', 'asc');
        $this->assertEquals([$comment1->id, $comment2->id, $comment3->id], $commentsAsc->pluck('id')->all());

        $commentsDesc = $this->commentRepository->getRootComments('created_at', 'desc');
        $this->assertEquals([$comment3->id, $comment2->id, $comment1->id], $commentsDesc->pluck('id')->all());

        $commentsDefault = $this->commentRepository->getRootComments('invalid_column', 'asc');
        $this->assertEquals([$comment1->id, $comment2->id, $comment3->id], $commentsDefault->pluck('id')->all());

        $commentsDefaultDirection = $this->commentRepository->getRootComments('created_at', 'invalid_direction');
        $this->assertEquals([$comment1->id, $comment2->id, $comment3->id], $commentsDefaultDirection->pluck('id')->all());
    }

    public function test_can_create_a_new_comment()
    {
        $user = User::factory()->create();

        $data = [
            'comment' => 'Este es un comentario de prueba.',
            'parent_id' => null,
        ];

        $comment = $this->commentRepository->createComment($user, $data);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals('Este es un comentario de prueba.', $comment->comment);
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertNull($comment->parent_id);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'comment' => 'Este es un comentario de prueba.',
            'user_id' => $user->id,
            'parent_id' => null,
        ]);
    }

    public function test_can_update_an_existing_comment()
    {
        $user = User::factory()->create();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'comment' => 'Comentario original.',
        ]);

        $data = [
            'comment' => 'Comentario actualizado.',
        ];

        $this->commentRepository->updateComment($comment, $data);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'comment' => 'Comentario actualizado.',
        ]);
    }

    public function test_can_delete_a_comment()
    {
        $user = User::factory()->create();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->commentRepository->deleteComment($comment);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }
}

