<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends FormRequest
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
            'page' => 'sometimes|numeric|min:1',
            'limit' => 'sometimes|numeric|min:1|max:100',
            'sort' => 'sometimes|in:id_asc,id_desc,updated_at_asc,updated_at_desc',
        ];
    }
}
