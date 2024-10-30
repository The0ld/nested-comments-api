<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentControllerGuestTest extends TestCase
{
    use RefreshDatabase;

    protected $comment;

    /**
     * Set up the comment before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->comment = Comment::factory()->create();
    }

    /**
     * Test that an unauthenticated user cannot access the comments index.
     */
    public function test_guest_cannot_access_comments_index()
    {
        $response = $this->getJson('/api/v1/comments');

        $response->assertStatus(401); // Unauthorized
    }

    /**
     * Test that an unauthenticated user cannot create a comment.
     */
    public function test_guest_cannot_create_comment()
    {
        $data = [
            'comment' => 'This is a test comment.',
        ];

        $response = $this->postJson('/api/v1/comments', $data);

        $response->assertStatus(401); // Unauthorized
    }

    /**
     * Test that an unauthenticated user cannot update a comment.
     */
    public function test_guest_cannot_update_comment()
    {
        $data = [
            'comment' => 'This is a test comment.',
        ];

        $response = $this->patchJson('/api/v1/comments/{$this->comment->id}', $data);

        $response->assertStatus(401); // Unauthorized
    }

    /**
     * Test that an unauthenticated user cannot delete a comment.
     */
    public function test_guest_cannot_delete_comment()
    {
        $data = [
            'comment' => 'This is a test comment.',
        ];

        $response = $this->deleteJson('/api/v1/comments/{$this->comment->id}', $data);

        $response->assertStatus(401); // Unauthorized
    }
}
