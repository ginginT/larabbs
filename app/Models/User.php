<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Traits\ActiveUserHelper;
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

    /**
     * 处理已读消息提示
     */
    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    /**
     * Eloquent 修改器 —— 密码加密处理
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        // 如果值的长度等于 60，即认为是已经加密过
        if (strlen($value) != 60) {
            // 不等于 60，做加密处理
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }

    public function setAvatarAttribute($path)
    {
        // 如果不是 'http' 字符串开头的，则为后台上传上来的头像，需要补全 URL
        if ( ! starts_with($path, 'http')) {
            // 拼接完整的 URL
            $path = config('app.url') . "/uploads/images/avatars/$path";
        }

        $this->attributes['avatar'] = $path;
    }
}
