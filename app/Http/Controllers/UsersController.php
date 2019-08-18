<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index()
    {
        $auth_user = request()->get('auth_user');
        
        // dd($user['id']);
        if($auth_user['superuser']){
            return response()->json(User::get(), 200);
        }
        return response()->json(['message' => 'Authentication error!'], 401);        
    }

    public function show($id)
    {
        $auth_user = request()->get('auth_user');
        $user = User::find($id);

        // dd($user['id']);
        if($auth_user['superuser']){
            if($this->exist($id)){
                return response()->json($user, 200);
            }else{
                return response()->json(['message' => 'User not found!!'], 404);
            }
        }elseif($auth_user['id'] == $id){
            return response()->json($user, 200);
        }else{
            return response()->json(['message' => 'Authentication error!'], 401);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:2|max:256',
            'email' => 'required|email|max:256|unique:users',
            // 'superuser' => 'required|boolean',
            'password' => 'required|min:6|max:12|confirmed',
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $data = $request->all();
        $data['superuser'] = User::REGULAR_USER;

        $user = User::create($request->all());
        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        /* Validation */
        $rules = [
            'name' => 'min:2|max:256',
            // 'email' => 'email|max:256|unique:users'. ($id ? ",id,$id" : ''),
            'email' => 'email|max:256|unique:users,email,'.$id,
            'superuser' => 'boolean',
            'password' => 'min:6|max:12',
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        /* Authentication */
        $auth_user = request()->get('auth_user');
        $user = User::find($id);

        if($auth_user['superuser']){
            if($this->exist($id)){
                $user->update($request->all());
                return response()->json($user, 200);
            }else{
                return response()->json(['message' => 'User not found!'], 404);
            }
        }elseif($auth_user['id'] == $id){
            $user->update($request->all());
            return response()->json($user, 200);
        }else{
            return response()->json(['message' => 'Authentication error!'], 401);
        }

    }

    public function destroy($id)
    {
        $auth_user = request()->get('auth_user');
        $user = User::find($id);

        if($auth_user['superuser']){
            if($this->exist($id)){
                $user->delete();
                return response()->json(['message' => 'User deleted successfully!'], 204);
            }else{
                return response()->json(['messgae' => 'User not found'], 404);
            }
        }elseif($auth_user['id'] == $id){
            $user->delete();
            return response()->json(['message' => 'User deleted successfully!'], 204);
        }else{
            return response()->json(['message' => 'Authentication error'], 401);
        }
        
    }

    private function exist($id){
        $user = User::find($id);
        return !is_null($user);
    }
}
