<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Fremaa_job;
use App\Models\JobType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Fremaa_jobController extends Controller
{
    //
    public function index(){
       $jobs = Fremaa_job::orderBy('created_at', 'DESC')->with('user','applications')->paginate(10);
        return view('admin.jobs.list', [
            'jobs' => $jobs
        ]);
    }

    //
    public function edit($id){
        $job = Fremaa_job::findOrFail($id);

        $categories = Category::orderBy('name', 'ASC')->get();
        $jobTypes = JobType::orderBy('name', 'ASC')->get();
        
        return view('admin.jobs.edit', [
            'job' => $job,
            'categories' =>$categories,
            'jobTypes' =>$jobTypes
        ]);
    }

    //

     public function update(Request $request, $id){        
        $rules=[
            'title'=>'required|min:5|max:200',
            'category' =>'required',
            'jobType' =>'required',
            'vacancy' =>'required|integer',
            'location' =>'required|max:50',
            'description' =>'required',
            'company_name' =>'required|min:3|max:75',
        ];    
        $validator = Validator::make($request->all(),$rules);

        if($validator->passes()){

            $fremaa_job = Fremaa_job::find($id);

            $fremaa_job->title=$request->title;
            $fremaa_job->category_id=$request->category;
            $fremaa_job->job_type_id=$request->jobType;
            $fremaa_job->vacancy=$request->vacancy;
            $fremaa_job->salary=$request->salary;
            $fremaa_job->location=$request->location;
            $fremaa_job->description=$request->description;
            $fremaa_job->benefits=$request->benefits;
            $fremaa_job->responsibility=$request->responsibility;
            $fremaa_job->qualifications=$request->qualifications;
            $fremaa_job->experience=$request->experience;
            $fremaa_job->keywords=$request->keywords;
            $fremaa_job->company_name=$request->company_name;
            $fremaa_job->company_location=$request->company_location;
            $fremaa_job->company_website=$request->company_website;

            $fremaa_job->save();

            session()->flash('success', 'Job Details updated successfully');

            return response()->json([
                'status'=>true,
                'errors' =>[]
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'errors' =>$validator->errors()
            ]);
        }
    }
}
