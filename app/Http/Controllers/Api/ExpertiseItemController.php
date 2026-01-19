<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpertiseItem;
use Illuminate\Http\JsonResponse;

class ExpertiseItemController extends Controller
{
    public function index(): JsonResponse
    {
        $items = ExpertiseItem::query()
            ->orderBy('sort_order')
            ->get();

        return response()->json($items);
    }
}
