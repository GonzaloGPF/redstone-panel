<?php

namespace App\Services\Minecraft;

use App\Interfaces\MinecraftServerInterface;

/**
 * A server that is always online, with a believable status, and that touches nothing: for the tests, the local
 * development without the VPS, and a public demo of the panel.
 *
 * @phpstan-import-type ServerStatus from MinecraftServerInterface
 */
class FakeServerService implements MinecraftServerInterface
{
    public function __construct(
        private readonly string $host = 'demo.redstone-panel.test',
        private readonly int $port = 19132,
    ) {}

    /**
     * @return ServerStatus
     */
    public function status(): array
    {
        return [
            'online' => true,
            'address' => "{$this->host}:{$this->port}",
            'latency' => random_int(8, 40),
            'motd' => 'RedstonePanel demo',
            'version' => '1.21.120.4',
            'protocol' => 859,
            'players' => random_int(0, 2),
            'maxPlayers' => 10,
            'world' => 'Bedrock level',
            'gameMode' => 'Survival',
            'checkedAt' => now()->toIso8601String(),
        ];
    }
}
