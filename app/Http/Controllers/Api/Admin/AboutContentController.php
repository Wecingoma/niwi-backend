<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AboutContentController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(AboutContent::query()->first());
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'highlight' => ['nullable', 'string', 'max:255'],
            'intro' => ['sometimes', 'required', 'string'],
            'mission_title' => ['nullable', 'string', 'max:255'],
            'mission_text' => ['nullable', 'string'],
            'vision_title' => ['nullable', 'string', 'max:255'],
            'vision_text' => ['nullable', 'string'],
            'approach_title' => ['nullable', 'string', 'max:255'],
            'approach_text' => ['nullable', 'string'],
            'services_title' => ['nullable', 'string', 'max:255'],
            'services_list' => ['nullable', 'array'],
            'services_list.*' => ['string'],
            'cta_label' => ['nullable', 'string', 'max:255'],
            'cta_link' => ['nullable', 'string', 'max:255'],
        ]);

        $content = AboutContent::query()->first();

        if (! $content) {
            $content = AboutContent::create($validated);
        } else {
            $content->update($validated);
        }

        return response()->json($content);
    }
}
