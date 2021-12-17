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
            return response('unauthorized', 403);
        }
        $request->validate([
            'name' => 'required'
        ]);
        $entry = $request->all();
        return Category::create($entry);
    }
}
