<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TeamMember::query()->orderBy('sort_order')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'theme' => ['nullable', 'string', 'max:50'],
            'reverse' => ['nullable', 'boolean'],
            'summary' => ['nullable', 'array'],
            'summary.*' => ['string'],
            'skills' => ['nullable', 'string'],
            'contact' => ['nullable', 'string'],
            'is_carousel' => ['nullable', 'boolean'],
            'socials' => ['nullable', 'array'],
            'socials.*' => ['string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $member = TeamMember::create($validated);

        return response()->json($member, 201);
    }

    public function show(TeamMember $teamMember): JsonResponse
    {
        return response()->json($teamMember);
    }

    public function update(Request $request, TeamMember $teamMember): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'role' => ['sometimes', 'required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'theme' => ['nullable', 'string', 'max:50'],
            'reverse' => ['nullable', 'boolean'],
            'summary' => ['nullable', 'array'],
            'summary.*' => ['string'],
            'skills' => ['nullable', 'string'],
            'contact' => ['nullable', 'string'],
            'is_carousel' => ['nullable', 'boolean'],
            'socials' => ['nullable', 'array'],
            'socials.*' => ['string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $teamMember->update($validated);

        return response()->json($teamMember);
    }

    public function destroy(TeamMember $teamMember): JsonResponse
    {
        $teamMember->delete();

        return response()->json(null, 204);
    }
}
