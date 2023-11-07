<?php

namespace App\Http\Controllers\Authentication;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Session;

class LogoutController extends Controller
{
    public function index(Request $request)
    {
        Session::flush();
        return redirect('/');
    }
}