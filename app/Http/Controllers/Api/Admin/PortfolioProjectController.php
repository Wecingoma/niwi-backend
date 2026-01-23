<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PortfolioProjectController extends Controller
{
    // --- Retourne tous les projets ---
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => PortfolioProject::query()->orderBy('sort_order')->get(),
        ]);
    }

    // --- Crée un nouveau projet avec image ---
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'title'       => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'back_text'   => ['nullable', 'string'],
                'sort_order'  => ['nullable', 'integer', 'min:0'],
                'image'       => ['nullable', 'image', 'max:2048'],
            ]);

            // Upload image
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('portfolio-projects', 'public');
            }

            $project = PortfolioProject::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $project,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // --- Affiche un projet ---
    public function show(PortfolioProject $portfolioProject): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $portfolioProject
        ]);
    }

    // --- Met à jour un projet ---
    public function update(Request $request, PortfolioProject $portfolioProject): JsonResponse
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'title'       => ['sometimes', 'required', 'string', 'max:255'],
                'description' => ['sometimes', 'required', 'string'],
                'back_text'   => ['nullable', 'string'],
                'sort_order'  => ['nullable', 'integer', 'min:0'],
                'image'       => ['nullable', 'image', 'max:2048'],
            ]);

            // Upload nouvelle image si présente
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image
                if ($portfolioProject->image && Storage::disk('public')->exists($portfolioProject->image)) {
                    Storage::disk('public')->delete($portfolioProject->image);
                }
                $validated['image'] = $request->file('image')->store('portfolio-projects', 'public');
            }

            $portfolioProject->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $portfolioProject
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // --- Supprime un projet et son image ---
    public function destroy(PortfolioProject $portfolioProject): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Supprimer l'image associée
            if ($portfolioProject->image && Storage::disk('public')->exists($portfolioProject->image)) {
                Storage::disk('public')->delete($portfolioProject->image);
            }

            $portfolioProject->delete();

            DB::commit();

            return response()->json([
                'success' => true,
            ], 204);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
