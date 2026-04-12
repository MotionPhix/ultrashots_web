<?php

namespace App\Http\Controllers;

use App\Ai\Agents\BrandConsultant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Ai\Enums\Lab;

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
        $isNewConversation = false;

        if (! $conversationId) {
            $conversationId = Str::uuid()->toString();
            $isNewConversation = true;
        }

        // Create conversation if it doesn't exist
        if ($isNewConversation) {
            DB::table('agent_conversations')->insert([
                'id' => $conversationId,
                'title' => 'New Brand Consultation',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create agent and prompt it with conversation history
        $agent = new BrandConsultant($conversationId);

        $response = $agent->prompt(
            $request->message,
            provider: Lab::Gemini,
            model: 'gemini-2.5-flash',
        );

        // Parse the structured response
        $structuredResponse = json_decode((string) $response, true);
        $assistantMessage = $structuredResponse['response'] ?? (string) $response;
        $toolUsed = $structuredResponse['tool_used'] ?? null;
        $bookingScheduled = $structuredResponse['booking_scheduled'] ?? false;

        return response()->json([
            'message' => $assistantMessage,
            'conversation_id' => $conversationId,
            'tool_used' => $toolUsed,
            'booking_scheduled' => $bookingScheduled,
        ]);
    }
}
