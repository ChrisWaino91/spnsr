<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function log(Request $request)
    {
        $data = $request->get('data');

        $rules = [
            'data.*.product.api_id' => 'required',
            'data.*.product.title' => 'required',
            'data.*.product.reference' => 'required',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        if (empty($data)) {
            return response()->json(['message' => 'No data provided. Please check payload.'], 400);
        }

        foreach ($data as $productData) {

            if (
                empty($productData['product']['api_id']) ||
                empty($productData['product']['title']) ||
                empty($productData['product']['reference']) ||
                empty($productData['categories'])
            ) {
                continue;
            }

            $product = Product::firstOrCreate(
                [
                    'api_id' => $productData['product']['api_id']
                ],
                [
                    'brand_id' => 0,
                    'title' => $productData['product']['title'],
                    'reference' => $productData['product']['reference'],
                    'price' => $productData['product']['price'],
                    'sale_price' => $productData['product']['sale_price'],
                    'rrp_price' => $productData['product']['rrp_price'],
                    'stock' => $productData['product']['stock'],
                ]);

            if (!empty($productData['product']['images'])) {
                $product->images = $productData['product']['images'];
                $product->save();
            }

            if (!empty($productData['product']['url'])) {
                $product->url = $productData['product']['url'];
                $product->save();
            }

            foreach ($productData['categories'] as $category) {
                $product->categories()->firstOrCreate(
                    [
                        'api_id' => $category['id'],
                    ],
                    [
                        'name' => $category['name'],
                        'url' => '/',
                        'level' => 0,
                        'promotion_id' => 0,
                        'cost_per_click' => 0.20,
                        'parent_id' => $category['parent_id']
                    ]
                );
            }

            if (
                !empty($productData['brand']) &&
                !empty($productData['brand']['id'])
             ) {
                $brand = Brand::firstOrCreate(
                    [
                        'api_id' => $productData['brand']['id']
                    ],
                    [
                        'name' =>  $productData['brand']['name'],
                        'supplier_id' => 0
                    ]
                );

                $product->brand_id = $brand->id;
                $product->save();
            }
        }

        return response()->json(['message' => count($data) . ' Products successfully imported!'], 200);

    }

}
