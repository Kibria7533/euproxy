<?php

namespace App\Http\Requests\SquidUser;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(Gate $gate): bool
    {
        // For user routes without user_id parameter, use authenticated user's id
        $userId = $this->route()->parameter('user_id') ?? Auth::user()->id;

        $auth = $gate->allows('search-squid-user', $userId);

        return $auth;
    }

    /**
     * Get the user_id for this search request
     */
    public function getUserId(): int
    {
        return $this->route()->parameter('user_id') ?? Auth::user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'per'=>'digits_between:1,100',
        ];
    }

    public function searchSquidUser(): array
    {
        $validated = $this->validated();

        return $validated;
    }
}
