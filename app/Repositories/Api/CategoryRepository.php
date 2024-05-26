<?php

namespace App\Repositories\Api;

use App\Models\Category;

class CategoryRepository
{

    protected $common;


    public function checkExists($post)
    {
        $data = Category::select('*')
            ->where('category_name', $post['category_name'])
            ->first();
        return $data;
    }

    /** To create a category */
    public function create($post)
    {
        $datanew['category_name'] = $post['category_name'];
        $datanew['created_at']          = date('Y-m-d H:i:s');
        $datanew['updated_at']          = date('Y-m-d H:i:s');
        Category::insert($datanew);
        $lastid = Category::latest()->first();
        if ($lastid) {
            $categoryData = Category::find($lastid);
            return $categoryData;
        }else {
            return false;
        }
    }
    /**
     * Get Category Types
     *
     * @return mix
     */
    public function list()
    {
        return Category::get()->toArray();
    }
}
