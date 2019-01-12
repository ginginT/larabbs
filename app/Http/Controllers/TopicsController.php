<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Category;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * 话题列表
     *
     * @param Request $request
     * @param Topic $topic    // 创建一个空白的话题实例
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function index(Request $request, Topic $topic)
	{
		$topics = $topic->withOrder($request->order)->paginate(20);
		return view('topics.index', compact('topics'));
	}

    /**
     * 话题详情页面
     *
     * @param Request $request
     * @param Topic $topic      话题实例
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request,Topic $topic)
    {
        // URL 校正
        if ( ! empty($topic->slug) && $topic->slug != $request->slug) {
            return redirect($topic->link(), 301);
        }

        return view('topics.show', compact('topic'));
    }

    /**
     * 创建话题页面
     *
     * @param Topic $topic    // 创建一个空白的话题实例
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function create(Topic $topic)
	{
	    $categories = Category::all();    // 读取所有分类
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

    /**
     * 处理创建话题
     *
     * @param TopicRequest $request    // 用户话题输入实例
     * @param Topic $topic   // 创建一个空白的话题实例
     * @return \Illuminate\Http\RedirectResponse
     */
	public function store(TopicRequest $request, Topic $topic)
	{
		$topic->fill($request->all());
		$topic->user_id = Auth::id();
		$topic->save();

		return redirect()->to($topic->link())->with('success', '成功创建话题！');
	}

    /**
     * 话题编辑页面
     *
     * @param Topic $topic    话题实例
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
	public function edit(Topic $topic)
	{
	    $this->authorize('update', $topic);
        $categories = Category::all();

		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

    /**
     * 处理话题更新
     *
     * @param TopicRequest $request
     * @param Topic $topic
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->to($topic->link())->with('success', '更新成功！');
	}

    /**
     * 删除话题
     *
     * @param Topic $topic   话题实例
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('success', '删除成功！');
	}

    /**
     * 发布帖子时的图片上传
     *
     * @param Request $request    用户输入实例
     * @param ImageUploadHandler $uploader    图片上传实例类
     * @return array
     */
	public function uploadImage(Request $request, ImageUploadHandler $uploader)
    {
        // 初始化返回数据，默认是失败的
        $data = [
            'success' => false,
            'msg' => '上传失败！',
            'file_path' => '',
        ];

        // 判断是否有上传文件，并赋值给$file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploader->save($file, 'topics', \Auth::id(), 1024);
            // 图片保存成功
            if ($result) {
                $data['success'] = true;
                $data['msg'] = '上传成功！';
                $data['file_path'] = $result['path'];
            }
        }

        return $data;
    }
}