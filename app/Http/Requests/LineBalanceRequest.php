<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LineBalanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(session('is_checker')){
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'ppc_output_per_hr' => 'required',
            'tbl_line_bal' => 'required',
        ];
    }

    public function exeptTokenParameters(){
        return $this->except('_token');

    }
}
