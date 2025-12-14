<?php

namespace App\Http\Requests\Store;

use App\Enums\AppModelStatus;
use App\Enums\StoreType;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $store_types = implode(',', StoreType::values());
        $status = implode(',', AppModelStatus::values());

        return [
            'name' => 'nullable|string|max:255|unique:stores,name,' . $this->store->id,
            'type' => "nullable|string|max:100|in:{$store_types}",
            'status' => "nullable|boolean|in:{$status}",
            'logo'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:1024'],
        ];
    }
}
