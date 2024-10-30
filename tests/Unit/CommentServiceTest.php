<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CommentService;
use App\Repositories\CommentRepository;
use App\Models\User;
use App\Models\Comment;
use App\Events\CommentPosted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Mockery;
use Illuminate\Auth\Access\AuthorizationException;

class CommentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $commentService;

    protected function setUp(): void
    {
        parent::setUp();

        $commentRepository = new CommentRepository();
        $this->commentService = new CommentService($commentRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_get_paginated_root_comments_with_user_and_replies()
    {
        $orderBy = 'created_at';
        $direction = 'asc';

        $comments = Comment::factory()->count(3)->create([
            'user_id' => User::factory()->create()->id,
            'parent_id' => null,
        ]);

        Comment::factory()->count(2)->create([
            'user_id' => User::factory()->create()->id,
            'parent_id' => $comments->first()->id,
        ]);

        $result = $this->commentService->getRootComments($orderBy, $direction);

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $result);
        $this->assertCount(3, $result);

        foreach ($result as $comment) {
            $this->assertTrue($comment->relationLoaded('user'));
            $this->assertTrue($comment->relationLoaded('replies'));
        }
    }

    public function test_can_create_a_new_comment_and_dispatch_event()
    {
        Event::fake();

        $user = User::factory()->create();

        $data = [
            'comment' => 'Este es un comentario de prueba.',
            'parent_id' => null,
        ];

        $comment = $this->commentService->createComment($user, $data);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals('Este es un comentario de prueba.', $comment->comment);
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertNull($comment->parent_id);

        Event::assertDispatched(CommentPosted::class, function ($event) use ($comment) {
            return $event->comment->id === $comment->id;
        });
    }

    public function test_can_update_an_existing_comment_when_authorized()
    {
        Gate::shouldReceive('authorize')
            ->once()
            ->with('update', Mockery::type(Comment::class))
            ->andReturnTrue();

        $user = User::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'comment' => 'Comentario original.',
        ]);

        $data = [
            'comment' => 'Comentario actualizado.',
        ];

        $this->commentService->updateComment($comment, $data);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'comment' => 'Comentario actualizado.',
        ]);
    }

    public function test_can_delete_a_comment_when_authorized()
    {
        Gate::shouldReceive('authorize')
            ->once()
            ->with('delete', Mockery::type(Comment::class))
            ->andReturnTrue();

        $user = User::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->commentService->deleteComment($comment);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_throws_exception_when_updating_comment_not_authorized()
    {
        Gate::shouldReceive('authorize')
            ->once()
            ->with('update', Mockery::type(Comment::class))
            ->andThrow(new AuthorizationException());

        $user = User::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'comment' => 'Comentario original.',
        ]);

        $data = [
            'comment' => 'Comentario actualizado.',
        ];

        $this->expectException(AuthorizationException::class);

        $this->commentService->updateComment($comment, $data);
    }

    public function test_throws_exception_when_deleting_comment_not_authorized()
    {
        Gate::shouldReceive('authorize')
            ->once()
            ->with('delete', Mockery::type(Comment::class))
            ->andThrow(new AuthorizationException());

        $user = User::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->expectException(AuthorizationException::class);

        $this->commentService->deleteComment($comment);
    }
}
