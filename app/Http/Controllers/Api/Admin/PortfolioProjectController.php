<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortfolioProjectController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(PortfolioProject::query()->orderBy('sort_order')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'back_text' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $project = PortfolioProject::create($validated);

        return response()->json($project, 201);
    }

    public function show(PortfolioProject $portfolioProject): JsonResponse
    {
        return response()->json($portfolioProject);
    }

    public function update(Request $request, PortfolioProject $portfolioProject): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'back_text' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $portfolioProject->update($validated);

        return response()->json($portfolioProject);
    }

    public function destroy(PortfolioProject $portfolioProject): JsonResponse
    {
        $portfolioProject->delete();

        return response()->json(null, 204);
    }
}
