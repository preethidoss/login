<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use App\User;

class MainController extends Controller
{
    function index()
    {
     return view('login');
    }

    function checklogin(Request $request)
    {
     $this->validate($request, [
      'email'   => 'required|email',
      'password'  => 'required|alphaNum|min:3'
     ]);

     $user_data = array(
      'email'  => $request->get('email'),
      'password' => $request->get('password')
     );

     if(Auth::attempt($user_data))
     {
      return redirect('main/successlogin');
     }
     else
     {
      //return back()->with('error', 'Wrong Login Details');
      return redirect('main/create');
     }

    }
    public function create()

    {

        return view('register');

    }

    public function store()

    {

        request()->validate([

            'name' => 'required|min:2|max:50',

            'phone' => 'required|numeric',            

            'email' => 'required|email|unique:users',

            'password' => 'required|min:6',                

            'confirm_password' => 'required|min:6|max:20|same:password',

        ], [

            'name.required' => 'Name is required',

            'name.min' => 'Name must be at least 2 characters.',

            'name.max' => 'Name should not be greater than 50 characters.',

        ]);



        $input = request()->except('password','confirm_password');

        $user=new User($input);

        $user->password=bcrypt(request()->password);

        $user->save();



        return back()->with('success', 'User created successfully.');

        

    }

    function successlogin()
    {
     return view('successlogin');
    }

    function logout()
    {
     Auth::logout();
     return redirect('main');
    }
}