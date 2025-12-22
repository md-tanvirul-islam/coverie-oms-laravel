<?php

namespace App\Http\Requests\Store;

use App\Enums\AppModelStatus;
use App\Enums\StoreType;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer', Rule::exists('users', 'id')->where(function (Builder $query) {
                $user = Auth::user();
                $query->where('team_id', $user->team_id);
            })],
            'full_data' => ['boolean', 'required']
        ];
    }
}
