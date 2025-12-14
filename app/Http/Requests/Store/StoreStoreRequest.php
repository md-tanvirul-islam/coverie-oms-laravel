<?php

namespace App\Http\Requests\Store;

use App\Enums\AppModelStatus;
use App\Enums\StoreType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'   => ['required', 'string', 'max:255', 'unique:stores,name'],
            'type'   => ['nullable', Rule::in(StoreType::values())],
            'status' => ['required', Rule::in(AppModelStatus::values())],
            'logo'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:1024'],
        ];
    }
}
