<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use MongoDB\Client as MongoClient;

class QueryController extends Controller
{
    protected $mongo;

    public function __construct()
    {
        // Initialize MongoDB client
        $this->mongo = new MongoClient(env('MONGO_URI', 'mongodb://127.0.0.1:27017'));
    }

    public function executeSQL(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sql' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'SQL query is required'], 400);
        }

        $sql = $request->input('sql');

        try {
            // Execute raw SQL
            $result = DB::select($sql);

            // Return as JSON
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to execute SQL',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
