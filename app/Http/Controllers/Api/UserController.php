<?php

namespace App\Http\Controllers\Api;

use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\MailController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Requests\UserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;



class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        //

      


    }

   

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

     //register the user
    public function store(Request $request)
    {
        try{
        //
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string|min:8',
            'dob' => 'required|string',
            'address' => 'required|string',
            'gender' => 'required|string|',
        ],
        [
            "email.required"=>"Please enter a valid email address",
        ]
         
    );

        $user = new User();
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->dob = $validated['dob'];
        $user->address = $validated['address'];
        $user->gender= $validated['gender'];
       
        $user->phone = $validated['phone'];
        $user->email = $validated['email'];
        $user->username = $validated['username'];
      
        $user->password = Hash::make($validated['password']);

        $checkEmailValid = $this->checkEmailValid($user->email);
        $checkEmailExists = $this->checkEmailExists($user->email);

          //generate otp token
        
          $otp = $random_number = rand(1000, 9999);

          $user->email_verification_token =  $otp;
          $user->email_verification_status = 0;
  
          if($checkEmailValid && !$checkEmailExists){

           // $token = $user->createToken('auth_token')->plainTextToken;

            $user->save();

            $token = $user->createToken('auth_token')->plainTextToken;

            //send otp email
            $this->send_otp_email("ebubeemeka19@gmail.com", "Ebube", "Verify your account", "Please use the code below to verify your email",$otp);
            return response()->json(['message' => 'User created',"token"=>$token], 201);
            
          
          
        }
        else if(!$checkEmailValid){
            return response()->json(['message' => 'That email is invalid !'], 403);


        }
        elseif($checkEmailExists==true){
            return response()->json(['message' => 'That email is already in use!'], 401);

           
        }
    }
    catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
        // catch a specific HTTP error code (e.g. 404)
        if ($e->getResponse()->getStatusCode() === 405) {
            // handle the 404 error here

            return response()->json(['message' => $e->getMessage()], 406);
        }
    } catch (\Exception $e) {
        // catch all other types of exceptions
        // handle the error here
        return response()->json(['message' => $e->getMessage()], 406);
    }
      
    }

    //Check if email is already in use by another user

    public function checkEmailExists($email)
{
  
    $user = User::where('email', $email)->first();

    if ($user) {
        return true;
       // return response()->json(['exists' => true]);
    } else {
        return false;
       // return response()->json(['exists' => false]);
    }
}
//validate email address
public function checkEmailValid($email){
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // email is valid
       return true;
      } else {
        // email is not valid
      return false;
      }
}

    /**
     * Display the specified resource.
     */

    //login user
    public function login(Request $request){

        try{
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user ) {
             return response()->json(['message'=>'That email doesn\'t exist.'],403);
        }
        else if(!Hash::check($request->password, $user->password)){
            return response()->json(['message'=>'That password is wrong.'],405);

        }

        if($user["email_verification_status"]=="0"){
            return response()->json(['message'=>'Account not verified. Please click the button below to get a verification code.'],401);

        }
       

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Successfully logged in.',
            'user' => $user,
            'access_token' => $token
        ]);
    }catch(Exception $e){

        return response()->json(['message' => $e->getMessage()],500);
    }
    
    }

    //send email otp

    public function send_otp_email($to_email, $name, $subject, $message, $otp){

        $data = array('name'=>$name, 'otp'=>$otp, 'message'=>$message);

        $receiver = $to_email;
     
        Mail::send('mail', $data, function($message)use ($receiver, $name, $subject) {
           $message->to($receiver, $name)->subject
              ($subject);
           $message->from('accounts@payworth.com','Payworth');
        });
        return response()->json(["messgages"=>"Basic Email Sent. Check your inbox."]) ;
    }

    //verify email otp

    public function verify_email_otp(Request $request){

    
            $request->validate([
                'otp' => 'required|string',
                
            ]);
    
            $email_otp = User::where('email_verification_token', $request->otp)->first();

            if($email_otp){
                $user_update = User::find($email_otp['id']);

                //update email verification status
                $user_update->email_verification_status = 1 ;
                $user_update->save();

                return response()->json(['message' => "Email successfully verified."], 201);
                  
            }
            else{
                return response()->json(['message' => "Wrong otp, please try again"], 401);
                
            }
        



    }


    public function show(User $user): Response
    {
        //

      
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        //
    }
}
