<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }
    public function store(Request $request)
    {
        if ($request->user()['name'] != 'admin') {
            return response()->json([
                'msg' => 'Unauthorized'
            ], 403);
        }
        $request->validate([
            'name' => 'required'
        ]);
        $entry = $request->all();
        return Category::create($entry);
    }
    public function destroy($id, Request $request)
    {
        if ($request->user()['name'] != 'admin') {
            return response()->json([
                'Unauthorized'
            ], 403);
        }
        return Category::destroy($id);
    }
}
