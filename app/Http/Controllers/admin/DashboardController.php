<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Middleware; 
use App\Http\Middleware\CheckAdmin;

class DashboardController extends Controller
{
    //
    #[Middleware(CheckAdmin::class)]
    public function index(){
        return view('admin.dashboard');
    }
}
