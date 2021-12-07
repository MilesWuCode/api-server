<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:200',
            'body' => 'nullable|max:2000',
            'status' => 'required|boolean',
            'publish_at' => 'nullable|date',
            'tags' => 'sometimes|array|nullable|max:5',
            'tags.*' => 'required',
        ];
    }
}
