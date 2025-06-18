<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class AccountController extends Controller
{
    //this method will show user registration page
    public function registration(){
        return view('front.account.registration');
    }

    //this method will save a user
    public function proceesRegistration(Request $requset){
        $validator=Validator::make($requset->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>"required|min:5|same:confirm_password",
            'confirm_password'=>'required'
        ]);

        if($validator->passes()){


            $user=new User();
            $user->name=$requset->name;
            $user->email=$requset->email;
            $user->password=Hash::make($requset->password);
            $user->save();

            session()->flash('success', 'You have registred sucessfully.');


            return response()->json([
                'status'=>true,
                'errors'=>[]
            ]);

        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }

    //this method will show user login page
    public function login(){
        return view('front.account.login');
    }

    //This method will authenticate the User
    public function authenticate(Request $request){
        $validator=Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if($validator->passes()){
            if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password])){
                return redirect()->route('account.profile');
            }else{
                return redirect()->route('account.login')->with('error', 'Either Email/Password is incorect');
            }
        }else{
            return redirect()->route('account.login')
            ->withErrors($validator)
            ->withInput($request->only('email'));
        }
    }

    //this method will display the Profile
    public function profile(){
        
        $id=Auth::user()->id;
        $user=User::where('id', $id)->first();
        
        return view('front.account.profile', [
            'user'=>$user]);
    }


    //this method is used for update profile
    public function updateProfile(Request $request){

        $id=Auth::user()->id;
        $validator=Validator::make($request->all(),[

            'name'=>'required|min:5|max:20',
            'email'=> 'required|email|unique:users,email,'.$id.',id'

        ]);

        if($validator->passes()){

            $user=User::find($id);
            $user->name=$request->name;
            $user->email=$request->email;
            $user->designation=$request->designation;
            $user->mobile=$request->mobile;
            $user->save();

            session()->flash('success', 'Profile updated Successfully.');
            
            return response()->json([
                'status'=>true,
                'errors'=>[]
            ]);

        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }

    //this method is for logout 
    public function logout(){
        Auth::logout();
        return redirect()->route('account.login');
    }


    //this method will upload the profile picture of user
    // public function updateProfilePic(Request $request){
    //     //dd($request->all());
    //     $id=Auth::user()->id;

    //    $validator = Validator::make($request->all(), [
    //     'image' => 'required|image'
    //     ]);

    //     if($validator->passes()){

    //         $image=$request->image;
    //         $ext=$image->getClientOriginalExtension();
    //         $imageName=$id.'-'.time().'.'.$ext;
    //         $image->move(public_path('/profile_pic/'), $imageName);


    //         //Create a small thumbnail
    //         // create new image instance (800 x 600)

    //             $sourcePath= public_path('/profile_pic/'.$imageName);
    //             $manager = new ImageManager(Driver::class);
    //             $image = $manager->read($sourcePath);

    //             // crop the best fitting 5:3 (600x360) ratio and resize to 600x360 pixel
    //             $image->cover(150, 150);
    //             $image->toPng()->save(public_path('/profile_pic/thumb/'), $imageName);

    //         User::where('id',$id)->update(['image'=>$imageName]);

    //         session()->flash('success', 'Profile Picture updated successfully');

    //         return response()->json([
    //             'status'=>true,
    //             'errors'=>[]
    //         ]);

    //     }else{
    //         return response()->json([
    //             'status'=>false,
    //             'errors'=>$validator->errors()
    //         ]);
    //     }
    // }


    public function updateProfilePic(Request $request)
{
    $id = Auth::user()->id;

    $validator = Validator::make($request->all(), [
        'image' => 'required|image'
    ]);

    if ($validator->passes()) {

        $image = $request->file('image');
        $ext = $image->getClientOriginalExtension();
        $imageName = $id . '-' . time() . '.' . $ext;

        // Create directory if not exists
        if (!File::exists(public_path('profile_pic/thumb/'))) {
            File::makeDirectory(public_path('profile_pic/thumb/'), 0755, true);
        }

        // Move original image
        $image->move(public_path('profile_pic'), $imageName);

        // Create thumbnail
        $sourcePath = public_path('profile_pic/' . $imageName);
        $thumbPath = public_path('profile_pic/thumb/' . $imageName);

        $manager = new ImageManager(new Driver());
        $img = $manager->read($sourcePath);
        $img->cover(150, 150)->toPng()->save($thumbPath);

        //Delete old Profile Pic
        File::delete(public_path('/profile_pic/'.Auth::user()->image));
        File::delete(public_path('/profile_pic/thumb/'.Auth::user()->image));

        // Update DB
        User::where('id', $id)->update(['image' => $imageName]);

        session()->flash('success', 'Profile Picture updated successfully');

        return response()->json([
            'status' => true,
            'errors' => []
        ]);

    } else {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}

public function createJob(){

    $categories = Category::orderBy('name', 'ASC')->where('status', 1)->get();

    $jobTypes = JobType::orderBy('name', 'ASC')->where('status', 1)->get();

    return view('front.account.job.create', [
        'categories' => $categories,
        'jobTypes' =>$jobTypes,
    ]);
}

}