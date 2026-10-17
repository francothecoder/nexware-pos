<?php

use App\NativeComponents\DashboardScreen;
use App\NativeComponents\PointOfSaleScreen;
use App\NativeComponents\ProductsScreen;
use App\NativeComponents\WelcomeScreen;
use Illuminate\Support\Facades\Route;

Route::native('/welcome', WelcomeScreen::class);
Route::native('/app/dashboard', DashboardScreen::class);
Route::native('/app/products', ProductsScreen::class);
Route::native('/app/pos', PointOfSaleScreen::class);
