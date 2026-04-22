<?php

namespace App\Http\Controllers;

use App\Services\WmsAiChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAiController extends Controller
{
    public function store(Request $request, WmsAiChatService $chatService): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'page_context' => ['nullable', 'string', 'max:120'],
            'previous_response_id' => ['nullable', 'string', 'max:255'],
        ]);

        return response()->json(
            $chatService->respond(
                $validated['message'],
                $validated['page_context'] ?? null,
                $validated['previous_response_id'] ?? null,
            )
        );
    }
}
