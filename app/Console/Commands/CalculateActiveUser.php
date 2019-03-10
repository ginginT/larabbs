<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CalculateActiveUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * 控制台命令，提供调用
     *
     * @var string
     */
    protected $signature = 'larabbs:calculate-active-user';

    /**
     * The console command description.
     *
     * 控制台命令描述
     *
     * @var string
     */
    protected $description = '生成活跃用户';

    /**
     * Execute the console command.
     *
     * 最终的执行方法
     *
     * @return mixed
     */
    public function handle(User $user)
    {
        // 在命令行打印一行信息
        $this->info('开始计算');

        $user->calculateAndCacheActiveUsers();

        $this->info('生成成功！');
    }
}
