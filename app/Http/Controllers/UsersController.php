<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index()
    {
        //
    }

    public function show()
    {
        //
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:2|max:256',
            'email' => 'required|email|max:256|unique:users',
            'password' => 'required|min:6|max:12'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json(['message' => 'Request error!!'], 400);
        }

        $user = User::create($request->all());
        return response()->json($user, 201);
    }

    public function update()
    {
        //
    }

    public function destroy()
    {
        //
    }
}
