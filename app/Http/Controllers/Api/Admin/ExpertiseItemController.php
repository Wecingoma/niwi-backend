<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpertiseItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExpertiseItemController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(ExpertiseItem::query()->orderBy('sort_order')->get());
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $rules = [
                'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('expertise_items', 'slug')],
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ];

            $rules['icon'] = $request->hasFile('icon')
                ? ['nullable', 'image', 'max:2048']
                : ['nullable', 'string', 'max:255'];

            $validated = $request->validate($rules);

            if ($request->hasFile('icon')) {
                $validated['icon'] = $this->storePublicFile($request->file('icon'), 'uploads/expertise-items');
            }

            $item = ExpertiseItem::create($validated);

            DB::commit();

            return response()->json($item, 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(ExpertiseItem $expertiseItem): JsonResponse
    {
        return response()->json($expertiseItem);
    }

    public function update(Request $request, ExpertiseItem $expertiseItem): JsonResponse
    {
        DB::beginTransaction();

        try {
            $rules = [
                'slug' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:255',
                    'alpha_dash',
                    Rule::unique('expertise_items', 'slug')->ignore($expertiseItem->id),
                ],
                'title' => ['sometimes', 'required', 'string', 'max:255'],
                'description' => ['sometimes', 'required', 'string'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ];

            $rules['icon'] = $request->hasFile('icon')
                ? ['nullable', 'image', 'max:2048']
                : ['nullable', 'string', 'max:255'];

            $validated = $request->validate($rules);

            if ($request->hasFile('icon')) {
                $this->deletePublicFile($expertiseItem->icon);
                $validated['icon'] = $this->storePublicFile($request->file('icon'), 'uploads/expertise-items');
            }

            $expertiseItem->update($validated);

            DB::commit();

            return response()->json($expertiseItem);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(ExpertiseItem $expertiseItem): JsonResponse
    {
        DB::beginTransaction();

        try {
            $this->deletePublicFile($expertiseItem->icon);
            $expertiseItem->delete();

            DB::commit();

            return response()->json(null, 204);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
