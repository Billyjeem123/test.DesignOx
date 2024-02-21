<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class Utility
{
    public static function outputData($boolean, $message, $data = null, $statusCode)
    {
        return response()->json([
            'status' => $boolean,
            'message' => $message,
            'data' => $data,
            'status_code' => $statusCode
        ], $statusCode);
    }


    public static function outputResult($boolean, $message, $data = null, $access_token)
    {
        return response()->json([
            'status' => $boolean,
            'message' => $message,
            'data' => $data,
            'access_token' => $access_token
        ]);
    }

    

    public static function token()
    {
        return mt_rand(100000, 999999);
    }


    public static function calculateEndDate($duration)
    {
        // Parse the duration string to extract the number and unit
        preg_match('/(\d+)\s*(\w+)/', $duration, $matches);
        $number = intval($matches[1]);
        $unit = strtolower($matches[2]);

        // Calculate the end date based on the unit
        switch ($unit) {
            case 'week':
            case 'weeks':
                $endDate = date('Y-m-d H:i:s', strtotime("+$number weeks"));
                break;
            case 'month':
            case 'months':
                $endDate = date('Y-m-d H:i:s', strtotime("+$number months"));
                break;
            case 'day':
            case 'days':
                $endDate = date('Y-m-d H:i:s', strtotime("+$number days"));
                break; // Don't forget to break after handling 'days'

            default:
                // Handle other units if needed
                $endDate = null;
                break;
        }

        return $endDate;
    }


}
