<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = 20;
        $page = $request->query('page', 1);

        $query = Customer::query();

        if ($request->has('country')) {
            $query->where('country', strtolower(trim($request->query('country'))));
        }
        if ($request->has('department')) {
            $query->where('department', trim($request->query('department')));
        }

        $customers = $query->orderBy('signup_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($customers);
    }

    public function show(int $id): JsonResponse
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $this->logCustomerAction($customer->id, 'view');

        return response()->json($customer);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:customers,email',
            'gender' => 'required|in:male,female,other',
            'country' => 'required|string',
            'department' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'signup_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->sanitizeData($validator->validated());

        try {
            DB::beginTransaction();

            $customer = Customer::create($data);

            $this->logCustomerAction($customer->id, 'create');

            DB::commit();

            return response()->json($customer, 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to store customer', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return response()->json([
                'message' => 'Failed to create customer'
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')->ignore($customer->id),
            ],
            'gender' => 'required|in:male,female,other',
            'country' => 'required|string',
            'department' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'signup_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->sanitizeData($validator->validated());

        try {
            DB::beginTransaction();

            $customer->update($data);

            $this->logCustomerAction($customer->id, 'update');

            DB::commit();

            return response()->json($customer);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to update customer', [
                'error' => $e->getMessage(),
                'customer_id' => $id,
                'data' => $data
            ]);

            return response()->json([
                'message' => 'Failed to update customer'
            ], 500);
        }
    }

    /**
     * Logs customer actions with batch insert support
     */
    protected function logCustomerAction(int $customerId, string $action, array $metadata = [])
    {
        try {
            $logData = [
                [
                    'customer_id' => $customerId,
                    'action' => $action,
                    'ip_address' => request()->ip(),
                    'device' => request()->userAgent(),
                    'timestamp' => now(),
                    'metadata' => json_encode($metadata),
                ]
            ];

            // Using insert() for batch insertion
            CustomerActivityLog::insert($logData);

        } catch (\Throwable $e) {
            Log::error('Failed to log customer activity', [
                'customer_id' => $customerId,
                'action' => $action,
                'error' => $e->getMessage(),
                'metadata' => $metadata
            ]);
        }
    }

    /**
     * Sanitize and normalize input data
     */
    protected function sanitizeData(array $data): array
    {
        $data = array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data);

        if (isset($data['email'])) {
            $data['email'] = strtolower($data['email']);
        }
        if (isset($data['country'])) {
            $data['country'] = strtolower($data['country']);
        }

        return $data;
    }
}
