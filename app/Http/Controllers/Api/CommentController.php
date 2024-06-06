<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with('user')->latest()->get();

        return new CommentResource(true, 'List Data Comment', $comments);
    }

    public function show($id)
    {
        $comment = Comment::where('id', $id)->first();

        if ($comment) {
            return new CommentResource(true, 'Detail Data Comment', $comment);
        }

        return response()->json([
            'status' => false,
            'message' => 'Comment Not Found',
            'data' => null
        ], 404);
    }

    public function store(Request  $request)
    {
        $post = Post::where('id', $request->post_id)->first();

        if ($post) {
            $validator = Validator::make($request->all(), [
                'content' => 'required',
                'post_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 422);
            }

            $comment = Comment::create([
                'content' => $request->content,
                'post_id' => $request->post_id,
                'user_id' => auth()->guard('api')->user()->id
            ]);

            if ($comment) {
                return new CommentResource(true, 'Create Comment Successfull', $comment);
            }
            return new CommentResource(false, 'Create Comment Failed', null);
        }

        return response()->json([
            'status' => false,
            'message' => 'Post Not Found',
            'data' => null,
        ]);
    }

    public function update(Request $request, Comment $comment)
    {
        $post = Post::where('id', $request->post_id)->first();

        if ($post) {
            $validator = Validator::make($request->all(), [
                'content' => 'required',
                'post_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 422);
            }

            $comment->update([
                'content' => $request->content,
                'post_id' => $request->post_id,
                'user_id' => auth()->guard('api')->user()->id
            ]);

            if ($comment) {
                return new CommentResource(true, 'Comment Successfull Updated', $comment);
            }
            return new CommentResource(false, 'Comment Failed to Update', null);
        }
        return response()->json([
            'status' => false,
            'message' => 'Post not found',
            'data'  => null
        ], 404);
    }

    public function destroy(Comment $comment) {
        if($comment->delete()) {
            return new CommentResource(true, 'Comment Sucessfull Deleted', null);
        }
        return new CommentResource(true, 'Comment Failed to Delete', null);
    }
}
