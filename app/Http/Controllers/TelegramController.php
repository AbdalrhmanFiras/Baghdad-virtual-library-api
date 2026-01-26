<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Telegram Webhook HIT: '.json_encode($request->all()));

        return response()->json(['ok' => true]);
    }
}
