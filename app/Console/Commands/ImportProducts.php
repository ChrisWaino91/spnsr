<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;


class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from the Visualsoft API connection into the application.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Importing products...');

        // create an array of array of products containing brand, category, and sub-products

        $data = [];

        $products = DB::table('products_parents')
            ->where([
                'active' => 'Y',
                'deleted' => 'N',
            ])
            ->get();

        foreach ($products as $product) {

            $brand = DB::table('products_manufacturers')
                    ->where('manufacturer_id', $product->manufacturer_id)
                    ->first();

            if (empty($brand)) {
                continue;
            }

            $categories = DB::table('products_to_categories as ptc')
                ->join('products_categories as pc', 'ptc.category_id', '=', 'pc.category_id')
                ->where('ptc.parent_product_id', $product->parent_product_id)
                ->select('pc.category_id', 'pc.name', 'pc.parent_id')
                ->get();

            $subProducts = DB::table('products_vs')
                ->where([
                    'parent_product_id' => $product->parent_product_id,
                    'active' => 'Y',
                    'deleted' => 'N',
                ])
                ->get();

            $productImportData[$product->parent_product_id] = [
                'reference' => $product->reference,
                'api_id' => $product->parent_product_id,
                'title' => $product->title,
                'price' => $product->price_inc_high,
                'sale_price' => $product->price_sale_inc_high,
                'rrp_price' => $product->price_rrp_inc_high,
                'stock' => $product->total_stock,

                'brand' => [
                    'name' => $brand->name,
                    'api_id' => $brand->manufacturer_id,
                ],

                'categories' => $categories,

            ];

        }

        $token = '1|iqGe4EcuXIr2amaX4pT0U2O01bmctqxtsP1r1plP1c7f0301';

        $client = new Client([
            'base_uri' => 'http://127.0.0.1:8000',
            'timeout'  => 30.0,
        ]);

        $chunks = array_chunk($productImportData, 500);

        foreach ($chunks as $chunk) {

            $payload = [
                'data' => $chunk
            ];

            try {
                $response = $client->request('POST', '/api/products', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'json' => $payload,
                ]);

                $body = $response->getBody();
                $content = $body->getContents();

            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                echo $e->getMessage();
            }
        }

    }

}
