<?php

namespace App\Http\Requests\SquidUser;

use App\Models\ProxySubscription;
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
            'bandwidth_limit_gb'=>'nullable|numeric|min:0|max:99999999.999',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $newLimit = (float) ($this->bandwidth_limit_gb ?? 0);
            if ($newLimit <= 0) return;

            $userId = $this->route()->parameter('user_id') ?? Auth::id();

            $totalPurchased = ProxySubscription::where('user_id', $userId)
                ->where('status', 'active')
                ->sum('bandwidth_total_gb');

            $totalAssigned = SquidUser::where('user_id', $userId)
                ->where('enabled', 1)
                ->sum('bandwidth_limit_gb');

            if (($totalAssigned + $newLimit) > $totalPurchased) {
                $available = max(0, $totalPurchased - $totalAssigned);
                $validator->errors()->add(
                    'bandwidth_limit_gb',
                    "Bandwidth limit exceeds your available quota. You have " . number_format($available, 3) . " GB remaining to assign."
                );
            }
        });
    }

    public function createSquidUser() : SquidUser
    {
        $squidUser = new SquidUser($this->validated());
        // For user routes without user_id parameter, use authenticated user's id
        $squidUser->user_id = $this->route()->parameter('user_id') ?? Auth::user()->id;

        return $squidUser;
    }
}
