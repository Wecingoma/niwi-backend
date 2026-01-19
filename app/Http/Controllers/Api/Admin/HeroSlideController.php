<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HeroSlideController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(HeroSlide::query()->orderBy('sort_order')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'cta' => ['required', 'string', 'max:255'],
            'images' => ['nullable', 'array'],
            'images.*' => ['string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $slide = HeroSlide::create($validated);

        return response()->json($slide, 201);
    }

    public function show(HeroSlide $heroSlide): JsonResponse
    {
        return response()->json($heroSlide);
    }

    public function update(Request $request, HeroSlide $heroSlide): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'cta' => ['sometimes', 'required', 'string', 'max:255'],
            'images' => ['nullable', 'array'],
            'images.*' => ['string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $heroSlide->update($validated);

        return response()->json($heroSlide);
    }

    public function destroy(HeroSlide $heroSlide): JsonResponse
    {
        $heroSlide->delete();

        return response()->json(null, 204);
    }
}
