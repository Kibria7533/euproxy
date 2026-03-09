<?php

namespace App\Http\Requests\SquidUser;

use App\Models\ProxySubscription;
use App\Models\SquidUser;
use App\Services\SquidUserService;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Http\FormRequest;

class ModifyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(Gate $gate, SquidUserService $squidUserService): bool
    {
        $auth = $gate->allows('modify-squid-user',
            $squidUserService->getById($this->route()->parameter('id'))
        );

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
            'user'=>'min:4|filled|unique:squid_users,user,'.$this->route()->parameter('id').',id',
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
            $newLimit    = (float) ($this->bandwidth_limit_gb ?? 0);
            $squidUserId = $this->route()->parameter('id');
            $squidUser   = SquidUser::find($squidUserId);

            if ($newLimit <= 0 || !$squidUser) return;

            $userId      = $squidUser->user_id;
            $proxyTypeId = $squidUser->proxy_type_id;

            // Purchased GB for this proxy type only
            $totalPurchased = ProxySubscription::where('user_id', $userId)
                ->where('proxy_type_id', $proxyTypeId)
                ->where('status', 'active')
                ->sum('bandwidth_total_gb');

            // Already assigned to other users of this proxy type (excluding this user)
            $totalAssigned = SquidUser::where('user_id', $userId)
                ->where('proxy_type_id', $proxyTypeId)
                ->where('enabled', 1)
                ->where('id', '!=', $squidUserId)
                ->sum('bandwidth_limit_gb');

            if (($totalAssigned + $newLimit) > $totalPurchased) {
                $available = max(0, $totalPurchased - $totalAssigned);
                $validator->errors()->add(
                    'bandwidth_limit_gb',
                    "Exceeds available bandwidth for this proxy type. You have " . number_format($available, 3) . " GB remaining."
                );
            }
        });
    }

    public function modifySquidUser() : SquidUser
    {
        $squidUser = new SquidUser($this->validated());
        $squidUser->id = $this->route()->parameter('id');

        return $squidUser;
    }
}
