<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetCommentsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'orderBy' => 'nullable|string|in:created_at,updated_at,user_id',
            'direction' => 'nullable|string|in:asc,desc',
        ];
    }

    public function messages()
    {
        return [
            'orderBy.in' => 'The orderBy field must be one of the following: created_at, updated_at, user_id.',
            'direction.in' => 'The direction field must be either asc or desc.',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'orderBy' => $this->query('orderBy', 'created_at'),
            'direction' => $this->query('direction', 'asc'),
        ]);
    }
}
