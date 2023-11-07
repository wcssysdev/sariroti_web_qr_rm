<?php

namespace App\Http\Controllers\StockOpname;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RepostController extends Controller
{
    public function index(Request $request)
    {
        generate_stock_opname_csv($request->id, session("plant"));
        return redirect()->back()->with('message', 'PID Successfully Reposted');   
    }
}
