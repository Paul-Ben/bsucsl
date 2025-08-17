<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\FeeSetup;
use App\Models\Registration;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class PaymentController extends Controller
{
    public function indexPage()
    {
        $user = Auth::user();
        $faculties = Faculty::all();
        $departments = Department::all();
        $fees = FeeSetup::all();

        return view('welcome', compact('user', 'faculties', 'departments', 'fees'));
    }
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

    public function transactions(Request $request)
    {
        $authUser = Auth::user();
        if ($request->ajax()) {
            $data = Transaction::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">View</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $transactions = Transaction::orderBy('id', 'desc')->paginate(10);
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
        $txRef = 'TX-' . time() . rand(1000, 9999);
        $secretKey = env('FLW_SECRET_KEY');
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'amount' => 'required|numeric',
            'jambNo' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
        
            $user = Registration::create([
                'name' => $request->name,
                'email' => $request->email,
                'reg_number' => $request->jambNo,
                'amount' => $request->amount,
                'paymentStatus' => 'pending',
                'phone_number' => $request->phone_number,
                'faculty' => $request->faculty,
                'department' => $request->department,
                'tx_ref' => $txRef
            ]);

            
            $subaccountID = "RS_abc123xyz"; 
            $mainAccountPercentage = 90; 
            $subAccountPercentage = 10;

            $headers = [
                'Authorization' => 'Bearer ' . $secretKey,
                'Content-Type' => 'application/json',
            ];
            
            $response = Http::withHeaders($headers)->post('https://api.flutterwave.com/v3/payments', [
                'tx_ref' => $txRef,
                'amount' => $request->amount,
                'currency' => 'NGN',
                'redirect_url' => route('payment.callback'),
                'payment_options' => 'card, mobilemoneyghana, ussd',
                'customer' => [
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'name' => $request->name,
                ],
                'customizations' => [
                    'title' => 'Elearning Training',
                    'description' => 'Payment for Elearning Training',
                    'logo' => 'https://www.campus.africa/wp-content/uploads/2018/04/249270c90489c478489dd462bfce82191f3b9429.jpg',
                ],
                'subaccounts' => [
                    [
                        'id' => $subaccountID,
                        'transaction_split_ratio' => $subAccountPercentage,
                    ],
                ],
            ])->json();

            if ($response['status'] === 'success') {
                DB::commit();
                return redirect($response['data']['link']); // Redirect to payment page
            }

            DB::rollBack();
            return back()->with('error', 'Payment initiation failed.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function paymentCallback(Request $request)
    {

        $txRef = $request->tx_ref;
        $status = $request->status;
        $trxID = $request->trx_id;

        $payment = Registration::where('tx_ref', $txRef)->first();
        if (!$payment) {
            return redirect()->route('home')->with('error', 'Transaction not found.');
        }
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . env('FLW_SECRET_KEY'), // Use Flutterwave Secret Key
        //     'Content-Type' => 'application/json',
        // ])->get("https://api.flutterwave.com/v3/transactions/{$txRef}/verify")->json();


        // if ($response['status'] === 'success' && $response['data']['status'] === 'successful')
        if ($status == 'successful') {
            $trxn = Transaction::create([
                'name' => $payment->name,
                'email' => $payment->email,
                'reg_number' => $payment->reg_number,
                'amount' => $payment->amount,
                'paymentStatus' => $status,
                'phone_number' => $payment->phone_number,
                'faculty' => $payment->faculty,
                'department' => $payment->department,
                'tx_ref' => $txRef,
                'txr_id' => $trxID
            ]);
            $payment->update(['paymentStatus' => 'successful']);
            return redirect()->route('home')->with('success', 'Payment successful!');
        }

        $payment->update(['paymentStatus' => 'failed']);
        return redirect()->route('home')->with('error', 'Payment failed.');
    }
    
}
