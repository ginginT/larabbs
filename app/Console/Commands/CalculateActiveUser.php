<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CalculateActiveUser extends Command
{
    // 控制台命令，提供调用
    protected $signature = 'larabbs:calculate-active-user';

    // 控制台命令描述
    protected $description = '生成活跃用户';

    // 最终的执行方法
    public function handle(User $user)
    {
        // 在命令行打印一行信息
        $this->info('开始计算');

        $user->calculateAndCacheActiveUsers();

        $this->info('生成成功！');
    }
}
