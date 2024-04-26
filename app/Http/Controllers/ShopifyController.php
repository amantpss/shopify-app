<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;

class ShopifyController extends Controller
{
    protected $accessToken; // Your Shopify Access Token
    protected $shopDomain; // Your Shopify Shop Domain
    protected $httpClient;

    public function __construct()
    {
        $this->accessToken = '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';
        $this->shopDomain = 'aman-demo.myshopify.com';
        $this->httpClient = new Client([
            'base_uri' => "https://{$this->shopDomain}/admin/api/2021-10/", // Shopify API base URI
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Shopify-Access-Token' => $this->accessToken,
            ],
        ]);
    }


    public function getProducts()
    {
        try {
            $response = $this->httpClient->get('products.json');
            $data = json_decode($response->getBody(), true)['products'];
            //return response()->json($data['products'],'Products retrived succefully.');
            return view ('shopify.products', array('products' => $data));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getProductDetail($product_id)
    {
        try {
            $response = $this->httpClient->get('products/'.$product_id.'.json');
            $data = json_decode($response->getBody(), true);
            //return response()->json($data['products'],'Products retrived succefully.');
            return view('shopify.productDetail',array('product'=>$data['product']));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function removeProduct($product_id)
    {
        try {
            $response = $this->httpClient->delete('products/'.$product_id.'.json');
            //$data = json_decode($response->getBody(), true);
            //return response()->json($data['products'],'Products retrived succefully.');
            return redirect()->back()->with('success', 'Product has been deleted succefully.');   
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function insertProduct(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'body_html' => 'required|string',
                'vendor' => 'required|string',
                'product_type' => 'required|string',
                'image' => 'required', // Assuming 'image' is a file upload field
                'variants' => 'required',
                'collection_id' => 'required|string',
            ]);
            $validatedData = json_decode('[' . $request->variants . ']', true);
            if ($validator->fails()) {
                //return $this->errorResponse($validator->errors()->first(),'Validation Error', 400);
                return response()->json([
                    'status'=> false,
                    'message' => $validator->errors()->first(),
                    'error' => 'Validation Error',
                ], 400);
            }
            $productData = array(
                    "title" => $request->title,
                    "body_html" => $request->body_html,
                    "vendor" => $request->vendor,
                    "published"    => true ,
                    "collection_id" => $request->collection_id,
            );
            foreach ($validatedData as $variant) {
                $productData['variants'][] = [
                    'option1' => $variant['option1'],
                    'price' => $variant['price'],
                    'sku' => $variant['sku'],
                    'inventory_quantity' => $variant['inventory_quantity'],
                    'inventory_management' => 'shopify',
                ];
            }
            //print_r($productData);die;
            $response = $this->httpClient->post('products.json', [
                'json' => ['product' => $productData],
            ]);
            $data = json_decode($response->getBody(), true);
            $productId = $data['product']['id'];
            foreach ($request->file('image') as $key => $image) {
                $imageData = base64_encode(file_get_contents($image->path()));
                $insertImage = $this->httpClient->post("products/{$productId}/images.json", [
                    'json' => ['image' => ['attachment' => $imageData]],
                ]);
            }
            $assignCollection = $this->httpClient->post("collects.json", [
                'json' => [
                    'collect' => [
                        'product_id' => $productId,
                        'collection_id' => $request->collection_id,
                    ]
                ],
            ]);
            
            $response = $this->httpClient->get('products/'.$productId.'.json');
            $data = json_decode($response->getBody(), true);
            return response()->json($data['product']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
