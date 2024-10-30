<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'comment_text'=> $this->comment_text,
            'parent_id'   => $this->parent_id,
            'author'      => [
                'id'           => $this->user->id,
                'name'         => $this->user->name,
                'email'        => $this->user->email,
                'phone_number' => $this->user->phone_number,
                'is_admin'     => $this->user->is_admin,
            ],
            'created_at'  => $this->created_at->toDateTimeString(),
            'updated_at'  => $this->updated_at->toDateTimeString(),
            'replies'     => CommentResource::collection($this->whenLoaded('replies')),
        ];
    }
}
