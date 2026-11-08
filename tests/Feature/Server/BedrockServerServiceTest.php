<?php

use App\Services\Minecraft\BedrockServerService;

/**
 * A pong as the Bedrock server sends it, with the given text after the header.
 */
function pong(string $text, string $magic = "\x00\xff\xff\x00\xfe\xfe\xfe\xfe\xfd\xfd\xfd\xfd\x12\x34\x56\x78", int $id = 0x1C): string
{
    return chr($id).pack('J', 1_700_000_000_000).pack('J', 42).$magic.pack('n', strlen($text)).$text;
}

beforeEach(function () {
    $this->bedrock = new BedrockServerService('127.0.0.1');
});

it('reads the status from the pong of the server', function () {
    $status = $this->bedrock->parsePong(pong('MCPE;GuadalCraft;2193;1.26.52;1;10;123456789;Our world;Survival;1;19132;19133;'));

    expect($status)->toBe([
        'motd' => 'GuadalCraft',
        'protocol' => 2193,
        'version' => '1.26.52',
        'players' => 1,
        'maxPlayers' => 10,
        'world' => 'Our world',
        'gameMode' => 'Survival',
    ]);
});

it('leaves the world and the game mode empty when the server does not say them', function () {
    $status = $this->bedrock->parsePong(pong('MCPE;Old server;100;1.0.0;0;5'));

    expect($status)->toMatchArray(['world' => null, 'gameMode' => null, 'maxPlayers' => 5]);
});

it('ignores what is not the pong of a Bedrock server', function (string $response) {
    expect($this->bedrock->parsePong($response))->toBeNull();
})->with([
    'nothing' => '',
    'too short' => "\x1C\x00\x01",
    'another packet' => pong('MCPE;A;1;1.0;0;5', id: 0x01),
    'another magic' => pong('MCPE;A;1;1.0;0;5', magic: str_repeat("\x00", 16)),
    'another game' => pong('JAVA;A;1;1.0;0;5'),
    'too few fields' => pong('MCPE;A;1'),
]);

it('says a server that does not answer is offline, without throwing', function () {
    $status = (new BedrockServerService('127.0.0.1', 9, 0.2))->status();

    expect($status)->toMatchArray([
        'online' => false,
        'address' => '127.0.0.1:9',
        'latency' => null,
        'players' => null,
        'version' => null,
    ]);
});
