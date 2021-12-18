<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\Products;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $comment = $request->validate([
            'products_id' => 'required|int',
            'comment' => 'required'
        ]);
        if (!Products::find($comment['products_id'])) {
            return response()->json([
                'msg' => 'Not found'
            ], 404);
        }
        $comment['user_id'] = auth()->user()['id'];
        $comment['commenter'] = auth()->user()['name'];
        return Comments::firstOrCreate($comment);
    }
    public function destroy($id)
    {
        $user = auth()->user();
        $comment = Comments::find($id);
        if (!$comment) {
            return response()->json([
                'msg' => 'Not found'
            ], 404);
        }
        if ($user['name'] != 'admin' && $user['id'] != $comment['user_id']) {
            return response()->json([
                'msg' => 'Unauthorized'
            ], 403);
        }
        return Comments::destroy($id);
    }
}
