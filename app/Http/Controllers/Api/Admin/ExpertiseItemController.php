<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpertiseItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpertiseItemController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(ExpertiseItem::query()->orderBy('sort_order')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('expertise_items', 'slug')],
            'title' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $item = ExpertiseItem::create($validated);

        return response()->json($item, 201);
    }

    public function show(ExpertiseItem $expertiseItem): JsonResponse
    {
        return response()->json($expertiseItem);
    }

    public function update(Request $request, ExpertiseItem $expertiseItem): JsonResponse
    {
        $validated = $request->validate([
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('expertise_items', 'slug')->ignore($expertiseItem->id),
            ],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $expertiseItem->update($validated);

        return response()->json($expertiseItem);
    }

    public function destroy(ExpertiseItem $expertiseItem): JsonResponse
    {
        $expertiseItem->delete();

        return response()->json(null, 204);
    }
}
