<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\WebpImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostEditorController extends Controller
{
    public function __construct(private WebpImageStore $images) {}

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

        $path = $this->images->store($validated['image'], 'blog/content');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ], 201);
    }
}
