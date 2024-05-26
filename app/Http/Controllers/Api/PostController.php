<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Api\PostRepository;
use App\Traits\ApiResponser;
use App\Models\Comment;
use App\Models\Post;
use Validator;
use DB;
use Auth;

class PostController extends Controller
{
    use ApiResponser;
    public function __construct(PostRepository $post)
    {
        $this->post   = $post;
    }
    /** ========================================================= ADD Post ========================================================= **/

    /** Add Post **/
    public function create(Request $request)
    {
        $post  = $request->all();
        if ($post['key'] != config('constants.APP_KEY') || $post['tag'] != config('constants.TAG_CREATE_POST')) {
            return $this->errorResponse(trans('api.general.keytag'), config('constants.RESPONSE_ERROR'));
        } else {
            $validation  = $this->validateAll($post, 'create');
            if ($validation->fails()) {
                return $this->throwValidation($validation->messages()->first(), config('constants.RESPONSE_ERROR'));
            }

            $data = $this->post->create($post);
            if ($data) {
                return $this->successResponse(trans('api.post.create'), config('constants.RESPONSE_SUCCESS'), $data);
            } else {
                return $this->errorResponse(trans('api.general.invalid'), config('constants.RESPONSE_ERROR'));
            }
        }
    }

    /** My Post List **/
    public function my_post_list(Request $request)
    {
        $post  = $request->all();
        $validation  = $this->validateAll($post, 'postlist');
        if ($validation->fails()) {
            return $this->throwValidation($validation->messages()->first(), config('constants.RESPONSE_ERROR'));
        }
        $page       = isset($post['page']) ? $post['page'] : 1;
        $data       = $this->post->my_post_list($post, $page);

        if ($data) {
            $total_social_post = post::select('*')->where('user_id', Auth::user()->id)->count();
            return $this->successResponseOfTotalPosts(
                trans('api.post.list'),
                config('constants.RESPONSE_SUCCESS'),
                $total_social_post,
                $data
            );

            // return $this->successResponse(trans('api.post.list'), config('constants.RESPONSE_SUCCESS'), $data);
        } else {
            return $this->errorResponse(trans('api.post.notFound'), config('constants.RESPONSE_ERROR'));
        }
        return $this->errorResponse(trans('api.general.invalid'), config('constants.RESPONSE_ERROR'));
    }

    /** List Post **/
    public function list(Request $request)
    {
        $post  = $request->all();
        if ($post['key'] != config('constants.APP_KEY') || $post['tag'] != config('constants.TAG_POST_LIST')) {
            return $this->errorResponse(trans('api.general.keytag'), config('constants.RESPONSE_ERROR'));
        } else {
            $validation  = $this->validateAll($post, 'list');
            if ($validation->fails()) {
                return $this->throwValidation($validation->messages()->first(), config('constants.RESPONSE_ERROR'));
            }
            $data       = $this->post->list($post);


            if ($data) {
                $total_social_post = Post::select('*')->count();
                return $this->successResponseOfTotalPosts(
                    trans('api.post.list'),
                    config('constants.RESPONSE_SUCCESS'),
                    $total_social_post,
                    $data
                );

                return $this->successResponse(trans('api.post.list'), config('constants.RESPONSE_SUCCESS'), $data);
            } else {
                return $this->errorResponse(trans('api.post.listNotExist'), config('constants.RESPONSE_ERROR'));
            }

            return $this->errorResponse(trans('api.general.invalidToken'), config('constants.RESPONSE_ERROR'));
        }
    }



