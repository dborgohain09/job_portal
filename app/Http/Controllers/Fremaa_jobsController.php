<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Fremaa_job;
use App\Models\JobApplication;
use App\Models\JobType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Fremaa_jobsController extends Controller
{
    //This method will show the job pages
    public function index(Request $request){

        $categories = Category::where('status',1)->get();
        $jobTypes = JobType::where('status',1)->get();

        $jobs= Fremaa_job::where('status',1);

        //Search using Keyword
        
        if(!empty($request->keyword)){
           $jobs = $jobs->where(function($query) use($request){
                $query->orWhere('title', 'like', '%' .$request->keyword. '%');
                $query->orWhere('keywords', 'like', '%' .$request->keyword. '%');
           });
        }


        //Search using location
        if(!empty($request->location)){
            $jobs = $jobs->where('location',$request->location );
        }

        //Search using category
        if(!empty($request->category)){
            $jobs = $jobs->where('category_id',$request->category );
        }

        //Search using Job Type
        $jobTypeArray = [];
        if(!empty($request->jobType)){
            //1,2,3
            $jobTypeArray = explode(',', $request->jobType);
            $jobs = $jobs->whereIn('job_type_id',$jobTypeArray );
        }

        //Search using experience
        
        if(!empty($request->experience)){
            $jobs = $jobs->where('experience',$request->experience );
        }

        $jobs= $jobs->with('jobType');

        if ($request->sort == "0" ){
            $jobs = $jobs->orderBy('created_at', 'ASC');
        } else {
            $jobs = $jobs->orderBy('created_at', 'DESC');
        }
        

        $jobs = $jobs->paginate(9);


        return view('front.fremaa_jobs',[
            'categories'=> $categories,
            'jobTypes'=> $jobTypes,
            'jobs' =>$jobs,
            'jobTypeArray' =>$jobTypeArray
        ]);
    }

    //This method will show job details
    public function details($id ){
        
        $job = Fremaa_job::where([
            'id' => $id,
            'status' => 1
        ])->with(['jobType' , 'category'])->first();

        if ($job == null) {
            abort(404);
        }

        return view('front.jobDetail', ['job' => $job]);
    }

    public function applyJob(Request $request){
        
        $id = $request->id;

        $job = Fremaa_job::where('id', $id)->first();
        //if Jobs not found in db
        if ($job == null) {
            $message = 'Job does not exist.';
            session()->flash('error', $message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }

        //you can not apply on your own job

        $employer_id = $job->user_id;

        if ($employer_id == Auth::user()->id) {
            
            $message = 'You can not apply on your own job.';
            
            session()->flash('error', $message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }

        //You canot apply for a job twice
        
        $jobApplicationCount = JobApplication::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id
        ])->count();

        if ($jobApplicationCount > 0) {
            $message = 'You have already applied to this job.';
            session()->flash('error', $message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }

        $application = new JobApplication();
        $application -> job_id = $id;
        $application -> user_id = Auth::user()->id;
        $application -> employer_id = $employer_id;
        $application -> applied_date = now();
        $application -> save();

        $message = 'You have successfully applied.';
        
        session()->flash('success', $message);
        
        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }
}
