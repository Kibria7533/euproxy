<?php

namespace App\Http\Requests\SquidUser;

use App\Models\ProxySubscription;
use App\Models\SquidUser;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateRequest extends FormRequest
{
    public function authorize(Gate $gate): bool
    {
        $userId      = $this->route()->parameter('user_id') ?? Auth::user()->id;
        $proxyTypeId = (int) $this->input('proxy_type_id') ?: null;

        return $gate->allows('create-squid-user', [$userId, $proxyTypeId]);
    }

    protected function prepareForValidation()
    {
        if (empty($this->enabled)) {
            $this->merge(['enabled' => 0]);
        }
    }

    public function rules(): array
    {
        return [
            'proxy_type_id'      => 'required|exists:proxy_types,id',
            'user'               => 'min:4|required|unique:squid_users',
            'password'           => ['required', 'string', 'min:8'],
            'enabled'            => 'filled|digits_between:0,1',
            'fullname'           => 'nullable',
            'comment'            => 'nullable',
            'bandwidth_limit_gb' => 'nullable|numeric|min:0|max:99999999.999',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $newLimit    = (float) ($this->bandwidth_limit_gb ?? 0);
            $proxyTypeId = (int) $this->proxy_type_id;

            if ($newLimit <= 0 || !$proxyTypeId) return;

            $userId = $this->route()->parameter('user_id') ?? Auth::id();

            // Purchased GB for this proxy type only
            $totalPurchased = ProxySubscription::where('user_id', $userId)
                ->where('proxy_type_id', $proxyTypeId)
                ->where('status', 'active')
                ->sum('bandwidth_total_gb');

            // Already assigned to other users of this proxy type
            $totalAssigned = SquidUser::where('user_id', $userId)
                ->where('proxy_type_id', $proxyTypeId)
                ->where('enabled', 1)
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

    public function createSquidUser(): SquidUser
    {
        $squidUser = new SquidUser($this->validated());
        $squidUser->user_id = $this->route()->parameter('user_id') ?? Auth::user()->id;

        return $squidUser;
    }
}
