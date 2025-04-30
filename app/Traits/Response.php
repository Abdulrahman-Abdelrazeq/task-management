<?php

namespace App\Traits;

trait Response
{
    public function sendRes($status = true, $message, $data = null, $errors = null, $code = 200)
    {
        $response = [
        'status' => $status,
        'message' => $message,
        ];

        if($data != null) {
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

        if($errors != null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
