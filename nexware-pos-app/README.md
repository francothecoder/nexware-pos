# Nexware POS

Nexware POS is an offline-first point-of-sale and stock-management app for small businesses in Zambia. The mobile app is built with Laravel and NativePHP for Mobile; SQLite is the local development and operational database.

## Foundation status

This foundation includes the Laravel app, SQLite defaults, a browser preview at `/`, and a NativePHP EDGE welcome screen at `/welcome`. Registration, selling, inventory, and synchronization are not implemented yet.

## Requirements verified against NativePHP Mobile 4

The project currently uses PHP `^8.3`, Laravel `^13.17`, NativePHP Mobile `^4.6`, and NativePHP Mobile UI `^0.8`. The installed versions are PHP 8.4.15, Laravel 13.35.0, NativePHP Mobile 4.6.0, and NativePHP Mobile UI 0.8.0.

NativePHP's [environment setup guide](https://nativephp.com/docs/mobile/4/getting-started/environment-setup) lists PHP 8.3+ and Laravel 10+. Its Android workflow requires Android Studio 2024.2.1 or later, Android SDK API 29 or later, SDK Build-Tools and Platform-Tools, and a working Java and ADB installation. The guide's environment examples use JDK 17. NativePHP Mobile and its Mobile UI plugin are MIT-licensed.

NativePHP 4 uses EDGE Blade components for native screens. These render as platform-native Android and iOS views; they are not ordinary HTML/CSS pages in a WebView. The browser preview is kept separate so it can still be checked in a Codespace.

## Set up in GitHub Codespaces

Open `nexware-pos-app` as the Codespace working directory, then run:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install
npm run build
```

The included environment defaults use `DB_CONNECTION=sqlite`, the `Africa/Lusaka` timezone, `NATIVEPHP_APP_ID=com.nexwaresystems.nexwarepos`, and `/welcome` as the mobile start route. Before publishing, replace the app ID if it is not a unique reverse-DNS identifier controlled by Nexware Systems.

To preview the browser version from the Codespace:

```bash
php artisan serve --host=0.0.0.0
```

Open port 8000 when Codespaces prompts you. The `/` route is the HTML/CSS preview; the NativePHP EDGE screen is registered at `/welcome`.

## Build and run on Android

Install Android Studio 2024.2.1 or later and, in **Tools → SDK Manager**, install an Android SDK for API 29 or later plus Android SDK Build-Tools and Platform-Tools. Create and start a virtual device in **Tools → Device Manager**, or enable USB debugging on a connected phone.

Before building, check that `java -version` and `adb devices` work in the same terminal you will use to run Artisan, and that your PHP has `ext-zip`, `pdo_sqlite`, and `sqlite3` enabled. NativePHP downloads an embedded PHP runtime during installation. Windows also requires [7-Zip](https://www.7-zip.org/). Publish and register the UI plugin once, then install and run the Android app:

```bash
php artisan vendor:publish --tag=nativephp-plugins-provider
php artisan native:plugin:register nativephp/mobile-ui
php artisan native:install android
php artisan native:run android
```

The app should open on its `/welcome` native route.

If you already have a debug APK, install it on a connected ARM64 Android phone from PowerShell with:

```powershell
adb install -r .\nexware-pos-debug.apk
```

NativePHP's current Android configuration packages the embedded PHP runtime for ARM64. An x86_64 emulator cannot run this APK.

### Windows PowerShell

Run NativePHP directly in **Windows PowerShell**, not in WSL. Install PHP 8.3+ (Laravel Herd is one option), Composer, Node.js, and JDK 17. Install 7-Zip. In Android Studio's SDK Manager, install the platform and build tools above. Then set the SDK/JDK variables for the current PowerShell session, adjusting `JAVA_HOME` if JDK 17 is installed elsewhere:

```powershell
$env:ANDROID_HOME = "$env:LOCALAPPDATA\Android\Sdk"
$env:JAVA_HOME = "C:\Program Files\Microsoft\jdk-17"
$env:Path += ";$env:JAVA_HOME\bin;$env:ANDROID_HOME\platform-tools"

java -version
adb devices
php -v
php -m | Select-String "zip|pdo_sqlite|sqlite3"
```

If Android Studio has a different SDK location, use the path shown under **File → Settings → Languages & Frameworks → Android SDK** instead. If `java -version` or `adb devices` fails, fix the corresponding install/path before continuing. Open the project in Windows and run:

```powershell
Set-Location .\nexware-pos-app
composer install
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
if (-not (Test-Path database\database.sqlite)) { New-Item -ItemType File database\database.sqlite | Out-Null }
php artisan migrate
npm install
npm run build
php artisan vendor:publish --tag=nativephp-plugins-provider
php artisan native:plugin:register nativephp/mobile-ui
php artisan native:install android
php artisan native:run android
```

If the plugin provider is already present, skip the `vendor:publish` command. Keep the emulator running or connect the phone before the final command. To run the same app again later, usually only `php artisan native:run android` is needed.

## Codespaces limitations

Codespaces runs on Linux, which NativePHP supports for Android builds, but this workspace does not have Android Studio, the Android SDK, ADB, or an embedded NativePHP PHP binary. Android emulators also need virtualization and a graphical environment that a typical Codespace does not expose. This Linux Codespace cannot control Android Studio or an emulator installed separately on your Windows computer. Run NativePHP from the Windows project directory for a local Windows build.

The scaffold in `nativephp/` is generated by NativePHP and intentionally ignored by Git; re-create it with `php artisan native:install android` after installing the Android prerequisites. iOS builds require macOS and Xcode and cannot be produced in a Linux Codespace.

## Validation

Run the Laravel tests and NativePHP route/component validation:

```bash
php artisan test
php artisan native:validate
```

## NativePHP references

- [NativePHP Mobile installation](https://nativephp.com/docs/mobile/4/getting-started/installation)
- [NativePHP Mobile environment setup](https://nativephp.com/docs/mobile/4/getting-started/environment-setup)
- [EDGE components](https://nativephp.com/docs/mobile/4/edge-components/introduction)
- [SuperNative architecture](https://nativephp.com/docs/mobile/4/architecture/super-native)
