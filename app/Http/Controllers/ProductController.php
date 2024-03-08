<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function log(Request $request)
    {
        $data = $request->get('data');

        $count = 0;
        foreach ($data as $dat) {
            $count++;
            if ($count > 2) {
                dd();
            }
            return response()->json(['data debug' => $data], 200);
        }

        $rules = [
            '*.api_id' => 'required',
            '*.title' => 'required',
            '*.reference' => 'required',
            '*.images.medium' => 'nullable|url',
            '*.images.thumbnail' => 'nullable|url',
            '*.categories.*.category_id' => 'required',
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
                empty($productData['api_id']) ||
                empty($productData['title']) ||
                empty($productData['reference'])
            ) {
                continue;
            }

            $product = Product::firstOrCreate(
                [
                    'api_id' => $productData['api_id']
                ],
                [
                    'brand_id' => 0,
                    'title' => $productData['title'],
                    'reference' => $productData['reference'],
                    'price' => $productData['price'],
                    'sale_price' => $productData['sale_price'],
                    'rrp_price' => $productData['rrp_price'],
                    'stock' => $productData['stock'],
                    'images' => isset($productData['images']) ? [
                        'medium' => $productData['images']['medium'] ?? null,
                        'thumbnail' => $productData['images']['thumbnail'] ?? null,
                    ] : null,
                ]);

            // check there are categories
            foreach ($productData['categories'] as $category) {
                $product->categories()->firstOrCreate(
                    [
                        'api_id' => $category['category_id'],
                    ],
                    [
                        'name' => $category['name'],
                        'url' => '/',
                        'level' => 0,
                        'cost_per_click' => 0.20,
                        'parent_id' => $category['parent_id']
                    ]
                );
            }

            $brand = Brand::firstOrCreate(
                [
                    'api_id' => $productData['brand']['api_id']
                ],
                [
                    'name' =>  $productData['brand']['name'],
                    'supplier_id' => 0
                ]
            );

            $product->brand_id = $brand->id;
            $product->save();
        }

        return response()->json(['message' => count($data) . ' Products successfully imported!'], 200);

    }

}
