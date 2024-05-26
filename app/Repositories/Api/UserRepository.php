<?php

namespace App\Repositories\Api;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\JWT;
use App\Repositories\CommonRepository;


class UserRepository
{
    protected $jwt;
    protected $Common;
    public function __construct(JWT $jwt, CommonRepository $Common)
    {
        $this->jwt = $jwt;
        $this->Common = $Common;
    }
    public function findUser($id)
    {
        return User::find($id);
    }
    public function register($post)
    {
        $user = User::create([
            'name'            => $post['name'],
            'email'                 => $post['email'],
            'password'              => $post['password'],
        ]);
        if ($user) {
            $credentials = ['email' => $post['email'], 'password' => $post['password']];
            $token       = Auth::guard('api')->attempt($credentials);

            $udata = User::find($user['id']);
            $udata['token'] = $token;
            return $udata;
        } else {
            return false;
        }
    }
    public function checkExists($post)
    {
        $data = User::select('*')
        ->where('email', $post['email'])
        ->first();
        return $data;
    }
    public function checkExistsEmail($post)
    {
        $query = User::select('*');
        $query->where('email', $post['email']);
        return $query->first();
    }

    public function checkUserIdExists($post){
        $query = User::select('*');
        $query->where('id', $post['user_id']);
        return $query->first();
    }
    public function checkExistsUpdate($post)
    {
        $data = User::select('*')->where('politician_id', $post['politician_id'])
            ->where('id', '!=', $post['user_id'])
            ->where(function ($query) use ($post) {
                $query->where('email', $post['email'])
                    ->orwhere('mobile_number', $post['mobile_number']);
            })->first();

        return $data;
    }
    public function login($post, $userData)
    {
        $user = User::where('email',$post['email'])->where('password',$userData['password'])->first();
        $udata = User::find($user['id']);
        if ($udata) {
            $credentials = ['email' => $post['email'], 'password' =>$post['password']];
            $token       = Auth::guard('api')->attempt($credentials);
            $udata['token'] = $token;
            return $udata;
        } else {
            return false;
        }
    }

    public function logout($post)
    {

        $user = User::where('id', $post['user_id'])->first();
        if ($user) {
            //Auth::guard('api')->logout();
            return true;
        } else {
            return false;
        }

        /*}else{
            return false;
        }*/
    }
}
