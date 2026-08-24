<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('electrik.name', config('app.name', 'Electrik')) }} — Laravel Application Starter</title>

    <style>
        :root {
            --bg: #08090b;
            --bg-soft: #0d0f12;
            --border: rgba(255, 255, 255, 0.09);
            --border-hover: rgba(255, 255, 255, 0.16);
            --text: #f5f5f5;
            --muted: #8d929a;
            --muted-light: #b7bbc2;
            --accent: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* --------------------------------
           Background
        -------------------------------- */

        .page {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
        }

        .grid {
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: 0.45;

            background-image:
                linear-gradient(
                    rgba(255,255,255,0.035) 1px,
                    transparent 1px
                ),
                linear-gradient(
                    90deg,
                    rgba(255,255,255,0.035) 1px,
                    transparent 1px
                );

            background-size: 72px 72px;

            mask-image: linear-gradient(
                to bottom,
                black 0%,
                black 45%,
                transparent 90%
            );
        }

        .glow {
            position: absolute;
            width: 700px;
            height: 500px;
            top: -220px;
            left: 50%;
            transform: translateX(-50%);

            background: radial-gradient(
                ellipse,
                rgba(255,255,255,0.10),
                rgba(255,255,255,0.035) 35%,
                transparent 70%
            );

            filter: blur(30px);
            pointer-events: none;
        }

        /* --------------------------------
           Container
        -------------------------------- */

        .container {
            width: min(1120px, calc(100% - 48px));
            margin: 0 auto;
        }

        /* --------------------------------
           Navigation
        -------------------------------- */

        nav {
            position: relative;
            z-index: 5;

            height: 82px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            border-bottom: 1px solid var(--border);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;

            font-size: 16px;
            font-weight: 650;
            letter-spacing: -0.02em;
        }

        .brand-mark {
            width: 26px;
            height: 26px;

            display: grid;
            place-items: center;

            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 7px;

            background: rgba(255,255,255,0.05);

            box-shadow:
                inset 0 1px rgba(255,255,255,0.08),
                0 0 30px rgba(255,255,255,0.04);
        }

        .brand-mark svg {
            width: 15px;
            height: 15px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link {
            padding: 8px 12px;

            color: var(--muted);

            font-size: 13px;
            font-weight: 500;

            border-radius: 7px;

            transition:
                color 160ms ease,
                background 160ms ease;
        }

        .nav-link:hover {
            color: var(--text);
            background: rgba(255,255,255,0.05);
        }

        /* --------------------------------
           Hero
        -------------------------------- */

        .hero {
            position: relative;
            z-index: 2;

            padding: 128px 0 100px;
            text-align: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            padding: 6px 10px;

            border: 1px solid var(--border);
            border-radius: 999px;

            background: rgba(255,255,255,0.035);

            color: var(--muted-light);

            font-size: 12px;
            font-weight: 500;

            box-shadow:
                inset 0 1px rgba(255,255,255,0.04);
        }

        .status-dot {
            width: 6px;
            height: 6px;

            border-radius: 50%;

            background: #8cffb0;

            box-shadow: 0 0 12px rgba(140,255,176,0.7);
        }

        h1 {
            max-width: 900px;
            margin: 28px auto 0;

            font-size: clamp(54px, 8vw, 94px);
            line-height: 0.95;

            letter-spacing: -0.065em;
            font-weight: 650;
        }

        .gradient-text {
            background:
                linear-gradient(
                    180deg,
                    #ffffff 10%,
                    #b9bdc5 90%
                );

            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero-description {
            max-width: 570px;
            margin: 28px auto 0;

            color: var(--muted);

            font-size: 17px;
            line-height: 1.7;
            letter-spacing: -0.01em;
        }

        .actions {
            margin-top: 38px;

            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;

            height: 44px;
            padding: 0 18px;

            border-radius: 8px;

            font-size: 13px;
            font-weight: 600;

            transition:
                transform 160ms ease,
                background 160ms ease,
                border-color 160ms ease;
        }

        .button:hover {
            transform: translateY(-1px);
        }

        .button-primary {
            color: #08090b;
            background: #fff;

            box-shadow:
                0 0 0 1px rgba(255,255,255,0.5),
                0 10px 35px rgba(0,0,0,0.25);
        }

        .button-primary:hover {
            background: #e9e9e9;
        }

        .button-secondary {
            border: 1px solid var(--border);

            color: var(--muted-light);

            background: rgba(255,255,255,0.025);
        }

        .button-secondary:hover {
            color: var(--text);
            border-color: var(--border-hover);
            background: rgba(255,255,255,0.05);
        }

        /* --------------------------------
           Stack
        -------------------------------- */

        .stack {
            margin-top: 70px;

            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .stack-item {
            padding: 7px 11px;

            border: 1px solid var(--border);
            border-radius: 6px;

            color: #7f848c;

            font-family:
                "SFMono-Regular",
                Consolas,
                "Liberation Mono",
                monospace;

            font-size: 11px;

            background: rgba(255,255,255,0.018);
        }

        /* --------------------------------
           Cards
        -------------------------------- */

        .features {
            position: relative;
            z-index: 2;

            padding-bottom: 110px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .card {
            position: relative;

            padding: 28px;

            min-height: 210px;

            border: 1px solid var(--border);
            border-radius: 12px;

            background:
                linear-gradient(
                    145deg,
                    rgba(255,255,255,0.045),
                    rgba(255,255,255,0.012)
                );

            transition:
                border-color 180ms ease,
                transform 180ms ease;
        }

        .card:hover {
            border-color: var(--border-hover);
            transform: translateY(-2px);
        }

        .card-icon {
            width: 34px;
            height: 34px;

            display: grid;
            place-items: center;

            border: 1px solid var(--border);
            border-radius: 8px;

            background: rgba(255,255,255,0.035);
        }

        .card-icon svg {
            width: 17px;
            height: 17px;
            stroke: #bfc3ca;
        }

        .card h3 {
            margin: 28px 0 8px;

            font-size: 15px;
            font-weight: 600;

            letter-spacing: -0.02em;
        }

        .card p {
            margin: 0;

            color: var(--muted);

            font-size: 13px;
            line-height: 1.65;
        }

        /* --------------------------------
           Footer
        -------------------------------- */

        footer {
            position: relative;
            z-index: 2;

            border-top: 1px solid var(--border);
        }

        .footer-inner {
            min-height: 82px;

            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .footer-left {
            color: #62666d;
            font-size: 11px;
        }

        .footer-right {
            display: flex;
            align-items: center;
            gap: 14px;

            color: #62666d;

            font-size: 11px;

            font-family:
                "SFMono-Regular",
                Consolas,
                monospace;
        }

        .footer-separator {
            width: 3px;
            height: 3px;

            border-radius: 50%;

            background: #44474c;
        }

        /* --------------------------------
           Responsive
        -------------------------------- */

        @media (max-width: 760px) {

            .container {
                width: min(100% - 32px, 1120px);
            }

            nav {
                height: 70px;
            }

            .nav-link:nth-child(2) {
                display: none;
            }

            .hero {
                padding: 92px 0 80px;
            }

            h1 {
                font-size: clamp(48px, 15vw, 72px);
            }

            .hero-description {
                font-size: 15px;
            }

            .actions {
                flex-direction: column;
            }

            .button {
                width: 100%;
                max-width: 220px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .card {
                min-height: auto;
            }

            .footer-inner {
                flex-direction: column;
                justify-content: center;
                gap: 10px;
                padding: 24px 0;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <div class="grid"></div>
    <div class="glow"></div>

    <!-- Navigation -->

    <div class="container">

        <nav>

            <a href="{{ url('/') }}" class="brand">

                <span class="brand-mark">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                    </svg>

                </span>

                {{ config('electrik.name', 'Electrik') }}

            </a>

            <div class="nav-links">

                <a href="https://electrik.dev" class="nav-link" target="_blank" rel="noopener noreferrer">
                    Documentation
                </a>

                <a href="https://github.com/electrikhq/electrik" class="nav-link" target="_blank" rel="noopener noreferrer">
                    GitHub
                </a>

                @auth
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        Dashboard
                    </a>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="nav-link">
                            Log in
                        </a>
                    @endif
                @endauth

            </div>

        </nav>

    </div>


    <!-- Hero -->

    <main>

        <section class="hero">

            <div class="container">

                <div class="eyebrow">

                    <span class="status-dot"></span>

                    Your application is ready

                </div>


                <h1>

                    <span class="gradient-text">
                        Build something
                    </span>

                    <br>

                    <span class="gradient-text">
                        worth shipping.
                    </span>

                </h1>


                <p class="hero-description">

                    Electrik gives you a clean, opinionated starting point
                    for building modern Laravel applications — so you can
                    spend less time wiring things together and more time
                    building your product.

                </p>


                <div class="actions">

                    @auth
                        <a href="{{ route('dashboard') }}" class="button button-primary">

                            Go to dashboard

                            <svg
                                width="15"
                                height="15"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M5 12h14"/>
                                <path d="m12 5 7 7-7 7"/>
                            </svg>

                        </a>
                    @else
                        <a href="{{ Route::has('register') ? route('register') : (Route::has('login') ? route('login') : url('/')) }}" class="button button-primary">

                            Start building

                            <svg
                                width="15"
                                height="15"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M5 12h14"/>
                                <path d="m12 5 7 7-7 7"/>
                            </svg>

                        </a>
                    @endauth


                    <a href="https://electrik.dev" class="button button-secondary" target="_blank" rel="noopener noreferrer">

                        Read the documentation

                    </a>

                </div>


                <div class="stack">

                    <span class="stack-item">Laravel</span>
                    <span class="stack-item">Livewire</span>
                    <span class="stack-item">Tailwind CSS</span>
                    <span class="stack-item">Alpine.js</span>
                    <span class="stack-item">Vite</span>

                </div>

            </div>

        </section>


        <!-- Features -->

        <section class="features">

            <div class="container">

                <div class="feature-grid">


                    <article class="card">

                        <div class="card-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M12 3v18"/>
                                <path d="M3 12h18"/>
                                <path d="M5 5l14 14"/>
                                <path d="M19 5L5 19"/>
                            </svg>

                        </div>

                        <h3>
                            Start with intention
                        </h3>

                        <p>
                            A carefully considered foundation with the
                            essentials already wired together.
                        </p>

                    </article>


                    <article class="card">

                        <div class="card-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect x="3" y="3" width="18" height="18" rx="3"/>
                                <path d="M8 12h8"/>
                                <path d="M12 8v8"/>
                            </svg>

                        </div>

                        <h3>
                            Everything you need
                        </h3>

                        <p>
                            Authentication, UI primitives, sensible defaults
                            and developer tooling ready when you need them.
                        </p>

                    </article>


                    <article class="card">

                        <div class="card-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M12 3v18"/>
                                <path d="M5 7h14"/>
                                <path d="M5 17h14"/>
                            </svg>

                        </div>

                        <h3>
                            Make it yours
                        </h3>

                        <p>
                            No unnecessary abstraction. Take the foundation,
                            shape it around your product and ship.
                        </p>

                    </article>


                </div>

            </div>

        </section>

    </main>


    <!-- Footer -->

    <footer>

        <div class="container">

            <div class="footer-inner">

                <div class="footer-left">

                    Powered by {{ config('electrik.name', 'Electrik') }}

                </div>

                <div class="footer-right">

                    <span>Laravel</span>

                    <span class="footer-separator"></span>

                    <span>{{ config('electrik.version', '5.x') }}</span>

                </div>

            </div>

        </div>

    </footer>

</div>

</body>
</html>
