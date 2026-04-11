<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class CreateLog
{
    /**
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array $inputData
     * @return void
     */
    public static function logError(string $message, string $file, int $line, array $inputData = []): void
    {
        Log::error($message, ['file' => $file, 'line' => $line, 'data' => $inputData]);
    }
}
