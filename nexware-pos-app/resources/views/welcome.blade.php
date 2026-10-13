<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f8f4">
    <title>{{ config('app.name', 'Nexware POS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="welcome-shell">
    <header class="site-header">
        <a class="brand" href="/" aria-label="Nexware POS home">
            <span class="brand-mark" aria-hidden="true">N</span>
            <span class="brand-copy">
                <strong>Nexware POS</strong>
                <span>by Nexware Systems</span>
            </span>
        </a>
        <span class="header-note">Made for business in Zambia</span>
    </header>

    <main>
        <section class="hero" aria-labelledby="welcome-title">
            <div class="hero-copy">
                <p class="eyebrow">A simpler way to run your shop</p>
                <h1 id="welcome-title">A clearer day at the <span>counter.</span></h1>
                <p class="hero-description">
                    Simple tools to help you sell, keep track of stock, and understand how your business is doing.
                    Built for everyday business in Zambia.
                </p>
                <div class="hero-actions">
                    <a class="primary-link" href="#features">
                        See what matters
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <span class="hero-footnote">Designed for the way you work</span>
                </div>
            </div>

            <div class="hero-art" aria-hidden="true">
                <div class="art-orbit"></div>
                <div class="floating-note note-top">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 10.5 12 4l8 6.5v8.25A1.25 1.25 0 0 1 18.75 20h-13.5A1.25 1.25 0 0 1 4 18.75V10.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        <path d="M9 20v-6h6v6" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    </svg>
                    Made for your shop
                </div>
                <div class="store-card">
                    <div class="store-card-top">
                        <span>Your business, at a glance</span>
                        <span aria-hidden="true">✳</span>
                    </div>
                    <svg class="store-illustration" viewBox="0 0 320 230" fill="none">
                        <path d="M24 193h272" stroke="#e3eadf" stroke-width="2" stroke-linecap="round"/>
                        <path d="M64 83h192v110H64V83Z" fill="#f1f5ed" stroke="#dce5d8" stroke-width="2"/>
                        <path d="M49 82 68 39h184l19 43H49Z" fill="#194b3b"/>
                        <path d="M49 82h222v13a15 15 0 0 1-30 0 15 15 0 0 1-30 0 15 15 0 0 1-30 0 15 15 0 0 1-30 0 15 15 0 0 1-30 0 15 15 0 0 1-30 0 15 15 0 0 1-30 0V82Z" fill="#d6ed8b"/>
                        <path d="M91 115h51v78H91v-78Z" fill="#fff" stroke="#dce5d8" stroke-width="2"/>
                        <path d="M105 130h23M105 140h23M105 150h16" stroke="#a8b9aa" stroke-width="3" stroke-linecap="round"/>
                        <path d="M166 116h63v35h-63v-35Z" fill="#fff" stroke="#dce5d8" stroke-width="2"/>
                        <path d="M177 127h41M177 137h28" stroke="#a8b9aa" stroke-width="3" stroke-linecap="round"/>
                        <path d="M167 167h61" stroke="#c8d7c5" stroke-width="5" stroke-linecap="round"/>
                        <circle cx="197" cy="168" r="8" fill="#85a75c"/>
                        <path d="M77 57h28M117 57h28M157 57h28M197 57h28" stroke="#cce47c" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                    <div class="store-card-caption">
                        <span class="caption-dot"></span>
                        <span>Sell simply. Stay in control.</span>
                    </div>
                </div>
                <div class="floating-note note-bottom">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        <path d="m4.5 7.75 7.5 4.5 7.5-4.5M12 12.5V20" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    </svg>
                    Keep stock in view
                </div>
            </div>
        </section>

        <section class="feature-section" id="features" aria-label="What Nexware POS is designed to help with">
            <div class="feature-grid">
                <article class="feature-card">
                    <span class="feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M4 7h16v13H4V7Zm3 0V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2M4 12h16" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <h2>Selling made simple</h2>
                    <p>Keep the everyday checkout clear and easy, so you can focus on the customer in front of you.</p>
                </article>
                <article class="feature-card">
                    <span class="feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M4 8.5 12 4l8 4.5v9L12 22l-8-4.5v-9Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            <path d="m4.5 8.75 7.5 4.5 7.5-4.5M12 13v8" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <h2>Stock you can follow</h2>
                    <p>Know what is on your shelves and what may need your attention before it runs out.</p>
                </article>
                <article class="feature-card">
                    <span class="feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M4 19V5m0 14h17M7 15l4-4 3 2 6-7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 6h4v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <h2>A clearer view of business</h2>
                    <p>Bring sales, stock, and day-to-day decisions together in one straightforward place.</p>
                </article>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <span><strong>Nexware POS</strong> · by Nexware Systems</span>
        <span>Made for the way Zambia does business</span>
    </footer>
</body>
</html>
