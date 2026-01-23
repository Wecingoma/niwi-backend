<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    // --- Retourne toutes les slides ---
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => HeroSlide::orderBy('sort_order')->get(),
        ]);
    }

    // --- Stocke une nouvelle slide avec images ---
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'title'       => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'cta'         => ['required', 'string', 'max:255'],
                'images'      => ['nullable', 'array'],
                'images.*'    => ['image', 'max:2048'],
                'sort_order'  => ['nullable', 'integer', 'min:0'],
            ]);

            // Stockage des images
            if ($request->hasFile('images')) {
                $paths = [];
                foreach ($request->file('images') as $image) {
                    $paths[] = $image->store('hero-slides', 'public'); // stockage dans storage/app/public/hero-slides
                }
                $validated['images'] = $paths;
            }

            $slide = HeroSlide::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $slide,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // --- Met à jour une slide ---
    public function update(Request $request, HeroSlide $heroSlide): JsonResponse
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'title'       => ['sometimes', 'required', 'string', 'max:255'],
                'description' => ['sometimes', 'required', 'string'],
                'cta'         => ['sometimes', 'required', 'string', 'max:255'],
                'images'      => ['nullable', 'array'],
                'images.*'    => ['image', 'max:2048'],
                'sort_order'  => ['nullable', 'integer', 'min:0'],
            ]);

            // Si de nouvelles images sont uploadées
            if ($request->hasFile('images')) {
                // Supprimer uniquement les images remplacées
                $existingImages = $heroSlide->images ?? [];
                foreach ($existingImages as $imgPath) {
                    if (Storage::disk('public')->exists($imgPath)) {
                        Storage::disk('public')->delete($imgPath);
                    }
                }

                $paths = [];
                foreach ($request->file('images') as $image) {
                    $paths[] = $image->store('hero-slides', 'public');
                }
                $validated['images'] = $paths;
            }

            $heroSlide->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $heroSlide,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // --- Supprime une slide et ses images ---
    public function destroy(HeroSlide $heroSlide): JsonResponse
    {
        DB::beginTransaction();

        try {
            $existingImages = $heroSlide->images ?? [];
            foreach ($existingImages as $imgPath) {
                if (Storage::disk('public')->exists($imgPath)) {
                    Storage::disk('public')->delete($imgPath);
                }
            }

            $heroSlide->delete();

            DB::commit();

            return response()->json([
                'success' => true,
            ], 204);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
