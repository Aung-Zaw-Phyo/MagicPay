<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class WalletController extends Controller
{
    public function index () {
        return view('backend.wallet.index');
    }

    public function ssd () {
        $wallet = Wallet::with('user');

        return DataTables::of($wallet) // ->editColumn((edit-column-name),function($each){}) or ->addColumn((add-column-name),function($each){})
        ->editColumn('amount', function ($each) {
            return number_format($each->amount, 2);
        })
        ->editColumn('created_at', function ($each) {
            $number = Carbon::parse($each->created_at)->format('Y-m-d H:i:s');
            return $number;
        })
        ->editColumn('updated_at', function ($each) {
            $number = Carbon::parse($each->updated_at)->format('Y-m-d H:i:s');
            return $number;
        })
        ->addColumn('account_person', function ($each) {
            $user = $each->user;
            if ($user) {
                return '<p>Name: '.$user->name.' </p><p>Email: '.$user->email.' </p><p>Phone: '.$user->phone.' </p>';
            }
            return '-';
        })
        ->rawColumns(['account_person'])
        ->make(true);
    }

    public function addAmount () {
        $users = User::orderBy('name')->get();
        return view('backend.wallet.add_amount', compact('users'));
    }

    public function addAmountStore (Request $request){
        $request->validate([
            'user_id' => ['required'],
            'amount' => ['required']
        ], [
            'user_id.required' => 'Please select the user.'
        ]);

        $to_account = User::with('wallet')->where('id', $request->user_id)->firstOrFail();

        if($request->amount < 1000) {
            return back()->withErrors(['amount' => 'The amount must be at least 1000 MMK.'])->withInput();
        }

        DB::beginTransaction();

        try {
            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount', $request->amount);
            $to_account_wallet->update();

            $refNumber = UUIDGenerate::refNumber();
            $to_account_transaction = new Transaction();
            $to_account_transaction->ref_no = $refNumber;
            $to_account_transaction->trx_id = UUIDGenerate::trxId();
            $to_account_transaction->user_id = $to_account->id;
            $to_account_transaction->type = 1;
            $to_account_transaction->amount = $request->amount;
            $to_account_transaction->source_id = 0;
            $to_account_transaction->description = $request->description;
            $to_account_transaction->save();

            DB::commit();
            return redirect()->route('admin.wallet.index')->with('create', 'Successfully added amount.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['fail' => 'Something wrong. ' . $e->getMessage()])->withInput();
        }
    }

    public function reduceAmount () {
        $users = User::orderBy('name')->get();
        return view('backend.wallet.reduce_amount', compact('users'));
    }

    public function reduceAmountStore (Request $request){
        $request->validate([
            'user_id' => ['required'],
            'amount' => ['required']
        ], [
            'user_id.required' => 'Please select the user.'
        ]);

        $to_account = User::with('wallet')->where('id', $request->user_id)->firstOrFail();

        if($request->amount < 1) {
            return back()->withErrors(['amount' => 'The amount must be at least 1 MMK.'])->withInput();
        }

        DB::beginTransaction();

        try {
            $to_account_wallet = $to_account->wallet;

            if($request->amount > $to_account_wallet->amount) {
                throw new Exception('The amount is greater than the wallet balance');
            }

            $to_account_wallet->decrement('amount', $request->amount);
            $to_account_wallet->update();

            $refNumber = UUIDGenerate::refNumber();
            $to_account_transaction = new Transaction();
            $to_account_transaction->ref_no = $refNumber;
            $to_account_transaction->trx_id = UUIDGenerate::trxId();
            $to_account_transaction->user_id = $to_account->id;
            $to_account_transaction->type = 2;
            $to_account_transaction->amount = $request->amount;
            $to_account_transaction->source_id = 0;
            $to_account_transaction->description = $request->description;
            $to_account_transaction->save();

            DB::commit();
            return redirect()->route('admin.wallet.index')->with('create', 'Successfully reduced amount.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['fail' => 'Something wrong. ' . $e->getMessage()])->withInput();
        }
    }
}
