<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function getAll()
    {
        return Category::latest()->paginate(10); 
    }

    public function findById($id)
    {
        return Category::find($id);  // Changed from findOrFail to handle not found in controller
    }

    public function create(array $data)
    {
        return Category::create($data);
    }

    public function update($id, array $data)
    {
        $category = Category::find($id);
        if (!$category) {
            return null;
        }
        $category->update($data);
        return $category;
    }

    public function delete($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return false;
        }
        return $category->delete();
    }
}