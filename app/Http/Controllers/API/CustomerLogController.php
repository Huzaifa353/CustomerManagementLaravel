<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerActivityLog;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerLogController extends Controller
{
    // POST /customers/{id}/log
    public function store(Request $request, int $id): JsonResponse
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $data = $request->validate([
            'action' => 'required|string',           // login|logout|purchase|profile_update etc.
            'ip_address' => 'required|ip',
            'device' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
            'timestamp' => 'nullable|date',
        ]);

        $log = CustomerActivityLog::create([
            'customer_id' => $customer->id,
            'action' => $data['action'],
            'ip_address' => $data['ip_address'],
            'device' => $data['device'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'timestamp' => $data['timestamp'] ?? now(),
        ]);

        return response()->json($log, 201);
    }

    // GET /customers/{id}/logs
    public function index(int $id): JsonResponse
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $logs = CustomerActivityLog::where('customer_id', $id)
            ->orderBy('timestamp', 'desc')
            ->get();

        return response()->json($logs);
    }
}
