<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class CreateLog
{
    /**
     * @param string $message
     * @param string $file
     * @param int $line
     * @return void
     */
    public static function logError(string $message, string $file, int $line): void
    {
        Log::error($message, ['file' => $file, 'line' => $line]);
    }
}
