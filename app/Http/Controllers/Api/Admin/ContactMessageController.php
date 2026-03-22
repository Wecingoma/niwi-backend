<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    public function index(): JsonResponse
    {
        $messages = ContactMessage::query()
            ->with('repliedBy:id,name,email')
            ->latest()
            ->get();

        return response()->json($messages);
    }

    public function show(ContactMessage $contactMessage): JsonResponse
    {
        return response()->json($contactMessage->load('repliedBy:id,name,email'));
    }

    public function reply(Request $request, ContactMessage $contactMessage): JsonResponse
    {
        $validated = $request->validate([
            'reply' => ['required', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($request, $contactMessage, $validated): void {
            Mail::raw($validated['reply'], function ($message) use ($contactMessage): void {
                $message
                    ->to($contactMessage->email, $contactMessage->name)
                    ->subject('Reponse a votre message');
            });

            $contactMessage->update([
                'admin_reply' => $validated['reply'],
                'replied_at' => now(),
                'replied_by' => $request->user()->id,
            ]);
        });

        return response()->json($contactMessage->fresh()->load('repliedBy:id,name,email'));
    }

    public function destroy(ContactMessage $contactMessage): JsonResponse
    {
        $contactMessage->delete();

        return response()->json(null, 204);
    }
}
