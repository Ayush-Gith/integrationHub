<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\WebhookReceived;

class WebhookController extends Controller
{
    public function index()
    {
        try {
            return response()->json(Webhook::latest()->paginate(20));
        } catch (\Throwable $e) {
            Log::error('WebhookController.index failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Unable to fetch webhooks'], 500);
        }
    }

    public function store(Request $request, $platform)
    {
        $payload = $request->all();

        // Log incoming webhook (do not log sensitive headers or secrets)
        Log::info("Webhook received from {$platform}", ['size' => strlen(json_encode($payload))]);

        try {
            $webhook = Webhook::create([
                'platform' => $platform,
                'payload' => $payload,
                'status' => 'received'
            ]);

            // Dispatch event - listener will process asynchronously
            WebhookReceived::dispatch($webhook);

            return response()->json(['message' => 'Webhook received', 'id' => $webhook->id], 201);

        } catch (\Throwable $e) {
            Log::error('WebhookController.store failed', ['error' => $e->getMessage(), 'platform' => $platform]);

            return response()->json(['message' => 'Failed to process webhook'], 500);
        }
    }
}
