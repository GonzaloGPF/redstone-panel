<?php

use App\Models\User;

/**
 * Waits for the fonts of the page to load, and tells which ones are there.
 *
 * @return array{figtree: bool, icons: bool, iconWidth: int}
 */
function loadedFonts($page): array
{
    $page->script('document.fonts.ready');

    return json_decode((string) $page->script(<<<'JS'
        (() => JSON.stringify({
            figtree: document.fonts.check('16px Figtree'),
            icons: document.fonts.check('24px "Material Symbols Outlined"'),
            iconWidth: Math.round(document.querySelector('.material-symbols-outlined')?.getBoundingClientRect().width ?? 0),
        }))()
    JS), true);
}

/**
 * The resources the page asked for to another origin.
 *
 * @return list<string>
 */
function externalRequests($page): array
{
    return json_decode((string) $page->script(<<<'JS'
        JSON.stringify(performance.getEntriesByType('resource').map(entry => entry.name).filter(url => new URL(url).origin !== location.origin))
    JS), true);
}

test('the pages ask no third party for anything, not even the fonts', function (bool $logged) {
    if ($logged) {
        $this->actingAs(User::factory()->admin()->create(['language' => 'en']));
    }

    $page = visit($logged ? '/dashboard' : '/');
    loadedFonts($page);

    expect(externalRequests($page))->toBeEmpty();
})->with(['guest' => false, 'logged' => true]);

test('the text is written with Figtree and the icons are drawn with their own font', function () {
    $page = visit('/');

    $fonts = loadedFonts($page);

    expect($fonts['figtree'])->toBeTrue()
        ->and($fonts['icons'])->toBeTrue()
        ->and($fonts['iconWidth'])->toBe(24); // An icon is one glyph of 24px, not the letters of its name

    $page->assertNoJavaScriptErrors();
});
