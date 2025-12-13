<?php

namespace App\Http\Requests\SquidAllowedIp;

use App\Models\SquidAllowedIp;
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

        $auth = $gate->allows('create-squid-allowed-ip', $userId);

        return $auth;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ip'=>'required|ip|unique:squid_allowed_ips',
        ];
    }

    public function createSquidAllowedIp() : SquidAllowedIp
    {
        $squidAllowedIp = new SquidAllowedIp($this->validated());
        // For user routes without user_id parameter, use authenticated user's id
        $squidAllowedIp->user_id = $this->route()->parameter('user_id') ?? Auth::user()->id;

        return $squidAllowedIp;
    }
}
