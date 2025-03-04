<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\FeeSetup;
use App\Models\Registration;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function feeSetUpIndex()
    {
        $authUser = Auth::user();
        $fees = FeeSetup::all();
        return view('feesetup.index', compact('fees', 'authUser'));
    }
    public function feeSetUpCreate()
    {
        $authUser = Auth::user();
        return view('feesetup.create', compact('authUser'));
    }
    public function feeSetUpStore(Request $request)
    {
       $validator = $request->validate([
            'name' => 'required|unique:fee_setups,name',
            'amount' => 'required',
        ]);

        FeeSetup::create([
            'name' => $request->name,
            'amount' => $request->amount,
        ]);
        $notification = array(
            'message' => 'Fee Setup Created Successfully',
            'alert-type' => 'success'
        );
        Log::info('Fee Setup Created Successfully by ' . Auth::user()->name);
        return redirect()->route('feesetup.index')->with($notification);
    }
    public function feeSetUpEdit(FeeSetup $fee)
    {
        return view('feesetup.edit', compact('fee'));
    }
    public function feeSetUpUpdate(Request $request, FeeSetup $fee)
    {
        $request->validate([
            'name' => 'required',
            'amount' => 'required',
        ]);

        $fee->update([
            'name' => $request->name,
            'amount' => $request->amount,
        ]);
        $notification = array(
            'message' => 'Fee Setup Updated Successfully',
            'alert-type' => 'success'
        );
        Log::info('Fee Setup Updated Successfully by ' . Auth::user()->name);
        return redirect()->route('feesetup.index')->with($notification);
    }
    public function feeSetUpDestroy(FeeSetup $fee)
    {
        $fee->delete();
        $notification = array(
            'message' => 'Fee Setup Deleted Successfully',
            'alert-type' => 'success'
        );
        Log::info('Fee Setup Deleted Successfully by ' . Auth::user()->name);
        return redirect()->route('feesetup.index')->with($notification);
    }

    public function transactions()
    {
        $authUser = Auth::user();
        $transactions = Transaction::all();
        return view('transactions.index', compact('transactions', 'authUser'));
    }

    public function index(Faculty $faculty)
    {
        $faculties = Faculty::all();
        $departments = Department::all();
        $fees = FeeSetup::all();
     
        return view('pages.pay', compact('faculties', 'departments', 'fees'));
    }

    public function initialize(Request $request)
    {

        //This generates a payment reference
        $reference = 'BSU-CSL2024' . substr(rand(0000, time()), 0, 8);
        // Flutterwave::generateReference();

        Registrations::create([
            'email' => request()->email,
            "phone_number" => request()->phone,
            "name" => request()->name,
            "reg_number" => request()->reg_number,
            "tx_ref" => $reference,
            "faculty" => request()->faculty,
            "department" => request()->department,
            
        ]);
        
        

        // Enter the details of the payment
        $data = [
            'payment_options' => "card,banktransfer, ussd",
            'amount' => request()->amount,
            'email' => request()->email,
            'tx_ref' => $reference,
            'currency' => "NGN",
            'redirect_url' => route('callback'),
            'customer' => [
                'email' => request()->email,
                "phone_number" => request()->phone,
                "name" => request()->name

            ],
            "subaccount" => [
                "id" => "RS_D87A9EE339AE28BFA2AE86041C6DE70E",
                "transaction_split_ratio" => "0.36"
            ],
            "meta" => [
                "reg_number" => request()->reg_number,
                "faculty" => request()->faculty,
                "department" => request()->department,
            ],

            "customizations" => [
                "title" => 'BSU Consultancy Services Ltd',
                "description" => "Registration Payments.",
            ]
        ];

        $payment = Flutterwave::initializePayment($data);
        
        if ($payment['status'] !== 'success') {
            // notify something went wrong
            return view('errors.unknown');
        }

        return redirect($payment['data']['link']);
    }

    public function callback(Registration $registration)
    {

        $status = request()->status;

        //if payment is successful
        if ($status == 'successful') {

            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);

            // dd(request()->all(), $data);
            // dd($data);
            $exists = Transaction::where('tx_ref', $data['data']['tx_ref'])->first();
            
            if( !$exists ) {
                Transaction::create([
                    'reg_number' =>  $data['data']['meta']['reg_number'],
                    'faculty' => $data['data']['meta']['faculty'],
                    'department' => $data['data']['meta']['department'],
                    'name' =>   $data['data']['customer']['name'],
                    'email' =>  $data['data']['customer']['email'],
                    'phone_number' =>  $data['data']['customer']['phone_number'],
                    'amount_settled' => $data['data']['amount_settled'],
                    'amount' => $data['data']['amount'],
                    'tx_ref' =>  $data['data']['tx_ref'],
                    'txr_id' => $data['data']['id'],
                    'paymentStatus' => $data['data']['status'],
                    
                ]);

                $registration = Registration::where('tx_ref', $data['data']['tx_ref']);
                $registration->update([
                    'paymentStatus' =>  $data['data']['status'],
                    'amount' =>  $data['data']['amount'],
                    'txr_id'  =>   request()->transaction_id,
                    'amount_settled'  =>  $data['data']['amount_settled'],
                ]);
                $receipt_info = Transaction::all()->where('tx_ref', $data['data']['tx_ref'])->first();

                // dd($receipt_info);
                return view('pages.receipt',  compact('receipt_info'));
                } else{
                return redirect()->route('register');
           }

        } elseif ($status == 'cancelled') {

            $registration = Registration::where('tx_ref', request()->tx_ref);
            $registration->update([
                
                'paymentStatus' =>  "cancled",
                'amount' =>  '0.00',
                'amount_settled'  =>  '0.00',
            ]);
            return view('errors.cancled');
        } else {
            $registration = Registrations::where('tx_ref', request()->tx_ref);
            $registration->update([
                
                'paymentStatus' =>  "failed",
                'amount' =>  '0.00',
                'amount_settled'  =>  '0.00',
            ]);
            return view('errors.failed');
        }

    }
}
