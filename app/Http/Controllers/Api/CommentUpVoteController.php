<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentUpVoteResource;
use App\Models\Comment;
use App\Models\CommentUpVote;
use Illuminate\Http\Request;

class CommentUpVoteController extends Controller
{
    public function store($id)
    {
        $post = Comment::find($id);

        if ($post) {
            $userId = auth()->guard('api')->user()->id;

            $existingVote = CommentUpVote::where('comment_id', $id)->where('user_id', $userId)->first();

            if ($existingVote) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have already voted for this comment',
                    'data' => null
                ], 400);
            }

            $vote = CommentUpVote::create([
                'comment_id' => (int) $id,
                'user_id' => $userId
            ]);

            if ($vote) {
                return new CommentUpVoteResource(true, 'Up Vote Success', $vote);
            }
            return new CommentUpVoteResource(false, 'Up Vote failed', null);
        }

        return response()->json([
            'status' => false,
            'message' => 'Comment Not Found',
            'data' => null
        ], 404);
    }


    public function destroy(CommentUpVote $id)
    {
        if ($id->delete()) {
            return new CommentUpVoteResource(true, 'Up Vote Deleted', null);
        }
        return new CommentUpVoteResource(true, 'Up Vote Unsuccess Deleted', null);
    }
}
