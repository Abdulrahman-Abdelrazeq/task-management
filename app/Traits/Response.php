<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

trait Response
{

    // Send a standardized JSON API response.
    public function sendRes($status = true, $message, $data = null, $errors = null, $code = 200)
    {
        $response = [
        'status' => $status,
        'message' => $message,
        ];

        if($data != null) {
            // Handle paginated collections
            if ($data instanceof AnonymousResourceCollection && $data->resource instanceof LengthAwarePaginator) {
                $paginator = $data->resource;
                $response['data'] = $data->collection;
                $response['pagination'] = [
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                    'next_page_url' => $paginator->nextPageUrl(),
                    'prev_page_url' => $paginator->previousPageUrl(),
                ];
            } else {
                $response['data'] = $data;
            }
        }

        // Append error details if present
        if($errors != null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
