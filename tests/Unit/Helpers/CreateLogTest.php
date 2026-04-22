<?php

namespace Tests\Unit\Helpers;

use App\Helpers\CreateLog;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CreateLogTest extends TestCase
{
    public function testLogErrorSuccess(): void
    {
        /**
         * Given - Arrange
         */
        $message = 'Falha ao processar transferência.';
        $file = '/app/Services/TransferValueService.php';
        $line = 88;

        Log::shouldReceive('error')
            ->once()
            ->with($message, ['file' => $file, 'line' => $line]);

        /**
         * When - Act
         */
        CreateLog::logError($message, $file, $line);

        /**
         * Then - Assert
         */
        $this->assertTrue(true, 'O método deveria registrar o log sem exceção.');
    }

    public function testLogErrorAcceptsEmptyValues(): void
    {
        /**
         * Given - Arrange
         */
        $message = '';
        $file = '';
        $line = 0;

        Log::shouldReceive('error')
            ->once()
            ->with($message, ['file' => $file, 'line' => $line]);

        /**
         * When - Act
         */
        CreateLog::logError($message, $file, $line);

        /**
         * Then - Assert
         */
        $this->assertTrue(true, 'O método deveria encaminhar valores vazios para o facade de log.');
    }
}

