<?php

namespace App\Http\Requests\SquidUser;

use App\Models\SquidUser;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(Gate $gate): bool
    {
        // For user routes without user_id parameter, use authenticated user's id
        $userId = $this->route()->parameter('user_id') ?? Auth::user()->id;

        $auth = $gate->allows('create-squid-user', $userId);

        return $auth;
    }

    protected function prepareForValidation()
    {
        if (empty($this->enabled)) {
            $this->merge([
                'enabled'=>0,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user'=>'min:4|required|unique:squid_users',
            'password'=>['required', 'string', 'min:8'],
            'enabled'=>'filled|digits_between:0,1',
            'fullname'=>'nullable',
            'comment'=>'nullable',
            'bandwidth_limit_gb'=>'nullable|numeric|min:0|max:99999999.99',
        ];
    }

    public function createSquidUser() : SquidUser
    {
        $squidUser = new SquidUser($this->validated());
        // For user routes without user_id parameter, use authenticated user's id
        $squidUser->user_id = $this->route()->parameter('user_id') ?? Auth::user()->id;

        return $squidUser;
    }
}
