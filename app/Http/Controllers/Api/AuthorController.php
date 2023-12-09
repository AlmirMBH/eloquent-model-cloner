<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Traits\ModelClonerTrait;
use Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{
    use ModelClonerTrait;

    public function clone(int $id): JsonResponse
    {
        $author = Author::findOrFail($id);

        try {
            $clone = $this->duplicate($author);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        $clone = $clone->load([
            'posts' => function ($query) {
                $query->with([
                    'image',
                    'comments.tags.tagType'
                ]);
            },
            'reviews',
        ]);

        return response()->json(['data' => $clone], 201);
    }

    public function show(int $id): JsonResponse
    {

        try {
            $author = Author::with([
                'posts' => function ($query) {
                    $query->with(['image', 'comments.tags.tagType']);
                },
                'reviews',
            ])->findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }

        return response()->json(['data' => $author]);
    }
}
