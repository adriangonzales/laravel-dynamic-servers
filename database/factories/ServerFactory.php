<?php

namespace Spatie\DynamicServers\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Facades\DynamicServers;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\ServerTypes\ServerType;

class ServerFactory extends Factory
{
    protected $model = Server::class;

    public function definition()
    {
        /** @var ServerType $serverType $serverType */
        $serverType = DynamicServers::getServerType('default');

        return [
            'name' => 'server-name',
            'type' => $serverType->name,
            'provider' => $serverType->providerName,
        ];
    }

    public function running(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ServerStatus::Running->value,
            ];
        });
    }
}
