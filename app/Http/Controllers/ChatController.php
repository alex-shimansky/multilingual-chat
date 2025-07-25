<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google\Cloud\Translate\V2\TranslateClient;

class ChatController extends Controller
{
    public function index(Request $request)
    {

        // $translate = new TranslateClient([
        //     'keyFilePath' => storage_path('app/google/translate-key.json'),
        // ]);

        // $result = $translate->translate('Hello world', ['target' => 'uk']);

        // echo $result['text'];
        // exit;

        $userLang = Auth::user()?->native_language;

        $messages = Message::with([
            'user',
            'translations' => fn($q) => $q->where('language', $userLang),
        ])
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                $msg->translated_content = $msg->translations->first()->translated_content ?? null;
                return $msg;
            });

        //dd($messages);

        return view('chat', compact('messages', 'userLang'));
    }
}
