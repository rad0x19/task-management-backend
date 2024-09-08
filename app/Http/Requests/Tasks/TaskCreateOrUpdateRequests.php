<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class TaskCreateOrUpdateRequests extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'isDraft'                       =>  'required|in:true,false',
            'title'                         =>  'required|string|max:100|unique:tasks',
            'priority'                      =>  'required|numeric',
            'description'                   =>  'required|string|min:5',
            'attachments.*'                   =>  'sometimes|image|max:4000',
            'subTasks'                      =>  'sometimes|array',
            'subTasks.*.title'                =>  'required|string',
            'subTasks.*.priority'             =>  'required|numeric',
            'subTasks.*.description'          =>  'required|string|min:5'
        ];
    }
}
