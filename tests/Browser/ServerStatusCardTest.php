<?php

use App\Enums\Languages;
use App\Enums\Roles;
use App\Interfaces\MinecraftServerInterface;
use App\Models\User;
use App\Services\Minecraft\BedrockServerService;

/**
 * Waits for the status of the server to arrive, and gives the state of the card.
 */
function serverCardState($page): string
{
    $state = 'loading';

    for ($attempt = 0; $attempt < 50 && $state === 'loading'; $attempt++) {
        $state = (string) $page->script("document.querySelector('[data-server-status]')?.dataset.state ?? 'loading'");

        if ($state === 'loading') {
            usleep(100_000);
        }
    }

    return $state;
}

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => Roles::USER, 'language' => Languages::EN]));
});

test('everybody sees the status of the server, with its players, version, latency and world', function () {
    $page = visit(route('dashboard.show', absolute: false));

    expect(serverCardState($page))->toBe('online')
        ->and((int) $page->script("document.querySelectorAll('[data-server-field]').length"))->toBe(4)
        ->and($page->script("document.querySelector('[data-server-status] .logo-mark')?.dataset.state"))->toBe('online');

    $page->assertSee('Online')
        ->assertSee('RedstonePanel demo')
        ->assertSee('Players')
        ->assertSee('/ 10')
        ->assertSee('1.21.120.4')
        ->assertSee('Bedrock level · Survival')
        ->assertNoJavaScriptErrors();
});

test('a server that does not answer is shown offline, with the lamp off', function () {
    $this->app->instance(MinecraftServerInterface::class, new BedrockServerService('127.0.0.1', 9, 0.2));

    $page = visit(route('dashboard.show', absolute: false));

    expect(serverCardState($page))->toBe('offline')
        ->and((int) $page->script("document.querySelectorAll('[data-server-field]').length"))->toBe(0)
        ->and($page->script("document.querySelector('[data-server-status] .logo-mark')?.dataset.state"))->toBe('offline');

    $page->assertSee('Offline')
        ->assertSee('The server does not answer')
        ->assertNoJavaScriptErrors();
});

test('the status of the server is translated', function () {
    $this->actingAs(User::factory()->create(['role' => Roles::USER, 'language' => Languages::ES]));

    $page = visit(route('dashboard.show', absolute: false));

    expect(serverCardState($page))->toBe('online');

    $page->assertSee('Estado del servidor')
        ->assertSee('En línea')
        ->assertSee('Jugadores');
});
