<?php

use App\NativeComponents\WelcomeScreen;
use Illuminate\Support\Facades\Route;

Route::native('/welcome', WelcomeScreen::class);
