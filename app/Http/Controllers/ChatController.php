<?php

namespace App\Http\Controllers;

use App\Ai\Agents\BrandConsultant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Ai\Enums\Lab;
use Throwable;

class ChatController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('frontend/chat/Index');
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'conversation_id' => 'nullable|string',
        ]);

        $conversationId = $request->input('conversation_id');

        // Use a guest object for unauthenticated conversations (user_id nullable in DB)
        $guest = new class
        {
            public ?int $id = null;
        };

        $agent = $conversationId
            ? (new BrandConsultant)->continue($conversationId, as: $guest)
            : (new BrandConsultant)->forUser($guest);

        try {
            $response = $agent->prompt(
                $request->message,
                provider: Lab::Gemini,
                model: 'gemini-2.5-flash',
            );
        } catch (Throwable) {
            return response()->json([
                'error' => true,
                'message' => "I'm having trouble connecting right now. Please try again in a moment.",
            ], 503);
        }

        // Parse the structured response
        $structuredResponse = json_decode((string) $response, true);
        $assistantMessage = $structuredResponse['response'] ?? (string) $response;
        $toolUsed = $structuredResponse['tool_used'] ?? null;
        $bookingScheduled = $structuredResponse['booking_scheduled'] ?? false;

        return response()->json([
            'message' => $assistantMessage,
            'conversation_id' => $response->conversationId,
            'tool_used' => $toolUsed,
            'booking_scheduled' => $bookingScheduled,
        ]);
    }
}
