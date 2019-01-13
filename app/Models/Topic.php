<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body', 'category_id', 'excerpt', 'slug'];

    /**
     * 关联分类，一个话题属于一个分类
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 关联用户，一个话题拥有一个作者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 关联用户回复，一个话题拥有多条回复
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * 使用了本地作用域，本地作用域允许我们定义通用的约束集合以便在应用中复用
     *
     * @param $query    // 查询构建器
     * @param $order    // 排序
     * @return mixed
     */
    public function scopeWithOrder($query, $order)
    {
        // 不同的排序使用不同的数据读取逻辑
        switch ($order) {
            case 'recent':
                $query->latest();   // 框架模型自带 latest() scope，作用是根据最新时间排序
                break;

            default:
                $query->recentReplied();
                break;
        }

        // 预加载防止 N+1 问题
        return $query->with('user', 'category');
    }

    /**
     * 最后回复
     *
     * @param $query
     * @return mixed
     */
    public function scopeRecentReplied($query)
    {
        // 当话题有新回复时，我们将编写逻辑来更新话题模型的 reply_count 属性
        // 此时会自动触发框架对数据模型 updated_at 时间戳的更新
        return $query->orderBy('updated_at', 'desc');
    }

    /**
     * 生成模型 URL
     *
     * @param array $params
     * @return string
     */
    public function link($params = [])
    {
        return route('topics.show', array_merge([$this->id, $this->slug], $params));
    }
}
