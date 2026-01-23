<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeamMemberController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => TeamMember::orderBy('sort_order')->get()
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            Log::info('=== CREATE TEAM MEMBER START ===');

            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'role'        => 'required|string|max:255',
                'theme'       => 'nullable|string|max:50',
                'reverse'     => 'nullable|boolean',
                'summary'     => 'nullable|array',
                'summary.*'   => 'string',
                'skills'      => 'nullable|string',
                'contact'     => 'nullable|string',
                'is_carousel' => 'nullable|boolean',
                'socials'     => 'nullable|array',
                'socials.*'   => 'string',
                'sort_order'  => 'nullable|integer|min:0',
                'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            // 1. Créer le membre sans image
            $member = TeamMember::create($validated);

            // 2. Gérer l’upload image
            if ($request->hasFile('image')) {
                $image     = $request->file('image');
                $extension = $image->getClientOriginalExtension();
                $safeName  = Str::slug($member->name);
                $fileName  = $safeName . '_' . time() . '.' . $extension;

                $storagePath = "team-members/{$member->id}";

                Storage::disk('public')->makeDirectory($storagePath);

                $path = $image->storeAs($storagePath, $fileName, 'public');

                $member->update([
                    'image' => $path
                ]);
            }

            DB::commit();

            Log::info('=== CREATE TEAM MEMBER SUCCESS ===', [
                'id' => $member->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Membre ajouté avec succès',
                'data'    => $member->fresh()
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('CREATE TEAM MEMBER ERROR', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du membre'
            ], 500);
        }
    }

    public function show(TeamMember $teamMember): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $teamMember
        ]);
    }

    public function update(Request $request, TeamMember $teamMember): JsonResponse
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'name'        => 'sometimes|string|max:255',
                'role'        => 'sometimes|string|max:255',
                'theme'       => 'nullable|string|max:50',
                'reverse'     => 'nullable|boolean',
                'summary'     => 'nullable|array',
                'summary.*'   => 'string',
                'skills'      => 'nullable|string',
                'contact'     => 'nullable|string',
                'is_carousel' => 'nullable|boolean',
                'socials'     => 'nullable|array',
                'socials.*'   => 'string',
                'sort_order'  => 'nullable|integer|min:0',
                'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            // Upload nouvelle image
            if ($request->hasFile('image')) {
                // Supprimer l’ancienne
                if ($teamMember->image && Storage::disk('public')->exists($teamMember->image)) {
                    Storage::disk('public')->delete($teamMember->image);
                }

                $image     = $request->file('image');
                $extension = $image->getClientOriginalExtension();
                $safeName  = Str::slug($validated['name'] ?? $teamMember->name);
                $fileName  = $safeName . '_' . time() . '.' . $extension;

                $storagePath = "team-members/{$teamMember->id}";
                Storage::disk('public')->makeDirectory($storagePath);

                $validated['image'] = $image->storeAs($storagePath, $fileName, 'public');
            }

            $teamMember->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Membre mis à jour avec succès',
                'data'    => $teamMember
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('UPDATE TEAM MEMBER ERROR', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }

    public function destroy(TeamMember $teamMember): JsonResponse
    {
        DB::beginTransaction();

        try {
            if ($teamMember->image && Storage::disk('public')->exists($teamMember->image)) {
                Storage::disk('public')->delete($teamMember->image);
            }

            Storage::disk('public')->deleteDirectory("team-members/{$teamMember->id}");

            $teamMember->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Membre supprimé avec succès'
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('DELETE TEAM MEMBER ERROR', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }
}
