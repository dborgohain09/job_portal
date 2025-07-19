<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Fremaa_job;
use Illuminate\Http\Request;

class Fremaa_jobController extends Controller
{
    //
    public function index(){
       $jobs = Fremaa_job::orderBy('created_at', 'DESC')->with('user')->paginate(10);
        return view('admin.jobs.list', [
            'jobs' => $jobs
        ]);
    }
}
