<?php

namespace App\Http\Controllers\Api;

use App\Events\ProjectReacted;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectReactionRequest;
use App\Models\Project;
use App\Models\Reaction;
use Illuminate\Http\JsonResponse;

class ReactionController extends Controller
{
    public function store(StoreProjectReactionRequest $request, Project $project): JsonResponse
    {
        $validated = $request->validated();
        $visitorEmail = $validated['visitor_email'] ?? null;

        if ($visitorEmail && Reaction::query()
            ->where('project_id', $project->id)
            ->where('type', $validated['type'])
            ->where('visitor_email', $visitorEmail)
            ->exists()) {
            return response()->json(['message' => 'You have already reacted to this project'], 409);
        }

        $reaction = $project->reactions()->create([
            'type' => $validated['type'],
            'feedback' => $validated['feedback'] ?? null,
            'visitor_name' => $validated['visitor_name'] ?? 'Anonymous',
            'visitor_email' => $visitorEmail,
        ]);

        ProjectReacted::dispatch($reaction);

        return response()->json([
            'message' => 'Reaction added successfully',
            'reaction' => $reaction,
        ], 201);
    }

    public function getReactions(Project $project): JsonResponse
    {
        $reactions = $project->reactions()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get();

        return response()->json([
            'project_id' => $project->id,
            'reactions' => $reactions,
        ]);
    }
}