    /** Update soical powt with or without image/video **/
    public function update_post(Request $request)
    {
        $post  = $request->all();
        if ($post['key'] != config('constants.APP_KEY') || $post['tag'] != config('constants.TAG_UPDATE_POST')) {
            return $this->errorResponse(trans('api.general.keytag'), config('constants.RESPONSE_ERROR'));
        } else {
            $validation  = $this->validateAll($post, 'update_post');
            if ($validation->fails()) {
                return $this->throwValidation($validation->messages()->first(), config('constants.RESPONSE_ERROR'));
            } else {
                $data = $this->post->update_post($post);
                if ($data) {
                    if ($data === 403) {
                        return $this->errorResponse(trans('api.general.Unauthorized'), config('constants.RESPONSE_ERROR'));
                    }
                    if ($data === true) {
                        return $this->successResponse(trans('api.post.update_post'), config('constants.RESPONSE_SUCCESS'), $data);
                    }
                } else {
                    return $this->errorResponse(trans('api.general.invalid'), config('constants.RESPONSE_ERROR'));
                }
            }
        }
    }
    // /** Delete post **/
    public function delete_post(Request $request)
    {
        $post  = $request->all();
        if ($post['key'] != config('constants.APP_KEY') || $post['tag'] != config('constants.TAG_DELETE_POST')) {
            return $this->errorResponse(trans('api.general.keytag'), config('constants.RESPONSE_ERROR'));
        } else {
            $validation  = $this->validateAll($post, 'delete_post');
            if ($validation->fails()) {
                return $this->throwValidation($validation->messages()->first(), config('constants.RESPONSE_ERROR'));
            } else {
                /** use for check post exist or not **/
                $checkExists = $this->post->checkExistspost($post);
                if ($checkExists == 0) {
                    return $this->errorResponse(trans('api.post.notFound'), config('constants.RESPONSE_ERROR'));
                } else {
                    $data = $this->post->delete_post($post);
                    if ($data === true) {
                        return $this->successResponse(trans('api.post.delete_post'), config('constants.RESPONSE_SUCCESS'), $data);
                    } else if ($data === 403) {
                        return $this->errorResponse(trans('api.general.Unauthorized'), config('constants.RESPONSE_ERROR'));
                    } else {
                        return $this->errorResponse(trans('api.general.invalid'), config('constants.RESPONSE_ERROR'));
                    }
                }
            }
        }
    }

    /** Add comment on Post **/
    public function post_comment(Request $request)
    {
        $post  = $request->all();
        if ($post['key'] != config('constants.APP_KEY') || $post['tag'] != config('constants.TAG_POST_COMMENT')) {
            return $this->errorResponse(trans('api.general.keytag'), config('constants.RESPONSE_ERROR'));
        } else {
            $validation  = $this->validateAll($post, 'post_comment');
            if ($validation->fails()) {
                return $this->throwValidation($validation->messages()->first(), config('constants.RESPONSE_ERROR'));
            } else {
                $data = $this->post->post_comment($post);
                if ($data) {
                    return $this->successResponse(trans('api.post.comment_added'), config('constants.RESPONSE_SUCCESS'), $data);
                } else {
                    return $this->errorResponse(trans('api.general.invalid'), config('constants.RESPONSE_ERROR'));
                }
            }
        }
    }

    public function validateAll($requestData, $social = '', $data = '')
    {
        switch ($social) {
            case 'create':
                return $validation = Validator::make($requestData, [
                    'user_id'  => 'required|integer|exists:users,id',
                    'title'     => 'required',
                    'content'   => 'required',
                    'slug'   => 'required',
                    'category_id' => 'required|integer|exists:categories,id',
                    'image'        => 'required',
                    'tag_id'           => 'required',
                ]);
                break;

            case 'update_post':
                return $validation = Validator::make($requestData, [
                    'user_id'  => 'required|integer|exists:users,id',
                    'id'  => 'required|integer|exists:posts,id',
                    'title'     => 'required',
                    'content'   => 'required',
                ], [
                    'id.required' => 'Post id field required',
                ]);
                break;
            case 'delete_post':
                return $validation = Validator::make(
                    $requestData,
                    [
                        'user_id'   => 'required|integer|exists:users,id',
                        'id'    => 'required|integer|exists:posts,id'
                    ],
                    [
                        'id.required' => 'Post id field required',
                    ]
                );
                break;
            case 'post_comment':
                return $validation = Validator::make($requestData, [
                    'user_id'  => 'required|integer|exists:users,id',
                    'id'  => 'required|integer|exists:posts,id',
                    'comment_content'  => 'required',
                    'commented_by'  => 'required|integer||exists:users,id'
                ], [
                    'id.required' => 'Post id field required',
                ]);
                break;
                //Post List
            default:
                return $validation = Validator::make($requestData, [
                    'user_id'  => 'required|integer|exists:users,id',
                ]);
                break;
        }
    }
}
