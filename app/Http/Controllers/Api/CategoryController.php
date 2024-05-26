<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Api\CategoryRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    use ApiResponser;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CategoryRepository $category)
    {
        $this->category   = $category;
    }

    public function create(Request $request)
    {
        $post  = $request->all();
        if ($post['key'] != config('constants.APP_KEY') || $post['tag'] != config('constants.TAG_CREATE_CATEGORY')) {
            return $this->errorResponse(trans('api.general.keytag'), config('constants.RESPONSE_ERROR'));
        } else {
            $validation  = $this->validateAll($post, 'register');
            if ($validation->fails()) {
                return $this->throwValidation($validation->messages()->first(), config('constants.RESPONSE_ERROR'));
            }

            $existsCategory = $this->category->checkExists($post);
            if (empty($existsCategory)) {
                $data = $this->category->create($post);

                if ($data) {
                    return $this->successResponse(trans('api.category.create'), config('constants.RESPONSE_SUCCESS'), $data);
                } else {
                    return $this->errorResponse(trans('api.general.invalid'), config('constants.RESPONSE_ERROR'));
                }
            } else {
                if ($existsCategory['category_name'] == $post['category_name']) {
                    return $this->errorResponse(trans('api.category.categoryExists'), config('constants.RESPONSE_ERROR'));
                }
            }
        }
    }
    /** 
     * send category list
     * 
     * 
     */

    public function list(Request $request)
    {
        $post  = $request->all();
        if ($post['key'] != config('constants.APP_KEY') || $post['tag'] != config('constants.TAG_CATEGORY_LIST')) {
            return $this->errorResponse(trans('api.general.keytag'), config('constants.RESPONSE_ERROR'));
        } else {
            $validation  = $this->validateAll($post, 'list');
            if ($validation->fails()) {
                return $this->throwValidation($validation->messages()->first(), config('constants.RESPONSE_ERROR'));
            }
            $data = $this->category->list($post);

            if ($data) {
                return $this->successResponse(trans('api.category.list'), config('constants.RESPONSE_SUCCESS'), $data);
            } else {
                return $this->errorResponse(trans('api.category.listNotExist'), config('constants.RESPONSE_ERROR'));
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
                    'category_name'   => 'required|unique:categories,category_name'
                ], $message);
                break;
        }
    }
}
