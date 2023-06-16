<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArticleAuthor;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function show(Request $request, $id)
    {
        $author = Author::with(['articleAuthors.article' => function ($q) {
            $q->generalQuery();
        }])
            ->find($id);

        if (!$author) {
            return response()->json([
                'message' => "Author not found"
            ], 404);
        }

        return response()->json([
            'data' => $author
        ]);
    }
}
