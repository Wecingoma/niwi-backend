<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Service::query()->orderBy('sort_order')->get());
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $rules = [
                'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('services', 'slug')],
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'features' => ['nullable', 'array'],
                'features.*' => ['string'],
                'benefits' => ['nullable', 'array'],
                'benefits.*' => ['string'],
                'methodology' => ['nullable', 'array'],
                'methodology.*' => ['string'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ];

            $rules['icon'] = $request->hasFile('icon')
                ? ['nullable', 'image', 'max:2048']
                : ['nullable', 'string', 'max:255'];

            $validated = $request->validate($rules);

            if ($request->hasFile('icon')) {
                $validated['icon'] = $this->storePublicFile($request->file('icon'), 'uploads/services');
            }

            $service = Service::create($validated);

            DB::commit();

            return response()->json($service, 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(Service $service): JsonResponse
    {
        return response()->json($service);
    }

    public function update(Request $request, Service $service): JsonResponse
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
                    Rule::unique('services', 'slug')->ignore($service->id),
                ],
                'title' => ['sometimes', 'required', 'string', 'max:255'],
                'description' => ['sometimes', 'required', 'string'],
                'features' => ['nullable', 'array'],
                'features.*' => ['string'],
                'benefits' => ['nullable', 'array'],
                'benefits.*' => ['string'],
                'methodology' => ['nullable', 'array'],
                'methodology.*' => ['string'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ];

            $rules['icon'] = $request->hasFile('icon')
                ? ['nullable', 'image', 'max:2048']
                : ['nullable', 'string', 'max:255'];

            $validated = $request->validate($rules);

            if ($request->hasFile('icon')) {
                $this->deletePublicFile($service->icon);
                $validated['icon'] = $this->storePublicFile($request->file('icon'), 'uploads/services');
            }

            $service->update($validated);

            DB::commit();

            return response()->json($service);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Service $service): JsonResponse
    {
        DB::beginTransaction();

        try {
            $this->deletePublicFile($service->icon);
            $service->delete();

            DB::commit();

            return response()->json(null, 204);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
