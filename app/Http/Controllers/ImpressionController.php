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

    public function logMultiple(Request $request)
    {
        $request->validate([
            'impressions' => 'required|array',
            'impressions.*.promotion_id' => 'required|integer',
            'impressions.*.product_id' => 'required|integer',
        ]);

        foreach ($request->impressions as $impressionData) {
            $impression = new Impression();
            $impression->promotion_id = $impressionData['promotion_id'];
            $impression->product_id = $impressionData['product_id'];
            $impression->save();
        }
    }

}
