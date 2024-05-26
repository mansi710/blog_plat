<?php

namespace App\Repositories\Api;

use App\Models\Tag;
class TagRepository
{

    protected $common;


    public function checkExists($post)
    {
        $data = Tag::select('*')
            ->where('tag_name', $post['tag_name'])
            ->first();
        return $data;
    }

    /** To create a tag */
    public function create($post)
    {
        $datanew['tag_name'] = $post['tag_name'];
        $datanew['created_at']          = date('Y-m-d H:i:s');
        $datanew['updated_at']          = date('Y-m-d H:i:s');
        Tag::insert($datanew);
        $lastid = Tag::latest()->first();
        if ($lastid) {
            $tagData = Tag::find($lastid);
            return $tagData;
        }else {
            return false;
        }
    }
    /**
     * Get Tags
     *
     * @return mix
     */
    public function list()
    {
        return Tag::get()->toArray();
    }
}
