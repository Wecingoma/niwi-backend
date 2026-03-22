<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $conversations = Conversation::query()
            ->with(['user:id,name,email', 'latestMessage.sender:id,name,email,role'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get();

        return response()->json($conversations);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $user = $request->user();

        $conversation = DB::transaction(function () use ($user, $validated): Conversation {
            $conversation = Conversation::create([
                'user_id' => $user->id,
                'last_message_at' => now(),
            ]);

            ConversationMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'message' => $validated['message'],
            ]);

            return $conversation;
        });

        return response()->json(
            $conversation->load(['user:id,name,email', 'latestMessage.sender:id,name,email,role']),
            201
        );
    }

    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        abort_unless($conversation->user_id === $request->user()->id, 403, 'Forbidden.');

        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $request->user()->id)
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
        abort_unless($conversation->user_id === $request->user()->id, 403, 'Forbidden.');

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
