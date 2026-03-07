<?php

namespace App\UseCases\SquidUser;

use App\Models\SquidAllowedIp;
use App\Models\SquidUser;
use Illuminate\Support\Facades\DB;
use function assert;

class ModifyAction
{
    public function __invoke(SquidUser $squidUser): SquidUser
    {
        $modifyUser = SquidUser::query()->where('id', '=', $squidUser->id)->first();
        assert($modifyUser->exists);

        $oldQuotaBytes = (int) $modifyUser->quota_bytes;
        $oldIsBlocked  = (int) $modifyUser->is_blocked;
        $oldUsedBytes  = (int) $modifyUser->used_bytes;
        $userId        = $modifyUser->user_id;

        $modifyUser->setRawAttributes(array_merge($modifyUser->getAttributes(), $squidUser->getAttributes()));
        $modifyUser->save();

        $newQuotaBytes = (int) $modifyUser->quota_bytes;

        // If limit was increased beyond current usage and user was blocked → unblock
        if ($oldIsBlocked && $newQuotaBytes > $oldUsedBytes) {
            DB::table('squid_users')->where('id', $modifyUser->id)->update(['is_blocked' => 0]);

            // Remove iptables block rules for all IPs belonging to this user
            $ips = SquidAllowedIp::withoutGlobalScopes()->where('user_id', $userId)->pluck('ip');
            foreach ($ips as $ip) {
                exec('iptables -D INPUT -s ' . escapeshellarg($ip) . ' -p tcp --dport 3128 -j REJECT --reject-with tcp-reset 2>/dev/null');
            }
        }

        return $modifyUser;
    }
}
