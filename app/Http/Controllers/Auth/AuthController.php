<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Registerotp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{

    public function random($length, $chars = ''){
        if (!$chars) {
            $chars = implode(range('a','f'));
            $chars .= implode(range('0','9'));
        }
        $shuffled = str_shuffle($chars);
        return substr($shuffled, 0, $length);
    }
    public function serialkey(){
        return random(4).''.random(4).''.random(4).''.random(4);
    }

    public function index(Request $request){
        $activationKey = self::serialkey();
        $data = ['activationKey'=>$activationKey,'to'=>'venkatraman858@gmail.com','mail_subject'=>'App Activation','template'=>'activation'];
        BaseController::sendEmail($data);
    }
    public function login(Request $request) {
        $message = 'User login failed.';
        $status = 0;
        $data = array();
        $request->validate([
            'mobile' => 'required',
            'password' => 'required|string',
            //'remember_me' => 'boolean'
        ]);
        $credentials = request(['mobile', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => $message,
                'status' => $status
            ], 200);
        $user = $request->user();
        $tokenResult = $user->createToken($request->email);
        /*$token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        if(!empty($token)){
            
        }*/
        $message = 'User login success.';
            $status = 1;
            $data = array('access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'user' => $user);
        return response()->json([
            'user_detail' =>$data,
            'status' => $status,
            'message' => $message
        ]);
    }
    public function register(Request $request)
    {
        $createUser = '';
        if(Auth::user()){
            $createUser = Auth::user();
        }
        $message = 'User creation failed.';
        $status = 0;

        $request->validate([
            'first_name' => 'required|string',
            'mobile' => 'required|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
        ]);

        $user = new User;
        $user->first_name = $request->first_name;
        if($request->last_name){
        $user->last_name = $request->last_name;
        }
        $user->mobile = $request->mobile;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->user_status = 'Active';
        $user->user_activation = 'Inactive';
        $user->user_type = $request->userType;
        if($createUser != ''){
        $user->created_by = $createUser->id;
        }
        $user->save();
        
        if(!empty($user)){
        	$otp = new Registerotp;
        	$otp->user_id = $user->id;
        	$otp->otp = mt_rand(100000, 999999);
        	$otp->save();
            $data = ['first_name'=>$user->first_name,'last_name'=>$user->last_name,'otp'=>$otp->otp,'to'=>$user->email,'mail_subject'=>'OTP Verification','template'=>'otp'];
            Controller::sendEmail($data);
            $message = 'Successfully created user!';
            $status = 1;
        }
        
        return response()->json([
            'status' => $status,
            'message' => $message,
            'user_detail' => $user
        ], 200);
    }
    public function verifyOtp(Request $request)
    {
        $message = '';
        $status = 0;
        $verifyotp = Registerotp::where('user_id',$request->user_id)->where('otp_status','0')->first();
        if(!empty($verifyotp)){
            if($verifyotp->otp == $request->otp){
                $userDetail = User::where('id', $request->user_id)->first();
                $userDetail->user_activation = 'Active';
                $userDetail->save();
                if($userDetail->user_activation == 'Active'){
                    $verifyotp->otp_status = '1';
                    $verifyotp->save();
                    $message = 'Successfully Verified!';
                    $status = 1;
                }
            }
        }else{
            $message = 'Your Account not registered.';
            $status = 0;
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ], 200);
    }
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
  
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Get the Customers
     *
     * @return [json] customer object
     */
    public function customers(Request $request)
    {
        $message = 'No Cutomers Found.';
        $status = 0;
        $user=Auth::user();
        if($user->user_type == "Dealer"){
            $customers = User::where('user_status','Active')->where('id','!=',$user->id)->get();
        }else if($user->user_type == "Sub-Dealer"){
            $customers = User::where('user_status','Active')->where('user_type','Customer')->where('created_by',$user->id)->get();
        }
        if(!empty($customers)){
            $status = 1;
            $message = 'Customer lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'customersList' => $customers
        ], 200);
    }
    public function customer(Request $request)
    {
        $message = 'Customer not available.';
        $status = 0;
        //$customer = User::where('user_type','Customer')->where('id',$request->customerId)->first();
        $customer = User::where('id',$request->customerId)->first();
        if(!empty($customer)){
            $status = 1;
            $message = 'Customer details.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'customerDetails' => $customer
        ], 200);
    }

    public function customerByType(Request $request)
    {
        $message = 'No Cutomers Found.';
        $status = 0;
        $user=Auth::user();
        $customers = User::where('user_status','Active')->where('user_type',$request->user_type)->where('id','!=',$user->id)->get();
        if(!empty($customers)){
            $status = 1;
            $message = 'Customer lists.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'customersList' => $customers
        ], 200);
    }

    public function updateUser(Request $request, $id){
        $message = 'Cutomers update failed.';
        $status = 0;
        $customer = User::findOrFail($id);
        /*$this->validate($request,[
        'first_name'=>'required|max:8',
        'email'=>'required',
        'mobile'=>'required'
        ]);
        dd($request);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->messages()],Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = 'Validation error';
        }else{
            
            
        }*/
        //dd($request->first_name);
        $customer = User::find($id);
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->email = $request->email;
        $customer->mobile = $request->mobile;
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->country = $request->country;
        $customer->postcode = $request->postcode;
        $customer->photo = $request->photo;        
        if($customer->save()){
            $status = 1;
            $message = 'Customer details updated.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'customerDetails' => $customer
        ], 200);
    }

    public function deleteUser(Request $request, $id){
        $message = 'Failed delete Cutomer.';
        $status = 0;
        $customer = User::find($id);
        if($customer){
            $customer->delete();
            $message = 'Cutomer deleted successfully.';
            $status = 1;
        }else{
            $message = 'Cutomer does not available.';
            $status = 0;
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ], 200);
    }
       
    public function uploadImage(Request $request,$id){
        $customer = User::find($id);
        $request['image_data']= str_replace('data:image/*;charset=utf-8;base64,','',$request['image_data']);
        $file = base64_decode($request['image_data']);
        $folderName = 'public/uploads/';
        $safeName = strtotime("now").'_'.$id.'.jpg';
        $destinationPath = public_path() . $folderName;
        $success = file_put_contents(public_path().'/uploads/profile/'.$safeName, $file);
        $customer->photo = $safeName;        
        if($customer->save()){
            $status = 1;
            $message = 'Customer Photo details updated.';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ], 200);
        
    }
}
