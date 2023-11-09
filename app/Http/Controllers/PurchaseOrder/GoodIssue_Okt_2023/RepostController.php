<?php

namespace App\Http\Controllers\PurchaseOrder\GoodIssue;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RepostController extends Controller
{
    public function index(Request $request)
    {
        generate_gi_csv($request->id, session("plant"));
        return redirect()->back()->with('message', 'GI Successfully Reposted');   
    }
}
