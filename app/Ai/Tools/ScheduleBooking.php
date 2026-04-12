<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ScheduleBooking implements Tool
{
    /**
     * Get the name of the tool.
     */
    public function name(): string
    {
        return 'schedule_booking';
    }

    /**
     * Get the description of the tool.
     */
    public function description(): string
    {
        return 'Schedule a brand consultation meeting with the client. Use this when the client expresses interest in working together or asks about booking a call.';
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'client_name' => $schema->string(),
            'client_email' => $schema->string(),
            'preferred_date' => $schema->string(),
            'preferred_time' => $schema->string(),
            'project_type' => $schema->string(),
        ];
    }

    /**
     * Handle the tool execution.
     */
    public function handle(Request $request): Stringable|string
    {
        $parameters = $request->all();

        // Generate a unique booking reference
        $bookingReference = 'BK-'.strtoupper(substr(md5(uniqid()), 0, 8));

        // Store the booking request (you can integrate with your calendar system)
        $booking = [
            'reference' => $bookingReference,
            'client_name' => $parameters['client_name'],
            'client_email' => $parameters['client_email'],
            'preferred_date' => $parameters['preferred_date'] ?? 'To be confirmed',
            'preferred_time' => $parameters['preferred_time'] ?? 'To be confirmed',
            'project_type' => $parameters['project_type'] ?? 'Brand consultation',
            'status' => 'pending_confirmation',
            'created_at' => now()->toIso8601String(),
        ];

        return "Booking request submitted successfully! Reference: {$bookingReference}. We'll send a confirmation email to {$parameters['client_email']} shortly.";
    }
}
