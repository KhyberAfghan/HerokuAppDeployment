<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class authController extends Controller
{
    public function register(Request $request) {

        
        $fields = $request->validate([
            'F_name' =>'required|string|max:255',
            'L_name'=>'required',
            'email' =>'required|string|unique:users,email',
            'country'=>'required|string|max:255',
            'zip_code'=>'required|string',
            'number'=>'required|string',
            // 'gender'=>'required|string',
            'gender'=> ['required','in:male,female'],
            'Image'=>'required|mimes:jpeg,png,jpg,gif,svg',
            'password' => 'required|string|confirmed'
            

        ]);
       



        $user = User::create([
            'F_name' => $fields['F_name'],
            'L_name' => $fields['L_name'],
            'email' => $fields['email'],
            'country'=>$fields['country'],
            'zip_code' => $fields['zip_code'],
            'number' => $fields['number'],
            'gender' => $fields['gender'],
            'Image' => $fields['Image'],
            'password' => bcrypt($fields['password'])
        ]);

        if ($request->file('Image')) {
    		$file = $request->file('Image');
    		$filename = date('YmdHi').$file->getClientOriginalName();
    		$file->move(public_path('upload/user_images'),$filename);
    		$user['Image'] = $filename;
        
    
    	}
 	     $user->save();

        // {
        //     "F_name":"aliahmad ",
        //     "L_name":"Jan",
        //     "email":"aliahmad12@gmail.com",
        //     "5":"Afg",
        //     "zip_code":"2211",
        //     "number":"+923331526763",
        //     "gender":"female",
        //     "password":"12345",
        //     "password_confirmation":"12345"
            //note:this data is used in postman as json format
        //     }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            // 'user' => $user,
            'token' => $token,
            'user id'=>$user->id,
            'message'=>'you registed successfully',
        
        ];

        return response($response, 201);
    }

    public function getData()
    {
        return User::all();
    }

    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad credintials'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            // 'user' => $user,
            'status code'=>'2001',
            'user id'=>$user->id,
            // 'token' => $token
        ];

        return response($response, 201);
    }

    // public function logout(Request $request) {
    //     auth()->user()->tokens()->delete();

    //     return [
    //         'message' => 'Logged out'
    //     ];
    // }
}
