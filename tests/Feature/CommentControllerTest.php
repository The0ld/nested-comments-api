<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    /**
     * Set up the authenticated user before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate a user
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /**
     * Test that an authenticated user can retrieve comments.
     */
    public function test_authenticated_user_can_get_comments()
    {
        // Create comments
        Comment::factory(5)->create();

        $response = $this->getJson('/api/v1/comments');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'comment_text',
                             'parent_id',
                             'author' => [
                                 'id',
                                 'name',
                                 'email',
                                 'phone_number',
                                 'is_admin',
                             ],
                             'created_at',
                             'updated_at',
                             'replies',
                         ],
                     ],
                 ]);
    }

    /**
     * Test that the index respects ordering parameters.
     */
    public function test_comments_index_respects_ordering()
    {
        // Create comments with different created_at timestamps
        Comment::factory(5)->sequence(
            fn ($sequence) => ['created_at' => now()->addSeconds($sequence->index)]
        )->create();

        $responseAsc = $this->getJson('/api/v1/comments?orderBy=created_at&direction=asc');
        $responseDesc = $this->getJson('/api/v1/comments?orderBy=created_at&direction=desc');

        $responseAsc->assertStatus(200);
        $responseDesc->assertStatus(200);

        $commentsAsc = $responseAsc->json('data');
        $commentsDesc = $responseDesc->json('data');

        $this->assertEquals(
            array_column($commentsAsc, 'id'),
            array_reverse(array_column($commentsDesc, 'id'))
        );
    }

    /**
     * Test that an authenticated user can create a comment.
     */
    public function test_authenticated_user_can_create_comment()
    {
        $data = [
            'comment' => 'This is a test comment.',
            'parent_id' => null,
        ];

        $response = $this->postJson('/api/v1/comments', $data);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                        'id',
                        'comment_text',
                        'parent_id',
                        'author' => [
                            'id',
                            'name',
                            'email',
                            'phone_number',
                            'is_admin',
                        ],
                        'created_at',
                        'updated_at'
                     ],
                 ]);
    }

    /**
     * Test validation errors when creating a comment.
     */
    public function test_create_comment_validation_errors()
    {
        $response = $this->postJson('/api/v1/comments', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['comment']);
    }

    /**
     * Test that a user can update their own comment.
     */
    public function test_user_can_update_own_comment()
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id]);

        $data = ['comment' => 'Updated comment text.'];

        $response = $this->patchJson("/api/v1/comments/{$comment->id}", $data);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                        'id',
                        'comment_text',
                        'parent_id',
                        'author' => [
                            'id',
                            'name',
                            'email',
                            'phone_number',
                            'is_admin',
                        ],
                        'created_at',
                        'updated_at'
                     ],
                 ]);
    }

    /**
     * Test that a user cannot update someone else's comment.
     */
    public function test_user_cannot_update_others_comment()
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $otherUser->id]);

        $data = ['comment' => 'Attempt to update another user\'s comment.'];

        $response = $this->putJson("/api/v1/comments/{$comment->id}", $data);

        $response->assertStatus(403); // Forbidden
    }

    /**
     * Test validation errors when updating a comment.
     */
    public function test_update_comment_validation_errors()
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("/api/v1/comments/{$comment->id}", []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['comment']);
    }

    /**
     * Test that a user can delete their own comment.
     */
    public function test_user_can_delete_own_comment()
    {
        $comment = Comment::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/v1/comments/{$comment->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Comment deleted successfully',
                 ]);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    /**
     * Test that a user cannot delete someone else's comment.
     */
    public function test_user_cannot_delete_others_comment()
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson("/api/v1/comments/{$comment->id}");

        $response->assertStatus(403); // Forbidden

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }
}
