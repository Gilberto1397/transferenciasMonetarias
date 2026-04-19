<?php

namespace App\Jobs;

use App\Clients\NotificationClient;
use App\Helpers\CreateLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 6;

    public int $maxExceptions = 6;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        if ($this->connection === 'sync' || env('QUEUE_CONNECTION') === 'sync') {
            throw new \DomainException('Sistema de filas não configurado. Configure o sistema de filas para usar este serviço!');
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!(new NotificationClient())->throwNotification()) {
            dump('Falha ao enviar notificação!');
            $this->release(1);
            return;
        }
        dump('Notificação enviada com sucesso!');
    }

    public function fail(\Throwable $exception): void
    {
        CreateLog::logError($exception->getMessage(), $exception->getFile(), $exception->getLine());
    }
}
