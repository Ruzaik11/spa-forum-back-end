<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BaseApiController extends Controller
{
    public function returnErrorMessage($error){

        Log::error($error->getMessage() , ['line' => $error->getLine(), 'file' => $error->getFile(), 'time' => Carbon::now()->format('Y-m-d H:i:s')]);
           

        return response()->json([
            'error' => true,
            'message' => 'Internal Server Error',
        ], 500);

    }
}
