<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReplyCommentResource;
use App\Models\Comment;
use App\Models\ReplyComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReplyCommentController extends Controller
{
    public function index()
    {
        $replyComments = ReplyComment::with('user', 'comment')->latest()->get();

        return new ReplyCommentResource(true, 'List Data Reply Comment', $replyComments);
    }

    public function show($id)
    {
        $replyComment = ReplyComment::where('id', $id)->first();

        if ($replyComment) {
            return new ReplyCommentResource(true, 'Detail Data Reply Comment', $replyComment);
        }

        return response()->json([
            'status' => false,
            'message' => 'Reply Comment Not Found',
            'data' => null
        ], 404);
    }

    public function store(Request  $request)
    {
        $comment = Comment::where('id', $request->comment_id)->first();

        if ($comment) {
            $validator = Validator::make($request->all(), [
                'content' => 'required',
                'comment_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 422);
            }

            $replyComment = ReplyComment::create([
                'content' => $request->content,
                'comment_id' => $request->comment_id,
                'user_id' => auth()->guard('api')->user()->id
            ]);

            if ($replyComment) {
                return new ReplyCommentResource(true, 'Create Reply Comment Successfull', $replyComment);
            }
            return new ReplyCommentResource(false, 'Create Reply Comment Failed', null);
        }

        return response()->json([
            'status' => false,
            'message' => 'Comment Not Found',
            'data' => null,
        ]);
    }

    public function update(Request $request, ReplyComment $reply_comment)
    {
        $comment = Comment::where('id', $request->comment_id)->first();

        if ($comment) {
            $validator = Validator::make($request->all(), [
                'content' => 'required',
                'comment_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 422);
            }

            $reply_comment->update([
                'content' => $request->content,
                'comment_id' => $request->comment_id,
                'user_id' => auth()->guard('api')->user()->id
            ]);

            if ($reply_comment) {
                return new ReplyCommentResource(true, 'Reply Comment Successfull Updated', $reply_comment);
            }
            return new ReplyCommentResource(false, 'Reply Comment Failed to Update', null);
        }
        return response()->json([
            'status' => false,
            'message' => 'Comment not found',
            'data'  => null
        ], 404);
    }

    public function destroy(ReplyComment $reply_comment) {
        if($reply_comment->delete()) {
            return new ReplyCommentResource(true, 'Reply Comment Sucessfull Deleted', null);
        }
        return new ReplyCommentResource(true, 'Reply Comment Failed to Delete', null);
    }
}
