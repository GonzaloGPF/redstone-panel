<?php

namespace App\Services;

use App\Interfaces\MinecraftServerInterface;
use Illuminate\Support\Facades\Cache;

/**
 * The status of the Minecraft server, kept for a few seconds: however many pages are polling, the server is asked
 * once per config('minecraft.status_ttl').
 *
 * @phpstan-import-type ServerStatus from MinecraftServerInterface
 */
class ServerStatusService
{
    public const string CACHE_KEY = 'minecraft.status';

    public function __construct(private readonly MinecraftServerInterface $server) {}

    /**
     * @return ServerStatus
     */
    public function current(): array
    {
        return Cache::remember(self::CACHE_KEY, config()->integer('minecraft.status_ttl'), fn () => $this->server->status());
    }
}
