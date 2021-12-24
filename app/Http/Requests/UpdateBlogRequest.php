<?php

namespace App\Http\Requests;

use App\Rules\FileExist;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:200',
            'body' => 'sometimes|nullable|max:2000',
            'status' => 'sometimes|required|boolean',
            'publish_at' => 'sometimes|nullable|date',
            // tag:"" is clear
            'tags' => 'sometimes|array|nullable|max:6',
            'tags.*' => 'required',
        ];
    }
}
