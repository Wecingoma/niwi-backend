<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = TeamMember::query()->orderBy('sort_order');

        if ($request->boolean('carousel')) {
            $query->where('is_carousel', true);
        }

        return response()->json($query->get());
    }
}
