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
            $this->stripItemDeep($value);
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
        return Products::find($id);
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
    /**
     * Search for a product satisfying partially, the name, category or expiration date
     * 
     * @param Request $request
     * @return Response
     */
    public function search(Request $request)
    {
        $field = $request->input('searchBy');
        $searchedItem = $request->input('keyword');
        if (!$searchedItem || !$field)
            return response('Missing fields', 400);
        if (!($field == 'name' ||
            $field == 'category' ||
            $field == 'timestamp-4'))
            return response('Invalid searching field', 400);
        $items = Products::where($field, 'like', '%' . $searchedItem . '%')->get();
        if (count($items) == 0)
            return response()->json([
                'msg' => 'No item was found',
                'num' => 0,
                'items' => null
            ]);
        foreach ($items as $key => $value) {
            $this->stripItemDeep($value);
        }
        return response()->json([
            'msg' => 'Items were found',
            'num' => count($items),
            'items' => $items
        ]);
    }
    /**
     * Remove details fields from a product
     * 
     * @param mixed $item should be a product
     */
    private function stripItemDeep(mixed $item)
    {
        unset($item['category']);
        unset($item['contact']);
        unset($item['amount']);
        $price = 0;
        if (time() >= strtotime($item['timestamp-3']))
            $price = $item['price-4'];
        elseif (time() >= strtotime($item['timestamp-2']))
            $price = $item['price-3'];
        elseif (time() >= strtotime($item['timestamp-1']))
            $price = $item['price-2'];
        else
            $price = $item['price-1'];
        $item['price'] = $price;
        unset($item['price-1']);
        unset($item['price-2']);
        unset($item['price-3']);
        unset($item['price-4']);
        unset($item['timestamp-1']);
        unset($item['timestamp-2']);
        unset($item['timestamp-3']);
        unset($item['timestamp-4']);
    }
}
