<?php

namespace Tests\Feature;

use App\NativeComponents\DashboardScreen;
use App\NativeComponents\PointOfSaleScreen;
use App\NativeComponents\ProductsScreen;
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

    public function test_the_native_start_url_defaults_to_the_welcome_screen(): void
    {
        $this->assertSame('/welcome', config('nativephp.start_url'));
    }

    public function test_mobile_business_features_are_registered_as_native_routes(): void
    {
        $routes = NativeRouter::registeredRoutes();

        $this->assertSame(DashboardScreen::class, $routes['/app/dashboard']['class'] ?? null);
        $this->assertSame(ProductsScreen::class, $routes['/app/products']['class'] ?? null);
        $this->assertSame(PointOfSaleScreen::class, $routes['/app/pos']['class'] ?? null);
    }
}
