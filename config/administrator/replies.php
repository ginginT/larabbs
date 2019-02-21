<?php

use App\Models\Reply;

return [
    'title' => '回复',
    'single' => '回复',
    'model' => Reply::class,

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],

        'content' => [
            'title' => '回复内容',
            'sortable' => false,
            'output' => function ($value, $model) {
                return '<div style="max-width:220px; margin: auto;">' . $value . '</div>';
            }
        ],

        'user' => [
            'title' => '作者',
            'sortable' => false,
            'output' => function ($value, $model) {
                $avatar = $model->user->avatar;
                $value = empty($avatar) ? 'N/A' : '<img src="'.$avatar.'" style="height:22px;width:22px"> ' . $model->user->name;
                return model_link($value, $model->user);
            }
        ],

        'topic' => [
            'title' => '话题',
            'sortable' => false,
            'output' => function ($value, $model) {
                return '<div style="max-width:260px; margin: auto;">' . model_admin_link($model->topic->title, $model->topic) . '</div>';
            }
        ],

        'operation' => [
            'title' => '管理',
            'sortable' => false,
        ],
    ],

    'edit_fields' => [
        'user' => [
            'title' => '用户',
            'type' => 'relationship',
            'name_field' => 'name',

            // 自动补全，对于大数据量的对应关系，推荐开启自动补全，
            // 可防止一次性加载对系统造成负担
            'autocomplete' => true,

            // 自动补全的搜索字段
            'search_fields' => ["CONCAT(id, ' ', name)"],

            // 自动补全排序
            'options_sort_field' => 'id',
        ],

        'topic' => [
            'title' => '话题',
            'type' => 'relationship',
            'name_field' => 'title',
            'autocomplete' => true,
            'search_fields'      => ["CONCAT(id, ' ', title)"],
            'options_sort_field' => 'id',
        ],

        'content' => [
            'title' => '回复内容',
            'type' => 'textarea',
        ],

        'view_count' => [
            'title' => '查看',
        ],
    ],

    'filters' => [
        'user' => [
            'title' => '用户',
            'type' => 'relationship',
            'name_field' => 'name',
            'autocomplete' => true,
            'search_fields'      => array("CONCAT(id, ' ', name)"),
            'options_sort_field' => 'id',
        ],

        'topic' => [
            'title' => '话题',
            'type' => 'relationship',
            'name_field' => 'title',
            'autocomplete' => true,
            'search_fields'      => array("CONCAT(id, ' ', title)"),
            'options_sort_field' => 'id',
        ],

        'content' => [
            'title' => '回复内容',
        ],
    ],

    'rules' => [
        'content' => 'required',
    ],

    'messages' => [
        'content.required' => '请填写回复内容',
    ],
];