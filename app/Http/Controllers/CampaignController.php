<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function get(Request $request)
    {
        $campaigns = Campaign::with(['promotions' => function ($query) {
            $query->withTrashed()->with('products');
        }])->withTrashed()->get();

         return response()->json($campaigns);
    }

}
