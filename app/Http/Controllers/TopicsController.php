<?php

namespace App\Http\Controllers;

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
     * @param Topic $topic    // 话题实例
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Topic $topic)
    {
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

		return redirect()->route('topics.show', $topic->id)->with('message', '话题创建成功！');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
		return view('topics.create_and_edit', compact('topic'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->route('topics.show', $topic->id)->with('message', 'Updated successfully.');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('message', 'Deleted successfully.');
	}
}