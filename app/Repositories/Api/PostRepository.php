<?php

namespace App\Repositories\Api;

use App\Models\Post;
use App\Models\postMedia;
use App\Models\PostTag;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\CommonRepository;
use DB;
use Auth;

class PostRepository
{

    protected $common;
    public function __construct(CommonRepository $common)
    {
        $this->common = $common;
    }

    /** METHOD FOR ADD POST HERE **/
    public function create($post)
    {
        $data = [
            'user_id'     => $post['user_id'],
            'title'              => $post['title'],
            'content'              => $post['content'],
            'slug'              => $post['slug'],
            'category_id'     => $post['category_id'],
            'created_at'        => date('Y-m-d H:i:s'),
            'published'    => isset($post['published']) ? $post['published'] : 1,
        ];

        DB::table('posts')->insert($data);
        $lastid = DB::table('posts')->latest()->first();
        if ($lastid) {
            // Explode the string of tag IDs
            if (is_array($post['tag_id']) && isset($post['tag_id'][0])) {
                // Extract the string from the array
                $tagIdString = $post['tag_id'][0];

                // Explode the string of tag IDs
                $tagIds = explode(',', $tagIdString);

                // Loop through each tag ID
                foreach ($tagIds as $tagId) {
                    // Insert record into post_tags table
                    DB::table('post_tags')->insert([
                        'post_id' => $lastid->id,
                        'tag_id' => $tagId,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            if (isset($_FILES['image']['name'][0]) && $_FILES['image']['name'][0] != "") {
                $path  = config('path.publicpath') . '/post_media/' . $lastid->id;
                foreach ($post['image'] as $spimg) {
                    $filename  = $this->common->uploadFiles($spimg, $path);

                    $media = array('post_id' => $lastid->id, 'image' => $filename, 'created_at' => date('Y-m-d H:i:s'));
                    DB::table('post_medias')->insert($media);
                }
            }
            $postData = Post::find($lastid->id);
            return $postData;
            // return $lastid->id;
        } else {
            return false;
        }
    }

    /** METHOD FOR LISTING POST HERE **/

    public function list($post)
    {
        $logged_in_user_id = Auth::user()->id;
        $final_data  = array();
        if ($logged_in_user_id == $post['user_id']) {
            $result = DB::table('posts')->select('*')->where('published',1)->orderBy('id', 'desc')->get()->toArray();
        }else{
            $result = DB::table('posts')->select('*')->orderBy('id', 'desc')->get()->toArray();
        }
        $resultdata = json_decode(json_encode($result), true);
        if (!empty($resultdata)) {

            foreach ($resultdata as $resultdata) {
                $created_by = User::select('*')->where('id', $resultdata['user_id'])->first();


                if (isset($post['user_id']) && $post['user_id'] != "") {

                    $resultdata['username'] = $created_by['name'];
                }
                // Display "Yes" for published value of 1 and "No" otherwise
                $resultdata['published'] = $resultdata['published'] == 1 ? 'Yes' : 'No';
                $postmedialisting = $this->postmedialisting($resultdata['id']);
                $posttaglisting = $this->posttaglist($resultdata['id']);
                $mediasdata = array();
                $tagsdata = array();
                foreach ($posttaglisting as $posttag) {
                    $tag_name = Tag::select('*')->where('id', $posttag['tag_id'])->first();
                    if ($tag_name) {
                        $tagsdata[] = $tag_name;
                    }
                }
                $resultdata['post_tags'] = $tagsdata;
                foreach ($postmedialisting as $postmedia) {
                    $image          = !empty($postmedia['image'] != '') ? config('path.imgpath') . '/post_media/' . $postmedia['post_id'] . '/' . $postmedia['image'] : '';

                    $imagepath = config('path.publicpath') . '/post_media/';

                    if (!empty($postmedia['image'])) {
                        $ipath = $imagepath . $postmedia['post_id'] . '/' . $postmedia['image'];
                        if (!is_dir($ipath)) {
                            $datahw = getimagesize($ipath);
                            if (!empty($datahw)) {
                                $width  = $datahw[0] != null  ? $datahw[0] : 0;
                                $height = $datahw[1] != null  ? $datahw[1] : 0;
                                $aspect_ratio = $width / $height;
                            } else {
                                $width          = 0;
                                $height         = 0;
                                $aspect_ratio   = 0;
                            }
                        } else {
                            $width          = 0;
                            $height         = 0;
                            $aspect_ratio   = 0;
                        }
                    } else {
                        $width          = 0;
                        $height         = 0;
                        $aspect_ratio   = 0;
                    }

                    $mediasdata[] = array_merge($postmedia, array("image" => $image, "width" => $width, "height" => $height, "aspect_ratio" => $aspect_ratio));
                }
                $resultdata['post_media'] = $mediasdata;
                $final_data[] = $resultdata;
            }
        }
        return $final_data;
    }

    /** METHOD FOR CHECK IF POST PPRESENT OR NOT FROM HERE **/
    public function checkExistspost($post)
    {
        $post = DB::table('posts')->where('id', $post['id'])->count();
        if ($post) {
            return 1;
        } else {
            return 0;
        }
    }

    /**METHOD FOR UPDATE POST **/
    public function update_post($post)
    {
        $logged_in_user_id = $post['user_id'];
        $checkPost = DB::table('posts')->where('id', $post['id'])->first();
        if ($checkPost) {
            if ($checkPost->user_id == $logged_in_user_id) {
                $data['title']          = isset($post['title']) ? $post['title'] : $checkPost['title'];
                $data['content']    = isset($post['title']) ? $post['content'] : $checkPost['content'];
                $data['published']    = isset($post['published']) ? $post['published'] : $checkPost['published'];

                $data['updated_at']     =  date('Y-m-d H:i:s');
                $upresult = DB::table('posts')->where('id', $post['id'])->update($data);
                if ($upresult) {
                    if (isset($_FILES['image']['name'][0]) && $_FILES['image']['name'][0] != "") {
                        $path  = config('path.publicpath') . '/post_media/' . $post['id'];

                        // Display "Yes" for published value of 1 and "No" otherwise
                        $checkPost['published'] = $checkPost['published'] == 1 ? 'Yes' : 'No';
                        foreach ($post['image'] as $spimg) {
                            $filename  = $this->common->uploadFiles($spimg, $path);

                            $media = array('post_id' => $post['id'], 'image' => $filename, 'created_at' => date('Y-m-d H:i:s'));
                            DB::table('post_medias')->insert($media);
                        }
                    }
                    if (is_array($post['tag_id']) && isset($post['tag_id'][0])) {
                        // Extract the string from the array
                        $tagIdString = $post['tag_id'][0];

                        // Explode the string of tag IDs
                        $tagIds = explode(',', $tagIdString);

                        // Loop through each tag ID
                        foreach ($tagIds as $tagId) {
                            // Insert record into post_tags table
                            DB::table('post_tags')->insert([
                                'post_id' => $post['id'],
                                'tag_id' => $tagId,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                    return true;
                } else {
                    return false;
                }
            } else {
                return 403;
            }
        }


        // $data['title']          = $post['title'];
        // $data['description']    = $post['description'];
        // $data['updated_at']     =  date('Y-m-d H:i:s');


    }
    /** METHOD FOR DELETE POST WITH IMAGE / VIDEO **/
    public function delete_post($post)
    {
        $logged_in_user_id = $post['user_id'];
        $unlinkFiles = DB::table('post_medias')->where('post_id', $post['id'])->select('*')->first();
        $post = DB::table('posts')->where('id', $post['id'])->first();
        if ($post) {
            if ($post->user_id == $logged_in_user_id) {
                if ($unlinkFiles) {
                    $unlinkFolderPath = config('path.publicpath') . '/post_media/' . $post->id;
                    $data = $this->common->removeFolder($unlinkFolderPath);
                    $unlinkFiles = DB::table('post_medias')->where('post_id', $post->id)->delete();
                }

                $remove_post_tag = PostTag::where('post_id', $post->id)->delete();
                $delete_post = DB::table('posts')->where('id', $post->id)->delete();

                return true;
            } else {
                return 403;
            }
        }
        return false;
    }

    /** METHOD FOR LISTING/GETTING POST HERE **/
    public function postmedialisting($post_id)
    {
        $result =  DB::table('post_medias')->where('post_id', $post_id)->select('*')->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /** METHOD FOE LISTING/GETTING POST TAG HERE */
    public function posttaglist($post_id)
    {
        $result =  DB::table('post_tags')->where('post_id', $post_id)->select('*')->get()->toArray();
        return json_decode(json_encode($result), true);
    }



    /*========================================= COMMNET PORTION STARTS FROM HERE ========================================= */
    /** ADD COMMENT ON POST **/
    public function post_comment($post)
    {
        $data = array(
            'post_id'    => $post['id'],
            'comment_content'  => $post['comment_content'],
            'commented_by'  =>$post['commented_by'],
            'created_at' => date('Y-m-d H:i:s'),
        );
        DB::table('comments')->insert($data);
        $result = DB::table('comments')->latest()->first();
        if ($result) {
            return $this->lastcomment($post['user_id'], $result->id);
        } else {
            return false;
        }
    }

    /** FETCH LAST COMMENT ON POST **/

    public function lastcomment($user_id, $id)
    {
        $final_data = array();
        $result     = DB::table('comments')->where('comments.id', $id)->join('users', 'comments.commented_by', '=', 'users.id')->select('comments.*', 'users.name')->first();
        $resultdata = json_decode(json_encode($result), true);

        if (!empty($resultdata)) {
            $final_data = $resultdata;
        }
        return $final_data;
    }
}
