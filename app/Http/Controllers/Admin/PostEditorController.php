<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostEditorController extends Controller
{
    /**
     * Render the article exactly as the website will show it.
     */
    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:100000'],
        ]);

        return response()->json([
            'html' => Post::renderMarkdown($validated['body'] ?? '')['html'],
        ]);
    }

    /**
     * Upload an image to use inside an article.
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        $path = $validated['image']->store('blog/content', 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ], 201);
    }
}
