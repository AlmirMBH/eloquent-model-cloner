<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Traits\ModelClonerTrait;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    use ModelClonerTrait;


    public function clone(int $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $post->refresh();

        try {
            $clone = $this->duplicate($post);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        $clone = $clone->load([
            'image',
            'comments.tags.tagType'
        ]);

        return response()->json(['data' => $clone], 201);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $post = Post::with([
                'image',
                'comments.tags.tagType'])
                ->findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }

        return response()->json(['data' => $post]);
    }
}
