<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     *  用户授权策略，用于用户更新时的权限验证。
     *
     * @param User $user    要进行授权的用户实例
     * @param User $currenUser    当前登录用户实例
     * @return bool
     */
    public function update(User $currenUser, User $user)
    {
        return $user->id === $currenUser->id;
    }
}
