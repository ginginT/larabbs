<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * 允许用户修改的字段
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'introduction', 'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 关联话题模型
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany    // 一对多关系
     */
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    /**
     * 用户授权
     *
     * @param $model    // 模型
     * @return mixed
     */
    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }
}
