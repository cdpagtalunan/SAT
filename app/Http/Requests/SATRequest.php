<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SATRequest extends FormRequest
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
            'device_name' => 'required',
            'operation_line' => 'required',
            'assembly_line' => 'required',
        ];
    }
    public function filterParameters()
    {
        return $this->except('_token');
    }

}
