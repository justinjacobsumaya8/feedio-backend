<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json([
            "data" => Category::all()
        ]);
    }

    public function show(Request $request, $id)
    {
        $category = Category
            ::with(['articles' => function ($articleQuery) {
                $articleQuery->generalQuery();
            }])
            ->find($id);

        if (!$category) {
            return response()->json([
                'message' => "Category not found"
            ], 404);
        }

        return response()->json([
            'data' => $category
        ]);
    }
}
