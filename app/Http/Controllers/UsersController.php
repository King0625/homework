<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function login(Request $request){
        $email = $request->auth_email;
        $password = $request->auth_password;
        $user = User::where('email', $email)->where('password', $password)->first();
        if(!is_null($user)){
            /* Renew an api_token every time user login?? */
            // $user->update(['api_token' => Str::random(60)]);
            return response(['message' => 'Login successfully!', 'api_token' => $user->api_token], 200);
        }
        return response(['message' => 'Authentication error!'], 401);
    }

    public function index(Request $request)
    {
        $auth_user = request()->get('auth_user')->first();
        // $token = $request->header('api_token');
        // $auth_user = User::where('api_token', $token)->get(); 

        // dd($user['id']);
        if($auth_user['superuser']){
            return response(['data' => User::get()], 200);
        }
        return response(['message' => 'Authentication error!'], 401);        
    }

    public function show($id)
    {
        $auth_user = request()->get('auth_user')->first();
        $user = User::find($id);

        // dd($user['id']);
        if($auth_user['superuser']){
            if($this->exist($id)){
                return response($user, 200);
            }else{
                return response(['message' => 'User not found!!'], 404);
            }
        }elseif($auth_user['id'] == $id){
            return response(['data' => $user], 200);
        }else{
            return response(['message' => 'Authentication error!'], 401);
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
            return response($validator->errors(), 400);
        }
        $data = $request->all();
        $data['superuser'] = User::REGULAR_USER;
        $data['api_token'] = Str::random(60);

        $user = User::create($data);
        return response(['data' => $user, 'api_token' => $user['api_token']], 201);
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
            return response($validator->errors(), 400);
        }

        /* Authentication */
        $auth_user = request()->get('auth_user')->first();
        $user = User::find($id);

        if($auth_user['superuser']){
            if($this->exist($id)){
                $user->update($request->all());
                return response(['data' => $user], 200);
            }else{
                return response(['message' => 'User not found!'], 404);
            }
        }elseif($auth_user['id'] == $id){
            $user->update($request->all());
            return response(['data' => $user], 200);
        }else{
            return response(['message' => 'Authentication error!'], 401);
        }

    }

    public function destroy($id)
    {
        $auth_user = request()->get('auth_user')->first();
        $user = User::find($id);

        if($auth_user['superuser']){
            if($this->exist($id)){
                $user->delete();
                // return response()->json(['message' => 'User deleted'], 204);
                return response(['message' => 'User deleted'], 200);
            }else{
                return response(['messgae' => 'User not found'], 404);
            }
        }elseif($auth_user['id'] == $id){
            $user->delete();
            return response(['message' => 'User deleted successfully!'], 200);
        }else{
            return response(['message' => 'Authentication error'], 401);
        }
        
    }

    private function exist($id){
        $user = User::find($id);
        return !is_null($user);
    }
}
