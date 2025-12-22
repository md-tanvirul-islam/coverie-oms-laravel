<?php

namespace App\Http\Requests\Store;

use App\Enums\AppModelStatus;
use App\Enums\StoreType;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
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
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer', Rule::exists('users', 'id')->where(function (Builder $query) {
                $user = Auth::user();
                $query->where('team_id', $user->team_id);
            })],
            'full_data_ar' => ['required', 'array'],
            'full_data_ar.*' => ['boolean'],
        ];
    }
}
