<?php

namespace App\PaymentProvider;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;


class PaypalProvider
{
    public $environment;
    public $client;
    protected $base_url;
    protected $client_id;
    protected $client_secret;

    const PAY_OUT_ENDPOINT = 'v1/payments/payouts';
    const TOKEN_ENDPOINT = 'v1/oauth2/token';

    public function __construct() {
        $this->client = new Client();        
        if(config('payment.environment') == 'sandbox') {
            $this->base_url = 'https://api.sandbox.paypal.com/';
            $this->client_id = config('payment.sandbox_client_id');
            $this->client_secret = config('payment.sandbox_client_secret');
        } else {
            $this->base_url = 'https://api.paypal.com/';
            $this->client_id = config('payment.live_client_id');
            $this->client_secret = config('payment.live_client_secret');
        }
        
    }

    public function payoutProcess($accessToken, $entries, $message) {
        //Response init
        $status = [
            'success' => false,
            'error' => false,
            'message' => ''
        ];

        try {
            $items = [];

            foreach($entries as $index => $entry) {
                $items[] = [
                    "recipient_type" => "EMAIL",
                    "amount" => [
                        "value" => $entry->amount,
                        "currency" => "USD"
                    ],
                    "note" => "Thanks for your patronage!",
                    "sender_item_id" => time().'-'.$index,
                    "receiver" => $entry->email,
                ];
            }

            // Request Data for Payout
            $requestData = [
                "sender_batch_header" => [
                    "sender_batch_id" => time().'1029',
                    "email_subject" => $message,
                    "email_message" => "You have received a payout! Thanks for using our service!"
                ],
                "items" => $items
            ];

            // Request for payout
            $response = $this->client->post($this->base_url . self::PAY_OUT_ENDPOINT, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization'=>'Bearer '.$accessToken
                ],
                RequestOptions::JSON => $requestData
            ]);

            $responseJson = $response->getBody()->getContents();
            $responseObj = json_decode($responseJson);            
            
            $status['success'] = true;
            $status['status'] = $responseObj->batch_header->batch_status;
            return $status;

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $status['error'] = $e->getMessage();      
            return $status;
        }   
    }

	/**
     * Attempts to retrieve access token from Paypal
     *
     * @throws \Exception
     * @return string
     */
    public function getAccessToken()
    {
        $status =[
            'success' => false,            
        ];

        try {
            $response = $this->client->request('POST', $this->base_url . self::TOKEN_ENDPOINT, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic ' . base64_encode($this->client_id . ':' . $this->client_secret)
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials'
                ]
            ]);

            $responseJson = $response->getBody()->getContents();
            $responseObject = json_decode($responseJson);

            $status['accessToken'] = $responseObject->access_token;
            $status['success'] = true;
            
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            var_dump($e->getMessage()); exit;
            $status['error'] = $e->getMessage();
        }

        return $status;
    }
}