<?php

namespace App\Http\Controllers;

use App\Models\Impression;
use Illuminate\Http\Request;

class ImpressionController extends Controller
{
    public function log(Request $request)
    {
        $request->validate([
            'promotion_id' => 'required|integer',
            'product_id' => 'required|integer',
        ]);

        $impression = new Impression();
        $impression->fill($request->all());
        $impression->save();
    }

}
