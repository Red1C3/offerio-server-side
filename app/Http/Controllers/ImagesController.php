<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use function PHPUnit\Framework\fileExists;

class ImagesController extends Controller
{
    /**
     * return an image file from the provided imgName if existing
     * otherwise return 404
     *
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function getImage($id)
    {
        if (file_exists(storage_path() . '/app/images/' . $id))
            return response()->file(storage_path() . '/app/images/' . $id);
        else
            return response()->json([
                'msg' => 'Not found'
            ], 404);
    }
}
