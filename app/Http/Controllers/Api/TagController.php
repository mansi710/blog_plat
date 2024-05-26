<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Api\TagRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{

    use ApiResponser;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TagRepository $tag)
    {
        $this->tag   = $tag;
    }

    public function create(Request $request)
    {
        $post  = $request->all();
        if ($post['key'] != config('constants.APP_KEY') || $post['tag'] != config('constants.TAG_CREATE_POST_TAG')) {
            return $this->errorResponse(trans('api.general.keytag'), config('constants.RESPONSE_ERROR'));
        } else {
            $validation  = $this->validateAll($post, 'register');
            if ($validation->fails()) {
                return $this->throwValidation($validation->messages()->first(), config('constants.RESPONSE_ERROR'));
            }

            $existstag = $this->tag->checkExists($post);
            if (empty($existstag)) {
                $data = $this->tag->create($post);

                if ($data) {
                    return $this->successResponse(trans('api.tag.create'), config('constants.RESPONSE_SUCCESS'), $data);
                } else {
                    return $this->errorResponse(trans('api.general.invalid'), config('constants.RESPONSE_ERROR'));
                }
            } else {
                if ($existstag['tag_name'] == $post['tag_name']) {
                    return $this->errorResponse(trans('api.tag.tagExists'), config('constants.RESPONSE_ERROR'));
                }
            }
        }
    }
    /** 
     * send tag list
     * 
     * 
     */

    public function list(Request $request)
    {
        $post  = $request->all();
        if ($post['key'] != config('constants.APP_KEY') || $post['tag'] != config('constants.TAG_POST_TAG_LIST')) {
            return $this->errorResponse(trans('api.general.keytag'), config('constants.RESPONSE_ERROR'));
        } else {
            $validation  = $this->validateAll($post, 'list');
            if ($validation->fails()) {
                return $this->throwValidation($validation->messages()->first(), config('constants.RESPONSE_ERROR'));
            }
            $data = $this->tag->list($post);

            if ($data) {
                return $this->successResponse(trans('api.tag.list'), config('constants.RESPONSE_SUCCESS'), $data);
            } else {
                return $this->errorResponse(trans('api.tag.listNotExist'), config('constants.RESPONSE_ERROR'));
            }

            return $this->errorResponse(trans('api.general.invalidToken'), config('constants.RESPONSE_ERROR'));
        }
    }
    /**
     * Validation
     *
     * @param array $requestData
     * @return Illuminate\Http\JsonResponse
     */
    public function validateAll($request_data, $social = '', $data = '')
    {
        switch ($social) {
            case 'list':
                $message           = ['name.regex' => trans('validation.name')];
                return $validation = Validator::make($request_data, [
                ], $message);
                break;
            default:
                $message           = ['name.regex' => trans('validation.name')];
                return $validation = Validator::make($request_data, [
                    'tag_name'   => 'required|unique:tags,tag_name'
                ], $message);
                break;
        }
    }
}
