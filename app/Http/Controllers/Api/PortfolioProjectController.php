<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PortfolioProject;
use Illuminate\Http\JsonResponse;

class PortfolioProjectController extends Controller
{
    public function index(): JsonResponse
    {
        $projects = PortfolioProject::query()
            ->orderBy('sort_order')
            ->get();

        return response()->json($projects);
    }
}
