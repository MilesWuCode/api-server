<?php

namespace App\Http\Requests;

// use App\Rules\FileExist;
use Illuminate\Foundation\Http\FormRequest;

class BlogFileDelRequest extends FormRequest
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
        $mediaClass = config('media-library.media_model');

        return [
            'collection' => 'required|in:gallery',
            'media_id' => ['required', 'exists:' . $mediaClass . ',id'],
        ];
    }
}
