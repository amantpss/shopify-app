<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ShopifyAuthController extends Controller
{
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    public function login(Request $request)
    {
        // Redirect users to Shopify OAuth URL for authentication
        $redirectUri = route('callback');

        // Generate a unique state parameter
        $state = uniqid();

        // Store the state parameter in the user's session for validation later
        $request->session()->put('state', $state);
        $shopifyAuthUrl = 'https://aman-demo.myshopify.com/admin/oauth/authorize';
        $queryParams = [
            'client_id' => 'dce2d8d0829bee879c244ff58d953ed3',
            'scope' => 'read_products,write_products', // Adjust scopes as needed
            'redirect_uri' => $redirectUri,
            'state' => $state,
        ];

        return redirect()->away($shopifyAuthUrl . '?' . http_build_query($queryParams));
    }

    public function callback(Request $request)
    {
        // Handle callback from Shopify OAuth
        $code = $request->input('code');

        // Exchange code for access token
        $response = $this->httpClient->post('https://aman-demo.myshopify.com/admin/oauth/access_token', [
            'form_params' => [
                'client_id' => 'dce2d8d0829bee879c244ff58d953ed3',
                'client_secret' => '970a3b49b0a22f3ae47e054e563c1a04',
                'code' => $code,
            ],
        ]);

        $accessToken = json_decode($response->getBody(), true)['access_token'];

        // Store access token in session or database
        session(['access_token' => $accessToken]);

        // Redirect to dashboard or another page
        return redirect()->route('products');
    }

    public function logout()
    {
        // Clear user session and redirect to login page
        session()->forget('access_token');
        return redirect()->route('login');
    }
}
