<?php

test('the homepage loads without javascript errors', function () {
    $page = visit('/');

    $page->assertSee('RedstonePanel')
        ->assertSee(__('labels.tagline'))
        ->assertNoSmoke();
});
