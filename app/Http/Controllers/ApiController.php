<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Util\Exception;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    use AuthenticatesUsers;

    public function register(Request $request)
    {
        //Validate data
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }

    public function authenticate(Request $request)
    {
        $input = $request->all();
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        $token = '';
        try {

            if(Auth::attempt(array('email' => $request->input('email'), 'password' => $request->input('password'))))
            {
                if (Auth::check()) {
                    $token = md5(rand(111111, 999999));
                    $checkUser= User::find(Auth::user()->id);
                    $checkUser->token = $token;
                    $checkUser->save();
                    return response()->json([
                        'success' => true,
                        'token' => $token,
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Credentials not match.',
                    ], 500);
                }
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Credentials not match.',
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not create token.',
            ], 500);
        }
    }

    public function createTicket(Request $request){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://reqres.in/api/users',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'ticket='.$request->message,
            CURLOPT_HTTPHEADER => array(
                'X-Auth-Token: '.$request->token,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
        return $response;
    }
}