<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SATProcessRequest extends FormRequest
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
        return [];
        // return [
        //     'obs1' => 'nullable|numeric',
        //     'obs2' => 'nullable|numeric',
        //     'obs3' => 'nullable|numeric',
        //     'obs4' => 'nullable|numeric',
        //     'obs5' => 'nullable|numeric'
        // ];
    }

    public function filterParameters()
    {
        return $this->except('_token');
    }
}
