<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Throwable;

class Handler extends Exception
{
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {

            $status = 500;

            if (method_exists($e, 'getStatusCode')) {
                $status = $e->getStatusCode();
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $status);
        }

        return parent::render($request, $e);
    }
}
