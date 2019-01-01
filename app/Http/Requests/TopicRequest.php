<?php

namespace App\Http\Requests;

class TopicRequest extends Request
{
    /**
     * 表单验证规则
     *
     * @return array
     */
    public function rules()
    {
        switch($this->method())
        {
            // CREATE
            case 'POST':
            // UPDATE
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'title' => 'required|min:2',
                    'body' => 'required|min:3',
                    'category_id' => 'required|numeric',
                ];
            }
            case 'GET':
            case 'DELETE':
            default:
            {
                return [];
            };
        }
    }

    /**
     * 错误信息提示
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.min' => '标题至少为两个字符',
            'body.min' => '文章内容至少为三个字符',
        ];
    }
}
