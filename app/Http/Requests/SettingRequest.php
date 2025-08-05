<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
        switch ($this->action) {
            case 'add_remarks':
                return [
                    'remarks' => 'required'
                ];
                break;
            case 'add_category':
                return [
                    'category' => 'required'
                ];
                break;
            default:
                return [];
                break;
        }
     
    }

    public function filterParameters()
    {
        return $this->except('token');
    }
}
