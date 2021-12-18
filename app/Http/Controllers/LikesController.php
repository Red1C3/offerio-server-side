<?php

namespace App\Http\Controllers;

use App\Models\Likes;
use App\Models\Products;
use Illuminate\Http\Request;

class LikesController extends Controller
{
    public function store(Request $request)
    {
        $like = $request->validate([
            'products_id' => 'required|int'
        ]);
        $like['user_id'] = auth()->user()['id'];
        return Likes::firstOrCreate($like);
    }
    public function destroy($id)
    {
        $user = auth()->user();
        $product = Products::find($id);
        if (!$product) {
            return response()->json(
                [
                    'msg' => 'Not found'
                ],
                404
            );
        }
        $like = Likes::where('products_id', $id)->where('user_id', $user['id'])->first();
        return Likes::destroy($like['id']);
    }
}
