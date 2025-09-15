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

    public function prepareForValidation()
    {
        if ($this->has('process_list')) {
            $processList = collect($this->process_list)->map(function ($item) {
                return [
                    'process_name' => trim(strip_tags($item['process_name'] ?? '')),
                    'allowance'    => is_numeric(strip_tags($item['allowance'] ?? ''))
                        ? (float) strip_tags($item['allowance'])
                        : null,
                ];
            });

            $this->merge([
                'process_list' => $processList->toArray(),
            ]);
        }
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'device_name'                 => 'required',
            'operation_line'              => 'required',
            'assembly_line'               => 'required',
            'qsat'                        => 'required',
            'process_list'                => ['nullable', 'array'],
            'process_list.*.process_name' => ['nullable', 'string', 'max:255'],
            'process_list.*.allowance'    => ['nullable', 'numeric', 'min:0'],
        ];
    }
    public function filterParameters()
    {
        return $this->except('_token');
    }

}
