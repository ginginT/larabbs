<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable {
        notify as protected laravelNotify;
    }

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
     * 关联回复模型，一个用户拥有多条回复
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
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

    /**
     * 处理通知数量
     *
     * @param $instance
     */
    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了
        if ($this->id == Auth::id()) {
            return;
        }

        $this->increment('notification_count');
        $this->laravelNotify($instance);
    }


    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }
}
