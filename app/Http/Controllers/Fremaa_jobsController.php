<?php
namespace App\Http\Controllers;
use App\Mail\JobNotificationEmail;
use App\Models\Category;
use App\Models\Fremaa_job;
use App\Models\JobApplication;
use App\Models\JobType;
use App\Models\SavedJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use Illuminate\Database\QueryException;

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

        $count = 0;
        if(Auth::user()){
            $count = SavedJob::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id
        ])->count();
        }

        //fetch Applications

        $applications = JobApplication::where(['job_id' => $id])->with('user')->get();

        //dd ($applications);

        return view('front.jobDetail', ['job' => $job, 
                                        'count' => $count, 
                                        'applications' =>$applications
                                        ]);
    }

    // public function applyJob(Request $request){
        
    //     $id = $request->id;

    //     $job = Fremaa_job::where('id', $id)->first();
    //     //if Jobs not found in db
    //     if ($job == null) {
    //         $message = 'Job does not exist.';
    //         session()->flash('error', $message);
    //         return response()->json([
    //             'status' => false,
    //             'message' => $message
    //         ]);
    //     }

    //     //you can not apply on your own job

    //     $employer_id = $job->user_id;

    //     //dd($employer_id);

    //     if ($employer_id == Auth::user()->id) {            
    //         $message = 'You can not apply on your own job.';            
    //         session()->flash('error', $message);
    //         return response()->json([
    //             'status' => false,
    //             'message' => $message
    //         ],200);

    //         //return redirect()->back()->with('error', 'You cannot apply on your own job.');
    //     }

    //     //You canot apply for a job twice
        
    //     $jobApplicationCount = JobApplication::where([
    //         'user_id' => Auth::user()->id,
    //         'job_id' => $id
    //     ])->count();

    //     if ($jobApplicationCount > 0) {
    //         $message = 'You have already applied to this job.';
    //         session()->flash('error', $message);
    //         return response()->json([
    //             'status' => false,
    //             'message' => $message
    //         ]);
    //     }

    //     $application = new JobApplication();
    //     $application -> job_id = $id;
    //     $application -> user_id = Auth::user()->id;
    //     $application -> employer_id = $employer_id;
    //     $application -> applied_date = now();
    //     $application -> save();

    //     $message = 'You have successfully applied.';
        
    //     session()->flash('success', $message);
        
    //     return response()->json([
    //         'status' => true,
    //         'message' => $message
    //     ]);
    // }

    public function applyJob(Request $request){
    
        $id = $request->id;

        $job = Fremaa_job::find($id);

        if (!$job) {
            return response()->json([
                'status' => false,
                'message' => 'Job does not exist.'
            ], 200);
        }

        // Prevent applying on your own job
        if ($job->user_id == Auth::user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot apply on your own job.'
            ], 200);
        }

        // Prevent duplicate applications
        $alreadyApplied = JobApplication::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id
        ])->exists();

        if ($alreadyApplied) {
            return response()->json([
                'status' => false,
                'message' => 'You have already applied to this job.'
            ], 200);
        }

        // Save the application
        JobApplication::create([
            'job_id' => $id,
            'user_id' => Auth::user()->id,
            'employer_id' => $job->user_id,
            'applied_date' => now()
        ]);

        //Send notification email to Employer
        $employer_id = $job->user_id;    
        $employer = User::where('id', $employer_id)->first();
        
        $mailData =[
            'employer' => $employer,
            'user' => Auth::user(),
            'job' => $job,
        ];

        Mail::to($employer->email)->send(new JobNotificationEmail($mailData));

        return response()->json([
            'status' => true,
            'message' => 'You have successfully applied.'
        ]);
    }

    //this method will be used to save a job to the save_job table

    // public function saveJob(Request $request){

    //     $id = $request->id;

    //     $job = Fremaa_job::find($id);

    //     session()->flash('error','Job not found');
        
    //     if($job == null){
    //         return response()->json([
    //             'status' => false,
    //         ]);  
    //     }

    //     //check if user already saved the job
    //     $count = SavedJob::where([
    //         'user_id' => Auth::user()->id,
    //         'job_id' => $id,
    //     ])->count();

    //     if($count >0){
    //         session()->flash('error','You already saved to this job');
    //         return response()->json([
    //             'status' => false,
    //         ]);
    //     }

    //     $saveJob = new SavedJob;
    //     $saveJob-> job_id = $id;
    //     $saveJob-> user_id =  Auth::user()->id;
    //     $saveJob-> save();

    //     session()->flash('success','You have successfully saved the job');
    //         return response()->json([
    //             'status' => true,
    //         ]);
    // }


    

public function saveJob(Request $request)
{
    $id = $request->id;

    $job = Fremaa_job::find($id);
    if (!$job) {
        return response()->json([
            'status' => false,
            'message' => 'Job not found in the database.',
        ], 404);
    }

    $alreadySaved = SavedJob::where('user_id', Auth::id())
                            ->where('job_id', $id)
                            ->exists();

    if ($alreadySaved) {
        return response()->json([
            'status' => false,
            'message' => 'You have already saved this job.',
        ]);
    }

    try {
        $saveJob = new SavedJob();
        $saveJob->job_id = $id;
        $saveJob->user_id = Auth::id();
        $saveJob->save();

        return response()->json([
            'status' => true,
            'message' => 'Job saved successfully.',
        ]);
    } catch (QueryException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Database error: ' . $e->getMessage(),
        ], 500);
    }
}


}
