<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Api\UserRepository;
use App\Traits\ApiResponser;
use Validator;

class UserController extends Controller
{
    use ApiResponser;
    public function __construct(UserRepository $user)
    {
        $this->user   = $user;
    }
    public function register(Request $request)
    {
        $post  = $request->all();
        if ($post['key'] != config('constants.APP_KEY') || $post['tag'] != config('constants.TAG_REGISTER')) {
            return $this->errorResponse(trans('api.general.keytag'), config('constants.RESPONSE_ERROR'));
        } else {
            $validation  = $this->validateAll($post, 'register');
            if ($validation->fails()) {
                return $this->throwValidation($validation->messages()->first(), config('constants.RESPONSE_ERROR'));
            }

            $existsUser = $this->user->checkExists($post);
            if (empty($existsUser)) {
                $data = $this->user->register($post);

                if ($data) {
                    return $this->successResponse(trans('api.user.register'), config('constants.RESPONSE_SUCCESS'), $data);
                } else {
                    return $this->errorResponse(trans('api.general.invalid'), config('constants.RESPONSE_ERROR'));
                }
            } else {
                if ($existsUser['email'] == $post['email']) {
                    return $this->errorResponse(trans('api.user.emailExists'), config('constants.RESPONSE_ERROR'));
                }
            }
        }
    }
    public function login(Request $request)
    {
        $post  = $request->all();
        if ($post['key'] != config('constants.APP_KEY') || $post['tag'] != config('constants.TAG_LOGIN')) {
            return $this->errorResponse(trans('api.general.keytag'), config('constants.RESPONSE_ERROR'));
        } else {
            $validation  = $this->validateAll($post, 'login');
            if ($validation->fails()) {
                return $this->throwValidation($validation->messages()->first(), config('constants.RESPONSE_ERROR'));
            }

            $existsUser = $this->user->checkExistsEmail($post);
            if (!empty($existsUser)) {
                if ($existsUser['deleted_at'] == NULL) {
                    $data = $this->user->login($post, $existsUser);
                    
                    if ($data) {
                        return $this->successResponse(trans('api.user.login'), config('constants.RESPONSE_SUCCESS'), $data);
                    } else {
                        return $this->errorResponse(trans('api.general.invalid'), config('constants.RESPONSE_ERROR'));
                    }
                } else {
                    return $this->successResponse(trans('api.user.softdelete'), config('constants.RESPONSE_SUCCESS'), $existsUser);
                }
            } else {
                return $this->errorResponse(trans('api.user.notExists'), config('constants.RESPONSE_ERROR'));
            }
        }
    }
    public function logout(Request $request)
    {
        $post  = $request->all();
        if ($post['key'] != config('constants.APP_KEY') || $post['tag'] != config('constants.TAG_LOGOUT')) {
            return $this->errorResponse(trans('api.general.keytag'), config('constants.RESPONSE_ERROR'));
        } else {
            $validation  = $this->validateAll($post, 'logout');
            if ($validation->fails()) {
                return $this->throwValidation($validation->messages()->first(), config('constants.RESPONSE_ERROR'));
            }

            $existsUser = $this->user->checkUserIdExists($post);

            if ($existsUser) {
                $data = $this->user->logout($post);
                if($data){
                    return $this->successResponse(trans('api.user.logout'), config('constants.RESPONSE_SUCCESS'));
                }else {
                    return $this->errorResponse(trans('api.general.invalid'), config('constants.RESPONSE_ERROR'));
                }
            } else {
                return $this->errorResponse(trans('api.user.notExists'), config('constants.RESPONSE_ERROR'));
            }
        }
    }
    public function validateAll($requestData, $social = '', $data = '')
    {
        switch ($social) {
            case 'register':
                return $validation = Validator::make($requestData, [
                    'name'     => 'required|max:50',
                    'email'          => 'required|email|unique:users,email|max:50',
                    'password'  => 'required|max:10',
                    'confirm_password'         => 'required|same:password',
                ]);
                break;
            case 'logout':
                return $validation = Validator::make($requestData, ['user_id'  => 'required|integer']);
                break;

                //default login validation
            default:
                return $validation = Validator::make($requestData, ['email'  => 'required','password' => 'required']);
                break;
        }
    }
}
