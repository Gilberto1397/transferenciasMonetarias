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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(NotificationClient $client)
    {
        try {
            if (!$client->throwNotification()) {
                dump('Falha ao enviar notificação!');
                $this->release(1);
                return;
            }
            dump('Notificação enviada com sucesso!');
        } catch (\Throwable $exception) {
            CreateLog::logError($exception->getMessage(), $exception->getFile(), $exception->getLine());
        }
    }
}
