<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Fremaa_job;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //This method will show us the Home page
    public function index() {
       
       $categories = Category::where('status',1)->orderBy('name', 'ASC')->take(8)->get();

       $featuredJobs = Fremaa_job::where('status', 1)
                        ->orderBy('created_at', 'DESC')
                        ->with('jobType')
                        ->where('isFeatured', 1)
                        ->take(6)->get();

       $latestJobs = Fremaa_job::where('status', 1)
                    ->with('jobType')
                    ->orderBy('created_at', 'DESC')
                    ->take(6)->get();

        return view('front.home', [
            'categories' => $categories,
            'featuredJobs' => $featuredJobs,
            'latestJobs' => $latestJobs
        ]);
    }

}
