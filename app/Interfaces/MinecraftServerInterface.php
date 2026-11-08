<?php

namespace App\Interfaces;

/**
 * The Minecraft server the panel manages. The real one is the Bedrock server of the VPS, and the fake one gives
 * believable data without touching any server: the tests and a public demo use it. config('minecraft.driver')
 * chooses which one the container gives.
 *
 * @phpstan-type ServerStatus array{
 *     online: bool,
 *     address: string,
 *     latency: ?int,
 *     motd: ?string,
 *     version: ?string,
 *     protocol: ?int,
 *     players: ?int,
 *     maxPlayers: ?int,
 *     world: ?string,
 *     gameMode: ?string,
 *     checkedAt: string,
 * }
 */
interface MinecraftServerInterface
{
    /**
     * What the server says about itself right now. A server that does not answer is offline, it never throws.
     *
     * @return ServerStatus
     */
    public function status(): array;
}
