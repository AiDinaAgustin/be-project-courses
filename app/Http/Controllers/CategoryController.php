<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        
        return response()->json([
            'message' => 'Categories retrieved successfully',
            'status' => 200,
            'data' => $categories->items(),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $category = $this->categoryService->createCategory($data);
        
        return response()->json([
            'message' => 'Category created successfully',
            'status' => 201,
            'data' => $category
        ], 201);
    }

    public function show($id)
    {
        $category = $this->categoryService->getCategoryById($id);
        
        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
                'status' => 404,
                'data' => null
            ], 404);
        }
        
        return response()->json([
            'message' => 'Category retrieved successfully',
            'status' => 200,
            'data' => $category
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $category = $this->categoryService->updateCategory($id, $data);
        
        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
                'status' => 404,
                'data' => null
            ], 404);
        }
        
        return response()->json([
            'message' => 'Category updated successfully',
            'status' => 200,
            'data' => $category
        ]);
    }

    public function destroy($id)
    {
        $deleted = $this->categoryService->deleteCategory($id);
        
        return response()->json([
            'message' => $deleted ? 'Category deleted successfully' : 'Category not found',
            'status' => $deleted ? 200 : 404,
            'data' => ['deleted' => $deleted]
        ], $deleted ? 200 : 404);
    }
}