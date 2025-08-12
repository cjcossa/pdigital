<?php
namespace App\Helpers;

class ApiResponse
{
    public static function send($success, $message, $status = 200, $data = null, $errors = null)
    {
        $response = [
            'success' => $success,
            'message' => $message,
            'data' => $data
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    public static function mapEstado($data = [])
    {
        return $data->map(function ($data) {
            return array_merge($data->toArray(), [
                'estado_desc' => $data->status_description,
            ]);
        });

    }
}
