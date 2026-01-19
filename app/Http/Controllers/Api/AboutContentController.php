<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use Illuminate\Http\JsonResponse;

class AboutContentController extends Controller
{
    public function show(): JsonResponse
    {
        $content = AboutContent::query()->first();

        return response()->json($content);
    }
}
