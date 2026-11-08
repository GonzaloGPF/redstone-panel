<?php

use App\Enums\Roles;
use App\Interfaces\MinecraftServerInterface;
use App\Models\User;
use App\Services\Minecraft\BedrockServerService;
use App\Services\Minecraft\FakeServerService;
use App\Services\ServerStatusService;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config(['inertia.testing.ensure_pages_exist' => false]);
});

/**
 * A server that counts how many times it is asked.
 */
function countingServer(): MinecraftServerInterface
{
    return new class implements MinecraftServerInterface
    {
        public int $calls = 0;

        public function status(): array
        {
            $this->calls++;

            return app(FakeServerService::class)->status();
        }
    };
}

it('gives the fake server unless the real one is asked for', function () {
    expect(app(MinecraftServerInterface::class))->toBeInstanceOf(FakeServerService::class);

    config(['minecraft.driver' => 'bedrock']);
    app()->forgetInstance(MinecraftServerInterface::class);

    expect(app(MinecraftServerInterface::class))->toBeInstanceOf(BedrockServerService::class);
});

it('gives everybody the status of the server, deferred in its own group', function (Roles $role) {
    $this->signIn(User::factory()->create(['role' => $role]));

    $this->get(route('dashboard.show'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->missing('server')
            ->loadDeferredProps('server', fn (Assert $reload) => $reload
                ->where('server.online', true)
                ->where('server.maxPlayers', 10)
                ->has('server.checkedAt')
                ->missing('stats')));
})->with([Roles::ADMIN, Roles::USER]);

it('asks the server once, however many times the status is read in a while', function () {
    $server = countingServer();
    $this->app->instance(MinecraftServerInterface::class, $server);
    config(['minecraft.status_ttl' => 15]);

    app(ServerStatusService::class)->current();
    app(ServerStatusService::class)->current();

    expect($server->calls)->toBe(1);

    $this->travel(16)->seconds();
    app(ServerStatusService::class)->current();

    expect($server->calls)->toBe(2);
});
