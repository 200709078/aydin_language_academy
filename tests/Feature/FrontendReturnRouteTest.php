<?php

use App\Http\Responses\FrontendLogoutResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LogoutResponse;

test('login bridge stores an allowed frontend route as the intended destination', function () {
    $response = $this->get('/giris?return=frontend.campaigns');

    $response->assertRedirect(route('login'));
    expect(session('url.intended'))->toBe(route('frontend.campaigns', absolute: false));
});

test('logout response returns to an allowed frontend route', function () {
    $request = Request::create('/logout', 'POST', ['return' => 'frontend.campaigns']);

    expect(app(LogoutResponse::class))->toBeInstanceOf(FrontendLogoutResponse::class);

    $response = app(LogoutResponse::class)->toResponse($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())->toBe(route('frontend.campaigns'));
});

test('logout response rejects protected and unknown return routes', function (string $returnRoute) {
    $request = Request::create('/logout', 'POST', ['return' => $returnRoute]);

    $response = app(LogoutResponse::class)->toResponse($request);

    expect($response->getTargetUrl())->toBe(route('home'));
})->with(['admin', 'https://example.com']);
