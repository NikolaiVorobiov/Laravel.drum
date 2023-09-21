<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|boolean',
            'priority' => 'required|in:1,2,3,4,5',
            'title' => 'required|string',
            'description' => 'required|string',
            'createdAt' => 'required|date',
            'completedAt' => 'nullable|date',
        ];
    }
}
