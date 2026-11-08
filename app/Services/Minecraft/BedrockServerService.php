<?php

namespace App\Services\Minecraft;

use App\Interfaces\MinecraftServerInterface;

/**
 * The Bedrock server of the VPS. Its status comes from the RakNet "unconnected ping": a UDP packet any client sends
 * to list a server, that needs no login nor access to the machine.
 *
 * @phpstan-import-type ServerStatus from MinecraftServerInterface
 */
class BedrockServerService implements MinecraftServerInterface
{
    /** The "offline message id" every unconnected packet of RakNet carries */
    private const string MAGIC = "\x00\xff\xff\x00\xfe\xfe\xfe\xfe\xfd\xfd\xfd\xfd\x12\x34\x56\x78";

    private const int UNCONNECTED_PING = 0x01;

    private const int UNCONNECTED_PONG = 0x1C;

    /** Id (1), time (8), server guid (8), magic (16) and the length of the text (2) */
    private const int PONG_HEADER_LENGTH = 35;

    public function __construct(
        private readonly string $host,
        private readonly int $port = 19132,
        private readonly float $timeout = 1.5,
    ) {}

    /**
     * @return ServerStatus
     */
    public function status(): array
    {
        $started = hrtime(true);
        $response = $this->ping();
        $latency = (int) round((hrtime(true) - $started) / 1_000_000);

        $pong = $response === null ? null : $this->parsePong($response);

        return [
            'online' => $pong !== null,
            'address' => "{$this->host}:{$this->port}",
            'latency' => $pong === null ? null : $latency,
            'motd' => $pong['motd'] ?? null,
            'version' => $pong['version'] ?? null,
            'protocol' => $pong['protocol'] ?? null,
            'players' => $pong['players'] ?? null,
            'maxPlayers' => $pong['maxPlayers'] ?? null,
            'world' => $pong['world'] ?? null,
            'gameMode' => $pong['gameMode'] ?? null,
            'checkedAt' => now()->toIso8601String(),
        ];
    }

    /**
     * Reads the answer of the server to the ping. The text is "MCPE;motd;protocol;version;players;max players;
     * server id;world;game mode;...", and anything that is not a pong of a Bedrock server is null.
     *
     * @return array{motd: string, protocol: int, version: string, players: int, maxPlayers: int, world: ?string, gameMode: ?string}|null
     */
    public function parsePong(string $response): ?array
    {
        if (strlen($response) < self::PONG_HEADER_LENGTH
            || ord($response[0]) !== self::UNCONNECTED_PONG
            || substr($response, 17, 16) !== self::MAGIC) {
            return null;
        }

        /** @var array{1: int} $length */
        $length = unpack('n', substr($response, 33, 2));
        $fields = explode(';', substr($response, self::PONG_HEADER_LENGTH, $length[1]));

        if (count($fields) < 6 || ! in_array($fields[0], ['MCPE', 'MCEE'], true)) {
            return null;
        }

        return [
            'motd' => $fields[1],
            'protocol' => (int) $fields[2],
            'version' => $fields[3],
            'players' => (int) $fields[4],
            'maxPlayers' => (int) $fields[5],
            'world' => ($fields[7] ?? '') !== '' ? $fields[7] : null,
            'gameMode' => ($fields[8] ?? '') !== '' ? $fields[8] : null,
        ];
    }

    /**
     * Sends the ping and waits for the pong. Null when the server does not answer in time.
     */
    private function ping(): ?string
    {
        $socket = @stream_socket_client("udp://{$this->host}:{$this->port}", timeout: $this->timeout);

        if ($socket === false) {
            return null;
        }

        try {
            stream_set_timeout($socket, (int) $this->timeout, (int) (fmod($this->timeout, 1) * 1_000_000));

            $packet = chr(self::UNCONNECTED_PING)
                .pack('J', (int) (microtime(true) * 1000))
                .self::MAGIC
                .pack('J', random_int(0, PHP_INT_MAX));

            if (@fwrite($socket, $packet) === false) {
                return null;
            }

            $response = @fread($socket, 2048);

            return $response === false || $response === '' ? null : $response;
        } finally {
            fclose($socket);
        }
    }
}
