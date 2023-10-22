<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Google_Client;
use Google_Service_Sheets;
use App\Entry;
use App\PaymentProvider\PaypalProvider;
use App\Sheet;

class PaymentController extends Controller
{
    
    public $client;

    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {        
        $entries = Entry::all();
        $total = Entry::sum('amount');
        $sheets = Sheet::all();    

        $data = [
            'entries' => $entries,
            'total' => $total,
            'sheets' => $sheets
        ];

        return view('home', $data);
    }

    public function load(Request $request) {        
        $title = $request->title;
        $emailCol = $request->email_column;
        $amountCol = $request->amount_column;
        $sheet_id = $request->sheet;

        if(!isset($title) || !isset($emailCol) || !isset($amountCol)) {            
            $entries = Entry::all();
            $data = [
                'entries' => $entries
            ];

            return back()->with($data);
        }

        // clear the data
        Entry::truncate();

        $service_account_file = 'keys.json';
        $spreadSheet = Sheet::whereId($sheet_id)->first();

        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);
        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(Google_Service_Sheets::SPREADSHEETS);
        $service = new Google_Service_Sheets($client);

        try {
            $response = $service->spreadsheets_values->get($spreadSheet->sheet_id, $title);
        } catch(\Exception $e) {
            $data = [
                'google_error' => 'Please check Google Sheet Title again'
            ];
            return back()->with($data);
        }        
        
        $values = $response->getValues();

        $emailIndex = ord(strtoupper($emailCol)) - ord('A');
        $amountIndex = ord(strtoupper($amountCol)) - ord('A');

        // echo $emailIndex;
        // echo $amountIndex; exit;

        $entries = [];

        foreach($values as $value) {            
            if(count($value) >= $emailIndex && count($value) >= $amountIndex) {                
                if(!isset($value[$emailIndex]) || !isset($value[$amountIndex])) {
                    continue;
                }
                if (strpos($value[$emailIndex], '@') !== false) {
                    if(isset($value[$emailIndex]) && $value[$emailIndex] != '' && isset($value[$amountIndex]) && $value[$amountIndex] != '') {
                        $item = [
                            'email' => $value[$emailIndex],
                            'amount' => str_replace([',', '$'], '', $value[$amountIndex])
                        ];

                        $new = Entry::create($item);
                        if($new !== null) {
                            $item['id'] = $new->id;
                            $entries[] = $item;
                        }                        
                    }
                }            
            }            
        }

        $total = Entry::sum('amount');

        $data = [
            'entries' => $entries,
            'total' => $total
        ];

        return back()->with($data);
    }

    public function delete(Request $request) {
        $id = $request->id;
        $result = Entry::whereId($id)->delete();

        $data = [
            'entries' => Entry::all(),
            'total' => Entry::sum('amount')
        ];
        
        return back();
    }

    public function sendMoneyToArtist($message) {
        $paymentProvider = new PaypalProvider();
        $status = $paymentProvider->getAccessToken();

        if($status['success'] == false) {
            return response()->json(['result' => 'failed']);
        }

        $accessToken = $status['accessToken'];

        $entries = Entry::all();
        if(count($entries) < 1) {
            return response()->json(['result' => 'no_record']);
        }

        $result = $paymentProvider->payoutProcess($accessToken, $entries, $message);
        if($result['status'] == 'PENDING') {
            Entry::truncate();
            return response()->json(['result' => 'success']);
        } else {
            return response()->json(['result' => 'error']);
        }

        return response()->json(['result' => 'error']);  
    }
}
