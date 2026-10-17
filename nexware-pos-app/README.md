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

Run NativePHP directly in **Windows PowerShell**, not in WSL. Install PHP 8.3+ (Laravel Herd is one option), Composer, Node.js, and JDK 17. The locked Vite 8 toolchain requires Node.js 20.19+ or 22.12+. Install 7-Zip. In Android Studio's SDK Manager, install Android SDK Platform 36, Android SDK Build-Tools, Android SDK Platform-Tools, and NDK `27.0.12077973`, as required by the generated Android Gradle configuration. Then set the SDK/JDK variables for the current PowerShell session, adjusting `JAVA_HOME` if JDK 17 is installed elsewhere:

```powershell
$env:ANDROID_HOME = "$env:LOCALAPPDATA\Android\Sdk"
$env:JAVA_HOME = "C:\Program Files\Microsoft\jdk-17"
$env:Path += ";$env:JAVA_HOME\bin;$env:ANDROID_HOME\platform-tools"

java -version
adb devices
php -v
php -m | Select-String "zip|pdo_sqlite|sqlite3"
```

If Android Studio has a different SDK location, use the path shown under **File → Settings → Languages & Frameworks → Android SDK** instead. If `java -version` or `adb devices` fails, fix the corresponding install/path before continuing. Open the existing repository in Windows and run:

```powershell
Set-Location .\nexware-pos-app
.\scripts\check-environment.ps1
.\scripts\setup-local.ps1
php artisan migrate:status
```

Review any pending migration files and confirm which database `.env` selects before running `php artisan migrate`; never reset or replace an existing database. Check `php artisan list native` before using a NativePHP command. If the ignored Android scaffold is missing in this checkout, `php artisan native:install android` generates it; inspect the existing setup first and do not reinitialize a scaffold that is already present. Register/publish the UI plugin only if it is not already registered. With the Android environment and authorized device ready, run `php artisan native:run android`.

For an existing Windows checkout, use the non-destructive helper scripts from `nexware-pos-app`:

```powershell
.\scripts\check-environment.ps1
.\scripts\setup-local.ps1
.\scripts\start-local.ps1
```

`setup-local.ps1` preserves an existing `.env` and APP_KEY, installs locked Composer/npm dependencies, and builds the frontend. It does not run migrations; review `php artisan migrate:status` and the pending migration files before running `php artisan migrate` against the intended local database. `start-local.ps1` serves the browser preview at `http://127.0.0.1:8000`. Run Android commands only from Windows PowerShell with the SDK, JDK, and device configured.

## Syncing between Windows and Codespaces

Both environments should use separate working copies of the same GitHub repository and branch. Before switching environments, check `git status --short --branch`; commit or otherwise preserve intended source changes before pulling. Pull only the branch you intend to use, for example `git pull --ff-only origin main`, then stage only the source files you mean to share, commit, and push with `git push origin main`. Use your actual feature branch instead of `main` when applicable. If Git reports a conflict or a non-fast-forward update, stop and resolve it deliberately; do not force-push or discard local work. `.env`, SQLite databases, generated Android files, and signing artifacts are ignored and should be configured or backed up separately in each environment.

## Codespaces limitations

NativePHP supports Android builds on Linux. In the Codespace checked for this project, the Android SDK is at `/home/codespace/android-sdk`, JDK 17 is installed, and the generated scaffold includes the embedded PHP runtime archive. Set `ANDROID_HOME` and `ANDROID_SDK_ROOT` to that SDK path, set `JAVA_HOME` to the installed JDK 17 path, and add the SDK's `platform-tools` directory to `PATH` before running Android commands. The current scaffold targets API 36, requires NDK `27.0.12077973`, and builds ARM64 only.

The Android Gradle debug build succeeds in this Codespace, but this instance has no configured AVD or attached device, and its user cannot access KVM. As a result, `php artisan native:run android` cannot launch the app here until an authorized Android target is available. Android emulator and USB-device availability varies by Codespaces host; it cannot control Android Studio or an emulator installed separately on your Windows computer. iOS builds require macOS and Xcode and cannot be produced in a Linux Codespace.

The scaffold in `nativephp/` is generated by NativePHP and intentionally ignored by Git. If it is missing, create it with `php artisan native:install android`; do not reinitialize a scaffold that is already present.

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
