<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Products;
use App\Models\User;
use App\Providers\AppServiceProvider;
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
                $this->serverDestroy($value['id'], true);
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
            'description' => 'required',
            'category_id' => 'required|int',
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
        if (!Category::find($entry['category_id'])) {
            return response()->json([
                'msg' => 'Category does not exist'
            ], 400);
        }
        if (
            $entry['price-1'] <= $entry['price-2'] ||
            $entry['price-2'] <= $entry['price-3'] ||
            $entry['price-3'] <= $entry['price-4']
        )
            return response()->json([
                'msg' => 'Invalid prices'
            ], 400);
        $timestamps[0] = strtotime($entry['timestamp-1']);
        $timestamps[1] = strtotime($entry['timestamp-2']);
        $timestamps[2] = strtotime($entry['timestamp-3']);
        $timestamps[3] = strtotime($entry['timestamp-4']);
        for ($i = 0; $i < 3; $i++)
            if ($timestamps[$i + 1] <= $timestamps[$i])
                return response()->json([
                    'msg' => 'Invalid timestamps'
                ], 400);
        $imageName = time() . '.' . $request->image->extension();
        $request->image->storeAs('images', $imageName);
        unset($entry['image']);
        $entry['imgName'] = $imageName;
        $entry['user_id'] = $request->user()['id'];
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
        $user = auth('sanctum')->user();
        $product = Products::find($id);
        if (
            $user != null
            && ($product['user_id'] == $user['id']
                || $user['name'] == 'admin')
        ) {
            $product['isOwner'] = true;
        } else {
            $product['isOwner'] = false;
        }
        return response()->json($product, 200);
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
    public function destroy($id, Request $request)
    {
        if (
            Products::find($id)['user_id'] != $request->user()['id'] &&
            $request->user()['name'] != 'admin'
        ) {
            return response()->json([
                'msg' => 'Unauthorized'
            ], 403);
        }
        $imgPath = storage_path() . '/app/images/' . Products::find($id)['imgName'];
        unlink($imgPath);
        return Products::destroy($id);
    }
    public function serverDestroy($id)
    {
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
            return response()->json([
                'msg' => 'Missing fields'
            ], 400);
        if (!($field == 'name' ||
            $field == 'category_id' ||
            $field == 'timestamp-4'))
            return response()->json([
                'msg' => 'Invalid searching field'
            ], 400);
        if ($field != 'category_id')
            $items = Products::where($field, 'like', '%' . $searchedItem . '%')->get();
        else {
            $items = Category::find($searchedItem)->products;
        }
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
