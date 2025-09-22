<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class HealthController extends Controller
{
    public function queue(Request $request)
    {
        try {
            // Return counts for main queues used in this app
            $queues = [
                'products' => Redis::llen('queues:products'),
                'default' => Redis::llen('queues:default'),
            ];

            return response()->json([
                'status' => 'ok',
                'queues' => $queues
            ]);
        } catch (\Throwable $e) {
            Log::error('HealthController.queue failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to fetch queue health'
            ], 500);
        }
    }
}
