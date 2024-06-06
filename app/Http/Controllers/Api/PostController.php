<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index() {
        $posts = Post::with('user.biodata', 'user.photoProfile', 'postUpVotes:id,user_id,post_id', 'comments')->when(request()->search, function ($posts) {
            $posts = $posts->where('content', 'like', '%'. request()->search . '%');
        })->latest()->paginate(5);

        $posts->appends(['search' => request()->search]);

        return new PostResource(true, 'List Data Posts', $posts);
    }

    public function myPosts() {
        $posts = Post::with('user.biodata', 'user.photoProfile', 'postUpVotes:id,user_id,post_id', 'comments')->when(request()->search, function ($posts) {
            $posts = $posts->where('content', 'like', '%'. request()->search . '%');
        })->where('user_id', auth()->user()->id)->latest()->paginate(5);

        $posts->appends(['search' => request()->search]);

        return new PostResource(true, 'List Data My Posts', $posts);
    }

    public function show($id) {
        $post = Post::where('id', $id)->with('user.biodata', 'user.photoProfile', 'postUpVotes:id,user_id,post_id', 'comments')->first();

        if($post) {
            return new PostResource(true, 'List Detail Data Post', $post);
        }
        return response()->json([
            'status' => false,
            'message' => 'Post Not Found',
            'data' => null
        ], 404);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'image' => 'image|mimes:png,jpg,jpeg|max:2000',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $postData = [
            'content' => $request->content,
            'user_id' => auth()->guard('api')->user()->id
        ];

        if($request->file('image')) {
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());
            $postData['image'] = $image->hashname();
        }
        $post = Post::create($postData);

        if($post) {
            return new PostResource(true, 'Post Sucessfull Created!', $post);
        }
        return new PostResource(false, 'Post UnSucessfull Created!', null);
    }

    public function update(Request $request, Post $post) {
        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'image' => 'image|mimes:png,jpg,jpeg|max:2000',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $postData = [
            'content' => $request->content,
            'user_id' => auth()->guard('api')->user()->id,
        ];

        if($request->file('image')) {
            Storage::disk('local')->delete('public/posts/'. basename($post->image));

            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            $postData['image'] = $image->hashName();
        } else {
            if($request->remove_image) {
                Storage::disk('local')->delete('public/posts/'. basename($post->image));
                $postData['image'] = null;
            }
        }

        $post->update($postData);

        if($post) {
            return new PostResource(true, 'Post Sucessfull Updated!', $post);
        }
        return new PostResource(true, 'Post Unsucessfull Updated!', null);
    }

    public function destroy(Post $post) {
        Storage::disk('local')->delete('public/posts/'. basename($post->image));

        if($post->delete()) {
            return new PostResource(true, 'Post Sucessfull Deleted', null);
        }
        return new PostResource(true, 'Post Unsucessfull Deleted', null);


    }
}
