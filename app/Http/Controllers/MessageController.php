<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\MessageTranslation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google\Cloud\Translate\V2\TranslateClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class MessageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        $targetLanguages = User::query()
            ->whereNotNull('native_language')
            ->where('native_language', '!=', Auth::user()->native_language)
            ->distinct()
            ->pluck('native_language')
            ->toArray();

        $userLang = Auth::user()?->native_language;

        $translate = new TranslateClient([
            'keyFilePath' => storage_path('app/google/translate-key.json'),
        ]);

        foreach ($targetLanguages as $lang) {
            if ($lang !== $userLang) {
                $translated = $translate->translate($message->content, ['target' => $lang]);

                MessageTranslation::create([
                    'message_id' => $message->id,
                    'language' => $lang,
                    'translated_content' => $translated['text'],
                ]);
            }
        }

        try {
            broadcast(new MessageSent($message));
        } catch (Throwable $e) {
            Log::error('Broadcast failed', ['message' => $e->getMessage()]);
        }

        return response()->json(['success' => true]);
    }
}
