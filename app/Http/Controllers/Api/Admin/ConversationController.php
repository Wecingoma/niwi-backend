<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function index(): JsonResponse
    {
        $conversations = Conversation::query()
            ->with(['user:id,name,email', 'latestMessage.sender:id,name,email,role'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get();

        return response()->json($conversations);
    }

    public function show(Conversation $conversation): JsonResponse
    {
        $conversation->messages()
            ->whereNull('read_at')
            ->whereHas('sender', fn ($query) => $query->where('role', 'user'))
            ->update(['read_at' => now()]);

        return response()->json(
            $conversation->load([
                'user:id,name,email',
                'messages.sender:id,name,email,role',
            ])
        );
    }

    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $message = DB::transaction(function () use ($request, $conversation, $validated): ConversationMessage {
            $message = ConversationMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $request->user()->id,
                'message' => $validated['message'],
            ]);

            $conversation->update([
                'last_message_at' => $message->created_at,
            ]);

            return $message;
        });

        return response()->json($message->load('sender:id,name,email,role'), 201);
    }
}
