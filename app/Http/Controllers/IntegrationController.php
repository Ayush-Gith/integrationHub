<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use App\Http\Resources\IntegrationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Jobs\SyncProductJob;

class IntegrationController extends Controller
{
    /**
     * List all integrations for the logged-in user.
     */
    public function index()
    {
        $this->authorize('viewAny', Integration::class);

        $integrations = Integration::where('user_id', Auth::id())->get();

        return IntegrationResource::collection($integrations);
    }

    /**
     * Store a new integration.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Integration::class);

        $validated = $request->validate([
            'platform'      => 'required|string|max:50',
            'api_key'       => 'required|string|max:255',
            'api_secret'    => 'nullable|string|max:255',
            'access_token'  => 'nullable|string|max:255',
            'settings'      => 'nullable|array',
        ]);

        try {
            $validated['user_id'] = Auth::id();

            // Convert settings array to JSON safely
            // Store settings as JSON; keep null if not provided
            $validated['settings'] = isset($validated['settings'])
                ? json_encode($validated['settings'])
                : null;

            $integration = Integration::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Integration created successfully',
                'data'    => new IntegrationResource($integration)
            ], 201);
        } catch (\Throwable $e) {
            Log::error("Integration store failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create integration'
            ], 500);
        }
    }

    /**
     * Show a single integration.
     */
    public function show(Integration $integration)
    {
        $this->authorize('view', $integration);

        if ($integration->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return new IntegrationResource($integration);
    }

    /**
     * Update an existing integration.
     */
    public function update(Request $request, Integration $integration)
    {
        $this->authorize('update', $integration);

        if ($integration->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'platform'      => 'sometimes|string|max:50',
            'api_key'       => 'sometimes|string|max:255',
            'api_secret'    => 'nullable|string|max:255',
            'access_token'  => 'nullable|string|max:255',
            'settings'      => 'nullable|array'
        ]);

        try {
            $validated['settings'] = isset($validated['settings'])
                ? json_encode($validated['settings'])
                : null;

            $integration->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Integration updated successfully',
                'data'    => new IntegrationResource($integration)
            ]);
        } catch (\Throwable $e) {
            Log::error("Integration update failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update integration'
            ], 500);
        }
    }

    /**
     * Delete an integration.
     */
    public function destroy(Integration $integration)
    {
        $this->authorize('delete', $integration);

        if ($integration->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $integration->delete();

            return response()->json([
                'success' => true,
                'message' => 'Integration deleted successfully'
            ], 200);
        } catch (\Throwable $e) {
            Log::error("Integration delete failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete integration'
            ], 500);
        }
    }
}
