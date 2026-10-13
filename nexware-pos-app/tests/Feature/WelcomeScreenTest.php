<?php

namespace Tests\Feature;

use App\NativeComponents\WelcomeScreen;
use Native\Mobile\Edge\NativeRouter;
use Tests\TestCase;

class WelcomeScreenTest extends TestCase
{
    public function test_the_browser_preview_displays_the_nexware_welcome_screen(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Nexware POS')
            ->assertSee('A clearer day at the')
            ->assertSee('Made for business in Zambia')
            ->assertSee('Selling made simple');
    }

    public function test_the_mobile_welcome_screen_is_registered_as_a_native_route(): void
    {
        $this->assertSame(
            WelcomeScreen::class,
            NativeRouter::registeredRoutes()['/welcome']['class'] ?? null,
        );
    }
}
