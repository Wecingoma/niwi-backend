<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactInfoController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(ContactInfo::query()->first());
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'hours' => ['nullable', 'string', 'max:255'],
            'map_embed_url' => ['nullable', 'string'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['string'],
        ]);

        $info = ContactInfo::query()->first();

        if (! $info) {
            $info = ContactInfo::create($validated);
        } else {
            $info->update($validated);
        }

        return response()->json($info);
    }
}
