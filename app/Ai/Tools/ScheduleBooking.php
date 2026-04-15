<?php

namespace App\Ai\Tools;

use App\Events\QuoteRequested;
use App\Models\Quote;
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
            'client_name' => $schema->string()->required(),
            'client_email' => $schema->string()->required(),
            'project_type' => $schema->string()->required(),
            'preferred_date' => $schema->string(),
            'preferred_time' => $schema->string(),
        ];
    }

    /**
     * Handle the tool execution.
     */
    public function handle(Request $request): Stringable|string
    {
        $preferredDate = $request->string('preferred_date')->value();
        $preferredTime = $request->string('preferred_time')->value();

        $timeline = match (true) {
            $preferredDate !== '' && $preferredTime !== '' => "{$preferredDate} at {$preferredTime}",
            $preferredDate !== '' => $preferredDate,
            default => 'Flexible',
        };

        $quote = Quote::create([
            'name' => $request->string('client_name'),
            'email' => $request->string('client_email'),
            'project_type' => $request->string('project_type'),
            'timeline' => $timeline,
            'description' => 'Brand consultation booking requested via chat.',
            'status' => 'pending',
        ]);

        QuoteRequested::dispatch($quote);

        return "Booking request submitted successfully! We'll be in touch with {$quote->email} shortly to confirm your consultation.";
    }
}
