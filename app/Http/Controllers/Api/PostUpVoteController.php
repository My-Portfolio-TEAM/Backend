<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostUpVoteResource;
use App\Models\Post;
use App\Models\PostUpVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostUpVoteController extends Controller
{
    public function store($id)
    {
        $post = Post::find($id);

        if ($post) {
            $userId = auth()->guard('api')->user()->id;

            $existingVote = PostUpVote::where('post_id', $id)->where('user_id', $userId)->first();

            if ($existingVote) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have already voted for this post',
                    'data' => null
                ], 400);
            }

            $vote = PostUpVote::create([
                'post_id' => (int) $id,
                'user_id' => $userId
            ]);

            if ($vote) {
                return new PostUpVoteResource(true, 'Up Vote Success', $vote);
            }
            return new PostUpVoteResource(false, 'Up Vote failed', null);
        }

        return response()->json([
            'status' => false,
            'message' => 'Post Not Found',
            'data' => null
        ], 404);
    }


    public function destroy(PostUpVote $id)
    {
        if ($id->delete()) {
            return new PostUpVoteResource(true, 'Up Vote Deleted', null);
        }
        return new PostUpVoteResource(true, 'Up Vote Unsuccess Deleted', null);
    }
}
