<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Fremaa_job;
use App\Models\JobType;
use Illuminate\Http\Request;

class Fremaa_jobsController extends Controller
{
    //This method will show the job pages
    public function index(){

        $categories = Category::where('status',1)->get();
        $jobTypes = JobType::where('status',1)->get();

        $jobs= Fremaa_job::where('status',1)->with('jobType')->orderBy('created_at', 'DESC')->paginate(9);


        return view('front.fremaa_jobs',[
            'categories'=> $categories,
            'jobTypes'=> $jobTypes,
            'jobs' =>$jobs
        ]);
    }
}
