<?php

namespace App\Http\Controllers\PurchaseOrder\GoodReceipt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RepostController extends Controller
{
    public function index(Request $request)
    {
        generate_gr_csv($request->id, session("plant"));
        return redirect()->back()->with('message', 'GR Successfully Reposted');   
    }
}
