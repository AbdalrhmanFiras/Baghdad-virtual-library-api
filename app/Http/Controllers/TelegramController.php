<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();

        Log::info('Telegram Webhook HIT: '.json_encode($data));

        if (isset($data['message'])) {
            $chatId = $data['message']['chat']['id'];
            $text = $data['message']['text'] ?? '';

            file_get_contents('https://api.telegram.org/bot'.config('telegram.bot_token')."/sendMessage?chat_id=$chatId&text=".urlencode("وصلت رسالتك: $text"));
        }

        return response()->json(['ok' => true]);
    }
}
