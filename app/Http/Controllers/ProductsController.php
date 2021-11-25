<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Return all products with all their fields
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Products::all();
    }

    /**
     * Create a new product and store its image in 
     * /storage/app/images
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg' //TODO validate all fields
        ]);
        $imageName = time() . '.' . $request->image->extension();
        $request->image->storeAs('images', $imageName);
        $entry=$request->all();
        unset($entry['image']);
        $entry['imgName']=$imageName;
        return Products::create($entry);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
