<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class M3u extends FormRequest
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
            'lamedb_host' => 'required',
            'lamedb_file' => 'required|file',
        ];
    }
}
