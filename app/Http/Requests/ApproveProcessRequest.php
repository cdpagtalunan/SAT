<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveProcessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(session('is_approver')){
            if(in_array($this->approval_type, session('approver_type'))){
                return true;
            }
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
            'approval_id' => 'required',
            'approval_type' => 'required'
        ];
    }

    public function filterParameters()
    {
        return $this->except('_token');
    }
}
