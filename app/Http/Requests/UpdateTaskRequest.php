<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string|max:2000',
            'difficulty' => 'sometimes|in:easy,medium,hard',
            'is_completed' => 'prohibited',
            'deadline' => 'prohibited',
        ];
    }
}