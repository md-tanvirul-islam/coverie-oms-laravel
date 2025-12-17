<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class FilterTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
                        'name' => 'string|nullable',
            'status' => 'boolean|nullable',
            'created_by' => 'integer|nullable',
            'updated_by' => 'integer|nullable',
        ];
    }
}
