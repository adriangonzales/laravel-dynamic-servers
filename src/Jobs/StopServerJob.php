<?php

namespace Spatie\DynamicServers\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\DynamicServers\Events\DeletingServerEvent;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\Config;

class StopServerJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $deleteWhenMissingModels = true;

    public function __construct(public Server $server)
    {
    }

    public function handle(): bool
    {
        try {
            $this->server->provider()->stopServer();
        } catch (Exception $exception) {
            $this->server->markAsErrored($exception);

            report($exception);

            return;
        }

        event(new DeletingServerEvent($this->server));

        $verifyServerStoppedJob = Config::jobClass('verify_server_stopped');

        dispatch(new $verifyServerStoppedJob($this->server));
    }
}
