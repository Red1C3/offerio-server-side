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
        $products = Products::all();
        foreach ($products as $key => $value) {
            $expirationDate = strtotime($value['timestamp-4']);
            if (time() >= $expirationDate) {
                $this->destroy($value['id'], true);
                unset($products[$key]);
                continue;
            }
            unset($value['owner']);
            unset($value['category']);
            unset($value['contact']);
            unset($value['amount']);
            $price = 0;
            if (time() >= strtotime($value['timestamp-3']))
                $price = $value['price-4'];
            elseif (time() >= strtotime($value['timestamp-2']))
                $price = $value['price-3'];
            elseif (time() >= strtotime($value['timestamp-1']))
                $price = $value['price-2'];
            else
                $price = $value['price-1'];
            $value['price'] = $price;
            unset($value['price-1']);
            unset($value['price-2']);
            unset($value['price-3']);
            unset($value['price-4']);
            unset($value['timestamp-1']);
            unset($value['timestamp-2']);
            unset($value['timestamp-3']);
            unset($value['timestamp-4']);
            unset($value['created_at']);
            unset($value['updated_at']);
        }
        return $products;
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
            'image' => 'required|image|mimes:png,jpg,jpeg',
            'name' => 'required',
            'category' => 'required',
            'contact' => 'required',
            'amount' => 'required',
            'timestamp-1' => 'required',
            'timestamp-2' => 'required',
            'timestamp-3' => 'required',
            'timestamp-4' => 'required',
            'price-1' => 'required',
            'price-2' => 'required',
            'price-3' => 'required',
            'price-4' => 'required',
        ]);
        $entry = $request->all();
        if (
            $entry['price-1'] <= $entry['price-2'] ||
            $entry['price-2'] <= $entry['price-3'] ||
            $entry['price-3'] <= $entry['price-4']
        )
            return response('Invalid prices', 400);
        $timestamps[0] = strtotime($entry['timestamp-1']);
        $timestamps[1] = strtotime($entry['timestamp-2']);
        $timestamps[2] = strtotime($entry['timestamp-3']);
        $timestamps[3] = strtotime($entry['timestamp-4']);
        for ($i = 0; $i < 3; $i++)
            if ($timestamps[$i + 1] <= $timestamps[$i])
                return response('Invalid timestamps', 400);
        $imageName = time() . '.' . $request->image->extension();
        $request->image->storeAs('images', $imageName);
        unset($entry['image']);
        $entry['imgName'] = $imageName;
        $entry['owner'] = 'admin@admin'; //TODO obtain from user
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
        $product = Products::find($id);
        unset($product['owner']);
        return $product;
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
     * @param boolean $isServer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $isServer = false)
    {
        if ($isServer == false) {
            //TODO check if user is authorized to delete this product
            return response('Forbidden', 403);
        }
        $imgPath = storage_path() . '/app/images/' . Products::find($id)['imgName'];
        unlink($imgPath);
        return Products::destroy($id);
    }
}
