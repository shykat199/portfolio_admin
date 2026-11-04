<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMessageRequest;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    /**
     * Save an enquiry sent from the website's contact form.
     */
    public function __invoke(StoreMessageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_agent'] = Str::limit((string) ($data['user_agent'] ?? ''), 297) ?: null;

        $message = Message::create($data);

        return response()->json(['data' => ['id' => $message->id]], 201);
    }
}
