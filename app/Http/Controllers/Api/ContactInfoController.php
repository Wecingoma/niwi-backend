<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactInfo;
use Illuminate\Http\JsonResponse;

class ContactInfoController extends Controller
{
    public function show(): JsonResponse
    {
        $info = ContactInfo::query()->first();

        return response()->json($info);
    }
}
