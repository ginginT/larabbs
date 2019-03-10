<?php

namespace App\Models\Traits;

use Redis;
use Carbon\Carbon;

trait LastActivedAtHelper
{
    /**
     * 哈希表名称前缀
     *
     * @var string
     */
    protected $hash_prefix = 'larabbs_last_actived_at_';

    /**
     * 字段前缀
     *
     * @var string
     */
    protected $field_prefix = 'user_';

    /**
     * 记录用户最后登录时间到 Redis
     */
    public function recordLastActivedAt()
    {
        // 获取今天的日期
        $date = Carbon::now()->toDateString();

        // Redis 哈希表的命名，如：larabbs_last_actived_at_2019-03-12
        $hash = $this->getHashFromDateString($date);

        // 字段名称，如 user_1
        $field = $this->getHashField();

//        dd(Redis::hGetAll($hash));

        // 当前时间，如：2019-03-12 15：45：30
        $now = Carbon::now()->toDateTimeString();

        // 数据写入 Redis ，字段已存在会被更新
        Redis::hSet($hash, $field, $now);
    }

    /**
     * 将用户最后登录时间从 Redis 同步到数据库
     */
    public function syncUserActivedAt()
    {
        // 获取昨天的日期，格式如：2017-10-21
        $yesterday_date = Carbon::yesterday()->toDateString();

        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        $hash = $this->getHashFromDateString($yesterday_date);

        // 从 Redis 中获取哈希表里所有的数据
        $datas = Redis::hGetAll($hash);

        // 遍历，并同步到数据库中
        foreach ($datas as $user_id => $actived_at) {
            // 将 user_id 转化为 1
            $user_id = str_replace($this->field_prefix, '', $user_id);

            // 只有用户存在时才将数据更新到数据库中
            if ($user = $this->find($user_id)) {
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }

        // 数据更新完毕，即可删除 Redis 中哈希表
        Redis::del($hash);
    }

    /**
     * 获取用户最后登录时间，用于显示在页面
     *
     * @param $value
     * @return Carbon
     */
    public function getLastActivedAtAttribute($value)
    {
        // 获取今天日期
        $date = Carbon::now()->toDateString();

        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        $hash = $this->getHashFromDateString($date);

        // 字段名称，如：user_1
        $field = $this->getHashField();

        // 三元运算符，优先选择 Redis 的数据，否则使用数据库中
        $datetime = Redis::hGet($hash, $field) ? : $value;

        // 如果存在的话 ，返回时间对应的 Carbon 实体
        if ($datetime) {
            return new Carbon($datetime);
        } else {
            // 否则使用用户注册时间
            return $this->created_at;
        }
    }

    /**
     * 定义哈希表名称
     *
     * @param $date
     * @return string
     */
    public function getHashFromDateString($date)
    {
        return $this->hash_prefix . $date;
    }

    /**
     * 定义哈希表字段
     *
     * @return string
     */
    public function getHashField()
    {
        return $this->field_prefix . $this->id;
    }
}