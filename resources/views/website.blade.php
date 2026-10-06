@php
    $name = optional($company)->name ?: 'Oppah Logistics & Timber Supply';
    $phone = optional($company)->phone_number ?: '768952479';
    $phoneDigits = '255'.ltrim(preg_replace('/\D/', '', $phone), '0');
    $phoneIntl = '+'.$phoneDigits;
    $phoneShow = '+255 '.implode(' ', str_split(substr($phoneDigits, 3), 3));
    $email = optional($company)->email_address ?: 'info@oppah01.co.tz';
    $location = 'Kongowe Mzinga, Dar es Salaam';
    // Map pin: replace with the yard's exact coordinates ("lat,lng") once known.
    $mapQuery = rawurlencode('Mzinga, Kongowe, Dar es Salaam, Tanzania');
    // WhatsApp link with a ready-made message.
    $wa = fn ($text) => 'https://wa.me/'.$phoneDigits.'?text='.rawurlencode($text);
    $img = fn ($file) => route('website-img', $file.'.jpg');
@endphp
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $name }} | Timber Supply &amp; Truck Transport in Tanzania</title>
<meta name="description" content="{{ $name }}: timber (mbao) supply from Kongowe Mzinga and truck transport across Tanzania. Call or WhatsApp {{ $phoneShow }}.">
<link rel="icon" href="{{ asset('assets/images/logo/oppah.png') }}">
<script>document.documentElement.classList.add('js');</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --blue: #4c73aa; --blue-dark: #2f4f7f; --green: #3f8f3a; --green-soft: #eaf4e8;
        --ink: #1d2733; --muted: #5d6b7a; --bg: #ffffff; --soft: #f4f7fb; --line: #e3e8ef;
        --night: #0f1a28; --wa: #25d366; --head: 72px;
    }
    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body { margin: 0; font-family: Inter, system-ui, sans-serif; color: var(--ink); background: var(--bg); line-height: 1.6; overflow-x: clip; }
    a { color: var(--blue); text-decoration: none; }
    img { max-width: 100%; }
    .wrap { max-width: 1160px; margin: 0 auto; padding: 0 16px; }
    .eyebrow { color: var(--green); font-weight: 700; letter-spacing: .08em; text-transform: uppercase; font-size: 13px; }
    h1 { font-size: 54px; line-height: 1.08; margin: 12px 0 18px; letter-spacing: -.02em; }
    h2 { font-size: 34px; line-height: 1.2; margin: 6px 0 12px; letter-spacing: -.01em; }
    h3 { font-size: 20px; margin: 0 0 8px; }
    .muted { color: var(--muted); }

    /* Buttons */
    .btn { display: inline-flex; align-items: center; gap: 10px; padding: 12px 22px; border-radius: 999px; font-weight: 600; font-size: 15px; border: 1.5px solid transparent; cursor: pointer; transition: background .2s, color .2s; }
    .btn-primary { background: var(--blue); color: #fff; }
    .btn-primary:hover { background: var(--blue-dark); }
    .btn-wa { background: var(--wa); color: #0b3d1f; }
    .btn-wa:hover { background: #1fb457; }
    .btn-light { border-color: #fff; color: #fff; }
    .btn-light:hover { background: rgba(255,255,255,.14); }
    .btn .arrow { display: grid; place-items: center; width: 28px; height: 28px; margin-right: -12px; border-radius: 50%; background: #fff; color: var(--blue); font-size: 15px; }
    .btn-sm { padding: 8px 16px; font-size: 14px; }

    /* Glass: see-through panel, strong blur, thin bright gradient edge */
    .glass { position: relative; background: rgba(255,255,255,.12); -webkit-backdrop-filter: blur(20px) saturate(150%); backdrop-filter: blur(20px) saturate(150%); box-shadow: 0 10px 30px rgba(0,0,0,.18); }
    .glass::before { content: ""; position: absolute; inset: 0; border-radius: inherit; padding: 1px; pointer-events: none;
        background: linear-gradient(135deg, rgba(255,255,255,.65), rgba(255,255,255,.08) 40%, rgba(255,255,255,.08) 60%, rgba(255,255,255,.45));
        -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite: xor; mask-composite: exclude; }
    .glass-dark { background: rgba(15,26,40,.45); }
    @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
        .glass { background: rgba(15,26,40,.82); }
    }
    .btn-glass { color: #fff; border-radius: 999px; }
    .btn-glass:hover { background: rgba(255,255,255,.22); }

    /* Header: logo + floating glass menu bar + glass login, over every section */
    header { position: fixed; top: 0; left: 0; right: 0; z-index: 20; padding-top: 14px; pointer-events: none; }
    header .wrap { display: flex; align-items: center; justify-content: space-between; height: 58px; gap: 12px; }
    header .wrap > * { pointer-events: auto; }
    .brand { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 18px; color: #fff; }
    .brand img { height: 48px; background: #fff; border-radius: 14px; padding: 4px 8px; box-shadow: 0 6px 18px rgba(0,0,0,.18); }
    .brand span { text-shadow: 0 1px 8px rgba(0,0,0,.4); }
    nav { display: flex; align-items: center; gap: 4px; padding: 6px; border-radius: 999px; transition: background .25s; }
    nav a { color: #fff; font-weight: 500; font-size: 15px; padding: 9px 16px; border-radius: 999px; transition: background .2s; }
    nav a:hover, nav a.active { background: rgba(0,0,0,.35); color: #fff; }
    header.solid nav, header.solid .login-pill, header.solid .menu-btn { background: rgba(15,26,40,.62); }
    header.solid .brand span { color: var(--ink); text-shadow: none; }
    .login-pill { padding: 6px 6px 6px 18px; font-size: 15px; }
    .head-right { display: flex; align-items: center; gap: 8px; }
    .menu-btn { display: none; width: 48px; height: 48px; border: 0; border-radius: 50%; font-size: 20px; color: #fff; cursor: pointer; }

    /* Hero */
    .hero { position: relative; overflow: hidden; min-height: 100vh; min-height: 100svh; display: flex; align-items: center; padding: calc(var(--head) + 40px) 0 90px; background: var(--night); color: #fff; }
    .hero .wrap { position: relative; z-index: 2; width: 100%; }
    .hero-copy { max-width: 660px; }
    .hero-slides { position: absolute; inset: 0; z-index: 0; }
    .hero-slides div { position: absolute; inset: 0; background-size: cover; background-position: center 40%; opacity: 0; transform: scale(1.06); transition: opacity 1.2s ease, transform 7s ease; }
    .hero-slides div.on { opacity: 1; transform: scale(1); }
    .hero::after { content: ""; position: absolute; inset: 0; z-index: 1; background: linear-gradient(90deg, rgba(15,26,40,.9) 0%, rgba(15,26,40,.6) 55%, rgba(15,26,40,.2) 100%), linear-gradient(180deg, rgba(15,26,40,.55) 0%, transparent 25%); }
    .hero .eyebrow { color: #8fd18a; }
    .hero h1 span { color: #f2b56b; }
    .hero .lead { font-size: 19px; color: rgba(255,255,255,.88); max-width: 560px; margin: 0; }
    .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 30px; }
    .hero-dots { position: absolute; z-index: 2; left: 0; right: 0; bottom: 28px; display: flex; justify-content: center; gap: 8px; }
    .hero-dots button { width: 10px; height: 10px; padding: 0; border-radius: 50%; border: 0; background: rgba(255,255,255,.45); cursor: pointer; }
    .hero-dots button.on { background: #fff; width: 26px; border-radius: 5px; }

    /* Sections */
    section { padding: 88px 0; scroll-margin-top: var(--head); }
    section.alt { background: var(--soft); }
    .section-head { max-width: 700px; margin: 0 0 44px; }
    .section-head.center { text-align: center; margin-left: auto; margin-right: auto; }
    .section-head p { color: var(--muted); margin: 0; font-size: 17px; }

    /* About */
    .about { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: center; }
    .about-photo { position: relative; border-radius: 20px; overflow: hidden; aspect-ratio: 4 / 5; max-height: 560px; box-shadow: 0 24px 50px rgba(15,26,40,.18); }
    .about-photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .about-photo .badge { position: absolute; left: 16px; bottom: 16px; background: #fff; border-radius: 12px; padding: 10px 14px; font-weight: 700; font-size: 14px; box-shadow: 0 8px 20px rgba(0,0,0,.15); }
    .about-photo .badge small { display: block; color: var(--muted); font-weight: 500; }
    .points { list-style: none; padding: 0; margin: 24px 0 0; display: grid; gap: 14px; }
    .points li { display: flex; gap: 12px; align-items: flex-start; }
    .points .tick { flex: none; width: 26px; height: 26px; border-radius: 50%; background: var(--green-soft); color: var(--green); display: grid; place-items: center; font-weight: 800; font-size: 14px; margin-top: 1px; }

    /* Services: full photo cards with a glass panel at the bottom */
    .services { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
    .service { position: relative; border-radius: 26px; overflow: hidden; min-height: 460px; display: flex; align-items: flex-end; padding: 14px; color: #fff; background: var(--night); }
    .service > img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .5s; }
    .service::after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, transparent 35%, rgba(15,26,40,.55)); }
    .service:hover > img { transform: scale(1.05); }
    .service .body { position: relative; z-index: 1; border-radius: 20px; padding: 20px; width: 100%; }
    .service h3 { color: #fff; }
    .service p { color: rgba(255,255,255,.88); margin: 0 0 14px; font-size: 15px; }
    .service .more { color: #fff; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
    .service .more .arrow { display: grid; place-items: center; width: 26px; height: 26px; border-radius: 50%; background: #fff; color: var(--ink); font-size: 14px; }

    /* Customers we serve (tabs) */
    .tabs { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 26px; }
    .tabs button { font: inherit; font-weight: 600; font-size: 15px; padding: 10px 18px; border-radius: 999px; border: 1.5px solid var(--line); background: #fff; color: var(--ink); cursor: pointer; }
    .tabs button[aria-selected="true"] { background: var(--ink); border-color: var(--ink); color: #fff; }
    .panel { display: grid; grid-template-columns: 1.1fr .9fr; gap: 36px; align-items: center; background: #fff; border: 1px solid var(--line); border-radius: 18px; padding: 32px; }
    .panel[hidden] { display: none; }
    .panel p { color: var(--muted); font-size: 17px; margin: 0 0 14px; }
    .panel ul { margin: 0; padding-left: 20px; }
    .panel li { margin: 6px 0; }
    .panel .pic { border-radius: 14px; overflow: hidden; aspect-ratio: 4 / 3; }
    .panel .pic img { width: 100%; height: 100%; object-fit: cover; display: block; }

    /* Fleet gallery */
    .fleet { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
    .fleet button { padding: 0; border: 0; background: none; cursor: zoom-in; border-radius: 14px; overflow: hidden; aspect-ratio: 3 / 4; display: block; }
    .fleet button:first-child { grid-column: span 2; grid-row: span 2; aspect-ratio: auto; }
    .fleet img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .3s; }
    .fleet button:hover img, .fleet button:focus-visible img { transform: scale(1.04); }
    .lightbox { position: fixed; inset: 0; z-index: 50; background: rgba(10,15,25,.92); display: none; align-items: center; justify-content: center; padding: 16px; }
    .lightbox.open { display: flex; }
    .lightbox img { max-width: 100%; max-height: 90vh; border-radius: 8px; }
    .lightbox .close { position: absolute; top: 12px; right: 16px; background: none; border: 0; color: #fff; font-size: 38px; line-height: 1; cursor: pointer; }

    /* Our system */
    .grid3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
    .feature { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 22px; }
    .feature h4 { margin: 0 0 6px; font-size: 17px; }
    .feature p { margin: 0; color: var(--muted); font-size: 15px; }
    .tag { display: inline-block; font-size: 12px; font-weight: 600; padding: 2px 10px; border-radius: 99px; margin-bottom: 10px; }
    .tag.t { background: var(--green-soft); color: var(--green); }
    .tag.l { background: #e8eef7; color: var(--blue); }

    /* Find us */
    .findus { display: grid; grid-template-columns: .8fr 1.2fr; gap: 28px; align-items: stretch; }
    .info-wrap { position: relative; border-radius: 26px; overflow: hidden; padding: 14px; display: flex; background: var(--night) center / cover no-repeat; }
    .info-card { position: relative; z-index: 1; flex: 1; color: #fff; border-radius: 20px; padding: 28px; display: flex; flex-direction: column; gap: 18px; }
    .info-card .row strong { display: block; color: #8fd18a; font-size: 13px; letter-spacing: .06em; text-transform: uppercase; }
    .info-card a { color: #fff; }
    .info-card .row span, .info-card .row a { font-size: 17px; }
    .map { border-radius: 26px; overflow: hidden; min-height: 380px; border: 1px solid var(--line); background: var(--soft); }
    .map iframe { width: 100%; height: 100%; min-height: 380px; border: 0; display: block; }

    /* Quote band: truck photo with a glass card on top */
    .quote { position: relative; border-radius: 30px; overflow: hidden; min-height: 420px; display: flex; align-items: center; justify-content: flex-end; padding: 32px; color: #fff; background: var(--night) center 55% / cover no-repeat; }
    .quote::after { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(15,26,40,.1), rgba(15,26,40,.45)); }
    .quote .glass { z-index: 1; max-width: 520px; border-radius: 26px; padding: 32px; }
    .quote h2 { margin: 0 0 10px; color: #fff; }
    .quote p { margin: 0; color: rgba(255,255,255,.9); }
    .staff { margin-top: 22px; border: 1px solid var(--line); border-radius: 18px; padding: 24px 28px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }
    .staff p { margin: 0; color: var(--muted); }

    /* Footer */
    footer { background: var(--night); color: #b9c3cf; padding: 56px 0 24px; font-size: 15px; }
    .foot-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 32px; }
    footer h4 { color: #fff; margin: 0 0 12px; font-size: 15px; }
    footer a { color: #b9c3cf; }
    footer a:hover { color: #fff; }
    footer ul { list-style: none; margin: 0; padding: 0; display: grid; gap: 8px; }
    footer .brand img { height: 52px; }
    .foot-bottom { border-top: 1px solid rgba(255,255,255,.12); margin-top: 40px; padding-top: 18px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; font-size: 14px; }

    /* Floating WhatsApp */
    .wa-float { position: fixed; right: 18px; bottom: 18px; z-index: 30; width: 58px; height: 58px; border-radius: 50%; background: var(--wa); display: grid; place-items: center; box-shadow: 0 10px 24px rgba(0,0,0,.25); transition: transform .2s; }
    .wa-float:hover { transform: scale(1.07); }
    .wa-float svg { width: 32px; height: 32px; fill: #fff; }

    @media (prefers-reduced-motion: reduce) { .hero-slides div { transition: none; transform: none; } .service, .fleet img { transition: none; } }

    /* ---------- Scroll animations (only when JS runs and motion is allowed) ---------- */
    html.lenis, html.lenis body { height: auto; }
    .lenis.lenis-smooth { scroll-behavior: auto !important; }
    .lenis.lenis-smooth [data-lenis-prevent] { overscroll-behavior: contain; }
    .progress { position: fixed; top: 0; left: 0; right: 0; height: 3px; z-index: 40; background: linear-gradient(90deg, var(--green), #8fd18a 50%, var(--blue)); transform-origin: 0 50%; transform: scaleX(0); pointer-events: none; }
    @media (prefers-reduced-motion: no-preference) {
        /* Fade + slide up */
        .js [data-reveal] { opacity: 0; transform: translateY(48px); transition: opacity .9s cubic-bezier(.2,.7,.2,1), transform .9s cubic-bezier(.2,.7,.2,1); transition-delay: calc(var(--i, 0) * 110ms); }
        .js [data-reveal="left"] { transform: translateX(-56px); }
        .js [data-reveal="right"] { transform: translateX(56px); }
        .js [data-reveal="zoom"] { transform: scale(.92); }
        @media (max-width: 960px) { .js [data-reveal="left"], .js [data-reveal="right"] { transform: translateY(48px); } }
        .js [data-reveal].in { opacity: 1; transform: none; }
        /* Headings: words rise out of a mask one after another */
        .js .split .w { display: inline-block; overflow: hidden; vertical-align: top; padding-bottom: .08em; margin-bottom: -.08em; }
        .js .split .w > span { display: inline-block; transform: translateY(110%); transition: transform .85s cubic-bezier(.2,.75,.2,1); transition-delay: calc(var(--i, 0) * 55ms); }
        .js .split.in .w > span { transform: none; }
        /* Photos: unveil from a curtain and settle from a slight zoom */
        .js [data-unveil] { clip-path: inset(12% 12% 12% 12% round 26px); transition: clip-path 1.2s cubic-bezier(.2,.7,.2,1); }
        .js [data-unveil].in { clip-path: inset(0 0 0 0 round 26px); }
        .js [data-unveil] img { transform: scale(1.18); transition: transform 1.6s cubic-bezier(.2,.7,.2,1); }
        .js [data-unveil].in img { transform: scale(1.06); }
        /* Hero entrance on page load */
        .js .hero-copy > * { opacity: 0; transform: translateY(30px); animation: heroIn 1s cubic-bezier(.2,.7,.2,1) forwards; }
        .js .hero-copy > :nth-child(1) { animation-delay: .15s; }
        .js .hero-copy > :nth-child(2) { animation-delay: .3s; }
        .js .hero-copy > :nth-child(3) { animation-delay: .5s; }
        .js .hero-copy > :nth-child(4) { animation-delay: .65s; }
        .js header .wrap > * { opacity: 0; animation: heroIn .9s .1s cubic-bezier(.2,.7,.2,1) forwards; }
        @keyframes heroIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: none; } }
        [data-parallax] { will-change: transform; }
    }

    @media (max-width: 960px) {
        h1 { font-size: 40px; }
        h2 { font-size: 28px; }
        section { padding: 64px 0; }
        .about, .findus, .panel { grid-template-columns: 1fr; }
        .about { gap: 32px; }
        .about-photo { aspect-ratio: 4 / 3; }
        .services, .grid3 { grid-template-columns: 1fr 1fr; }
        .fleet { grid-template-columns: repeat(2, 1fr); }
        .foot-grid { grid-template-columns: 1fr 1fr; }
        .hero::after { background: linear-gradient(180deg, rgba(15,26,40,.6) 0%, rgba(15,26,40,.88) 100%); }
        .menu-btn { display: block; }
        .service { min-height: 420px; }
        .quote { justify-content: center; min-height: 0; padding: 16px; }
        nav#siteNav { display: none; position: absolute; top: 80px; left: 16px; right: 16px; flex-direction: column; align-items: stretch; gap: 2px; padding: 10px; border-radius: 24px; background: rgba(15,26,40,.72); }
        nav#siteNav.open { display: flex; }
        nav a { padding: 13px 16px; font-size: 16px; }
    }
    @media (max-width: 600px) {
        h1 { font-size: 32px; }
        .hero { padding-bottom: 80px; }
        .hero .lead { font-size: 17px; }
        .services, .grid3, .foot-grid { grid-template-columns: 1fr; }
        .panel, .info-card, .quote .glass { padding: 24px; }
        .brand span, .login-pill .label-long { display: none; }
        .actions .btn { flex: 1 1 auto; justify-content: center; }
    }
</style>
</head>
<body>

<div class="progress" aria-hidden="true"></div>
<header id="siteHeader">
    <div class="wrap">
        <a class="brand" href="#top"><img src="{{ asset('assets/images/logo/oppah.png') }}" alt="{{ $name }} logo"><span>Oppah</span></a>
        <nav id="siteNav" class="glass">
            <a href="#about">About us</a>
            <a href="#services">Services</a>
            <a href="#customers">Who we serve</a>
            <a href="#fleet">Our fleet</a>
            <a href="#findus">Find us</a>
        </nav>
        <div class="head-right">
            <a class="btn btn-glass glass login-pill" href="{{ route('login') }}" target="_top"><span>Staff<span class="label-long"> login</span></span><span class="arrow">&rarr;</span></a>
            <button type="button" class="menu-btn glass" aria-label="Open menu" aria-expanded="false" aria-controls="siteNav">&#9776;</button>
        </div>
    </div>
</header>

@php($slides = ['truck-2', 'truck-1', 'truck-3', 'truck-5', 'truck-4', 'truck-6'])
<div class="hero" id="top">
    <div class="hero-slides" aria-hidden="true">
        @foreach($slides as $i => $slide)
            <div class="{{ $i === 0 ? 'on' : '' }}" @if($i === 0) style="background-image:url('{{ $img($slide) }}')" @else data-bg="{{ $img($slide) }}" @endif></div>
        @endforeach
    </div>
    <div class="wrap">
        <div class="hero-copy">
            <div class="eyebrow">Kongowe Mzinga &middot; Dar es Salaam</div>
            <h1>Timber you can build on. <span>Trucks you can count on.</span></h1>
            <p class="lead">We supply quality timber (mbao) and move it, and your cargo, with our own Scania fleet across Tanzania.</p>
            <div class="actions">
                <a class="btn btn-wa" href="{{ $wa('Hello Oppah, I would like a quote for ') }}" target="_blank" rel="noopener">WhatsApp for a quote</a>
                <a class="btn btn-glass glass" href="#services">Our services <span class="arrow">&rarr;</span></a>
            </div>
        </div>
    </div>
    <div class="hero-dots">
        @foreach($slides as $i => $slide)
            <button type="button" class="{{ $i === 0 ? 'on' : '' }}" aria-label="Show photo {{ $i + 1 }}"></button>
        @endforeach
    </div>
</div>

<section id="about">
    <div class="wrap about">
        <div>
            <div class="eyebrow">About us</div>
            <h2>One company for timber and transport</h2>
            <p class="muted">{{ $name }} is based at Kongowe Mzinga in Dar es Salaam. We sell sawn timber from our own yard and run our own trucks, so we control the whole journey: from the timber we buy, to the load on the truck, to the delivery at your site.</p>
            <ul class="points">
                <li><span class="tick">&check;</span><span><strong>Our own yard.</strong> Timber in common sizes, ready to load at Mzinga.</span></li>
                <li><span class="tick">&check;</span><span><strong>Our own trucks.</strong> A modern Scania fleet driven by our own drivers.</span></li>
                <li><span class="tick">&check;</span><span><strong>Proper paperwork.</strong> Invoices, receipts and customer statements for every sale.</span></li>
                <li><span class="tick">&check;</span><span><strong>Every trip followed.</strong> Our office tracks each truck from loading to delivery.</span></li>
            </ul>
        </div>
        <div class="about-photo">
            <img src="{{ $img('truck-3') }}" alt="Oppah truck carrying a full timber load" loading="lazy">
            <div class="badge">Timber + Transport<small>Kongowe Mzinga, Dar es Salaam</small></div>
        </div>
    </div>
</section>

<section class="alt" id="services">
    <div class="wrap">
        <div class="section-head center">
            <div class="eyebrow">Our services</div>
            <h2>What we can do for you</h2>
            <p>Buy timber, hire a truck, or both. We load it, move it and deliver it.</p>
        </div>
        <div class="services">
            <article class="service">
                <img src="{{ $img('truck-4') }}" alt="Oppah truck loaded with timber" loading="lazy">
                <div class="body glass">
                    <h3>Timber supply (Mbao)</h3>
                    <p>Sawn timber for construction, roofing, formwork and furniture, from our yard at Kongowe Mzinga. Cash or credit, always invoiced.</p>
                    <a class="more" href="{{ $wa('Hello Oppah, I want to buy timber. Sizes and quantity: ') }}" target="_blank" rel="noopener">Ask for timber prices <span class="arrow">&rarr;</span></a>
                </div>
            </article>
            <article class="service">
                <img src="{{ $img('truck-6') }}" alt="Oppah truck carrying cargo" loading="lazy">
                <div class="body glass">
                    <h3>Truck hire &amp; transport</h3>
                    <p>Our trucks carry timber and general cargo, short and long distance, with every trip planned and followed by our office.</p>
                    <a class="more" href="{{ $wa('Hello Oppah, I need a truck. From: ... To: ... Cargo: ...') }}" target="_blank" rel="noopener">Book a truck <span class="arrow">&rarr;</span></a>
                </div>
            </article>
            <article class="service">
                <img src="{{ $img('truck-1') }}" alt="Oppah Scania truck ready for delivery" loading="lazy">
                <div class="body glass">
                    <h3>Delivery to your site</h3>
                    <p>Buy your timber and we bring it to your site, workshop or shop with our own truck. No need to find transport.</p>
                    <a class="more" href="{{ $wa('Hello Oppah, I want timber delivered to: ') }}" target="_blank" rel="noopener">Arrange a delivery <span class="arrow">&rarr;</span></a>
                </div>
            </article>
        </div>
    </div>
</section>

<section id="customers">
    <div class="wrap">
        <div class="section-head">
            <div class="eyebrow">Who we serve</div>
            <h2>Built around our customers</h2>
            <p>From one-off orders to regular supply, these are the people we work with every day.</p>
        </div>
        @php($groups = [
            ['builders', 'Builders & contractors', 'truck-5', 'Steady timber supply for building projects, delivered when the site needs it.', ['Formwork, roofing and scaffolding timber', 'Large orders delivered straight to site', 'Credit for regular customers, with statements']],
            ['carpenters', 'Carpenters & furniture', 'truck-2', 'Good timber for workshops making doors, windows, beds and furniture.', ['A range of sizes from one yard', 'Pick up at Mzinga or get it delivered', 'Clear receipts for every purchase']],
            ['shops', 'Hardware shops', 'truck-4', 'Restock your shop with timber in the sizes your customers ask for.', ['Wholesale quantities', 'Regular deliveries with our trucks', 'Invoices and payment records you can trust']],
            ['cargo', 'Cargo customers', 'truck-6', 'Need goods moved? Hire our trucks for cargo across Tanzania.', ['Short and long distance trips', 'Experienced drivers, followed by our office', 'One call or WhatsApp to book']],
        ])
        <div class="tabs" role="tablist" aria-label="Customers we serve">
            @foreach($groups as $i => [$key, $label])
                <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}" tabindex="{{ $i === 0 ? '0' : '-1' }}">{{ $label }}</button>
            @endforeach
        </div>
        @foreach($groups as $i => [$key, $label, $photo, $intro, $items])
            <div class="panel" role="tabpanel" id="panel-{{ $key }}" aria-labelledby="tab-{{ $key }}" @if($i !== 0) hidden @endif>
                <div>
                    <h3>{{ $label }}</h3>
                    <p>{{ $intro }}</p>
                    <ul>
                        @foreach($items as $item)<li>{{ $item }}</li>@endforeach
                    </ul>
                </div>
                <div class="pic"><img src="{{ $img($photo) }}" alt="Oppah truck" loading="lazy"></div>
            </div>
        @endforeach
    </div>
</section>

<section class="alt" id="fleet">
    <div class="wrap">
        <div class="section-head center">
            <div class="eyebrow">Our fleet</div>
            <h2>Our trucks on the road</h2>
            <p>A modern Scania fleet in Oppah colours, carrying timber and cargo across Tanzania.</p>
        </div>
        <div class="fleet">
            @foreach([
                ['truck-1', 'Oppah Scania truck'],
                ['truck-5', 'Oppah truck loaded with timber'],
                ['truck-3', 'Oppah truck carrying a full timber load'],
                ['truck-4', 'Oppah truck loaded with cargo'],
                ['truck-6', 'Oppah truck on a village road'],
                ['truck-7', 'Oppah truck being prepared for a trip'],
            ] as [$file, $alt])
                <button type="button" data-full="{{ $img($file) }}" aria-label="View larger: {{ $alt }}">
                    <img src="{{ $img($file) }}" alt="{{ $alt }}" loading="lazy">
                </button>
            @endforeach
        </div>
    </div>
</section>

<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Truck photo">
    <button type="button" class="close" aria-label="Close">&times;</button>
    <img src="" alt="">
</div>

<section id="system">
    <div class="wrap">
        <div class="section-head center">
            <div class="eyebrow">How we work</div>
            <h2>Every sale and every trip on record</h2>
            <p>Our own system connects the timber yard, the trucks and the office, so you get accurate invoices and we always know where your order is.</p>
        </div>
        <div class="grid3">
            <div class="feature"><span class="tag t">Timber</span><h4>Live stock</h4><p>We know what is in the yard before we quote you.</p></div>
            <div class="feature"><span class="tag t">Timber</span><h4>Invoices &amp; statements</h4><p>Every sale is invoiced, and every payment shows on your statement.</p></div>
            <div class="feature"><span class="tag t">Timber</span><h4>Payment reminders</h4><p>Clear reminders with a QR code to check your balance.</p></div>
            <div class="feature"><span class="tag l">Trucks</span><h4>Planned trips</h4><p>Each trip has a truck, driver, route and cargo before it leaves.</p></div>
            <div class="feature"><span class="tag l">Trucks</span><h4>Followed on the map</h4><p>Our drivers' app shows the office where each truck is.</p></div>
            <div class="feature"><span class="tag l">Trucks</span><h4>Checked every day</h4><p>Sales, deposits and costs are closed and checked daily.</p></div>
        </div>
    </div>
</section>

<section class="alt" id="findus">
    <div class="wrap">
        <div class="section-head">
            <div class="eyebrow">Find us</div>
            <h2>Visit our yard at Kongowe Mzinga</h2>
            <p>Come and see the timber, or call us and we will bring it to you.</p>
        </div>
        <div class="findus">
            <div class="info-wrap" style="background-image:url('{{ $img('truck-5') }}')">
            <div class="info-card glass glass-dark">
                <div class="row"><strong>Location</strong><span>{{ $location }}</span></div>
                <div class="row"><strong>Phone &amp; WhatsApp</strong><a href="tel:{{ $phoneIntl }}">{{ $phoneShow }}</a></div>
                <div class="row"><strong>Email</strong><a href="mailto:{{ $email }}">{{ $email }}</a></div>
                <div class="actions" style="margin-top:auto">
                    <a class="btn btn-wa btn-sm" href="{{ $wa('Hello Oppah, how do I get to your yard at Kongowe Mzinga?') }}" target="_blank" rel="noopener">WhatsApp us</a>
                    <a class="btn btn-glass glass btn-sm" href="https://www.google.com/maps/search/?api=1&query={{ $mapQuery }}" target="_blank" rel="noopener">Open in Google Maps</a>
                </div>
            </div>
            </div>
            <div class="map">
                <iframe src="https://www.google.com/maps?q={{ $mapQuery }}&z=14&output=embed" title="Map: {{ $location }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</section>

<section id="contact">
    <div class="wrap">
        <div class="quote" style="background-image:url('{{ $img('truck-3') }}')">
            <div class="glass">
                <h2>Need timber or a truck this week?</h2>
                <p>Send us the sizes, the quantity or the route. We reply with a price and a delivery time.</p>
                <div class="actions" style="margin-top:22px">
                    <a class="btn btn-wa" href="{{ $wa('Hello Oppah, I would like a quote for ') }}" target="_blank" rel="noopener">WhatsApp for a quote</a>
                    <a class="btn btn-glass glass" href="tel:{{ $phoneIntl }}">Call {{ $phoneShow }}</a>
                </div>
            </div>
        </div>
        <div class="staff">
            <div>
                <strong>Oppah staff portal</strong>
                <p>Sales, stock, trucks and trips: log in to the company system.</p>
            </div>
            <a class="btn btn-primary" href="{{ route('login') }}" target="_top">Staff login <span class="arrow">&rarr;</span></a>
        </div>
    </div>
</section>

<footer>
    <div class="wrap">
        <div class="foot-grid">
            <div>
                <a class="brand" href="#top"><img src="{{ asset('assets/images/logo/oppah.png') }}" alt="{{ $name }} logo"></a>
                <p style="margin:14px 0 0">Timber supply and truck transport from Kongowe Mzinga, Dar es Salaam.</p>
            </div>
            <div>
                <h4>Quick links</h4>
                <ul>
                    <li><a href="#about">About us</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#customers">Who we serve</a></li>
                    <li><a href="#fleet">Our fleet</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <ul>
                    <li><a href="tel:{{ $phoneIntl }}">{{ $phoneShow }}</a></li>
                    <li><a href="{{ $wa('Hello Oppah') }}" target="_blank" rel="noopener">WhatsApp</a></li>
                    <li><a href="mailto:{{ $email }}">{{ $email }}</a></li>
                    <li>{{ $location }}</li>
                </ul>
            </div>
            <div>
                <h4>Staff</h4>
                <ul>
                    <li><a href="{{ route('login') }}" target="_top">Staff login</a></li>
                </ul>
            </div>
        </div>
        <div class="foot-bottom">
            <span>&copy; {{ date('Y') }} {{ $name }}. All rights reserved.</span>
            <span>Smart Generation In Smart Business</span>
        </div>
    </div>
</footer>

<a class="wa-float" href="{{ $wa('Hello Oppah') }}" target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp">
    <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M16 3C9 3 3.3 8.6 3.3 15.6c0 2.5.7 4.8 2 6.8L3.2 29l6.8-2.1c1.9 1 4 1.6 6.1 1.6 7 0 12.7-5.7 12.7-12.7S23 3 16 3zm0 23.2c-2 0-3.9-.6-5.5-1.6l-.4-.2-4 1.2 1.3-3.9-.3-.4c-1.1-1.7-1.7-3.6-1.7-5.6C5.4 9.8 10.2 5.1 16 5.1s10.6 4.7 10.6 10.5S21.8 26.2 16 26.2zm5.8-7.9c-.3-.2-1.9-.9-2.2-1-.3-.1-.5-.2-.7.2-.2.3-.8 1-1 1.2-.2.2-.4.2-.7.1-.3-.2-1.3-.5-2.5-1.6-.9-.8-1.6-1.8-1.7-2.1-.2-.3 0-.5.1-.7l.5-.6c.2-.2.2-.3.3-.6.1-.2 0-.4 0-.6l-1-2.4c-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.3-1.2 1.2-1.2 2.9s1.2 3.4 1.4 3.6c.2.2 2.4 3.7 5.8 5.2 2.9 1.1 3.4.9 4.1.8.6-.1 1.9-.8 2.2-1.6.3-.8.3-1.4.2-1.6-.1-.1-.3-.2-.7-.4z"/></svg>
</a>

<script>
    // Header turns solid after scrolling past the top of the hero.
    (function () {
        var head = document.getElementById('siteHeader');
        function update() { head.classList.toggle('solid', window.scrollY > 40 || document.getElementById('siteNav').classList.contains('open')); }
        window.addEventListener('scroll', update, { passive: true });
        update();
        window.__updateHeader = update;
    })();

    // Phone menu: open/close, and close after picking a section.
    (function () {
        var btn = document.querySelector('.menu-btn'), nav = document.getElementById('siteNav');
        function set(open) { nav.classList.toggle('open', open); btn.setAttribute('aria-expanded', open); btn.innerHTML = open ? '&times;' : '&#9776;'; window.__updateHeader(); }
        btn.addEventListener('click', function () { set(!nav.classList.contains('open')); });
        nav.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', function () { set(false); }); });
    })();

    // Hero background carousel: crossfade every 5 s, pause when the tab is hidden.
    (function () {
        var slides = document.querySelectorAll('.hero-slides div'), dots = document.querySelectorAll('.hero-dots button');
        var current = 0, timer = null, still = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        slides.forEach(function (s) { var bg = s.getAttribute('data-bg'); if (bg) { s.style.backgroundImage = "url('" + bg + "')"; } });
        function show(n) {
            slides[current].classList.remove('on'); dots[current].classList.remove('on');
            current = (n + slides.length) % slides.length;
            slides[current].classList.add('on'); dots[current].classList.add('on');
        }
        function start() { if (!still) { clearInterval(timer); timer = setInterval(function () { show(current + 1); }, 5000); } }
        dots.forEach(function (d, i) { d.addEventListener('click', function () { show(i); start(); }); });
        document.addEventListener('visibilitychange', function () { document.hidden ? clearInterval(timer) : start(); });
        start();
    })();

    // "Who we serve" tabs (arrow keys move between tabs).
    (function () {
        var tabs = Array.prototype.slice.call(document.querySelectorAll('[role="tab"]'));
        function select(tab) {
            tabs.forEach(function (t) {
                var on = t === tab;
                t.setAttribute('aria-selected', on);
                t.tabIndex = on ? 0 : -1;
                document.getElementById(t.getAttribute('aria-controls')).hidden = !on;
            });
        }
        tabs.forEach(function (tab, i) {
            tab.addEventListener('click', function () { select(tab); });
            tab.addEventListener('keydown', function (e) {
                var next = e.key === 'ArrowRight' ? i + 1 : e.key === 'ArrowLeft' ? i - 1 : null;
                if (next === null) { return; }
                next = tabs[(next + tabs.length) % tabs.length];
                select(next); next.focus(); e.preventDefault();
            });
        });
    })();

    // Fleet photos: click to enlarge.
    (function () {
        var box = document.getElementById('lightbox'), img = box.querySelector('img');
        document.querySelectorAll('.fleet button').forEach(function (btn) {
            btn.addEventListener('click', function () {
                img.src = btn.getAttribute('data-full');
                img.alt = btn.querySelector('img').alt;
                box.classList.add('open');
            });
        });
        function close() { box.classList.remove('open'); img.src = ''; }
        box.addEventListener('click', function (e) { if (e.target !== img) close(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
    })();
</script>
<script src="https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js"></script>
<script>
    // Scroll animations: smooth scrolling, reveals, word-by-word headings, parallax, progress bar.
    (function () {
        if (!window.matchMedia('(prefers-reduced-motion: no-preference)').matches) { return; }
        var $ = function (sel) { return Array.prototype.slice.call(document.querySelectorAll(sel)); };

        // 1. Mark what animates (kept here so the markup stays clean).
        function mark(sel, kind) { $(sel).forEach(function (el) { el.setAttribute('data-reveal', kind || ''); }); }
        function group(sel) { $(sel).forEach(function (wrap) { Array.prototype.forEach.call(wrap.children, function (el, i) { el.setAttribute('data-reveal', ''); el.style.setProperty('--i', i % 4); }); }); }
        mark('.section-head .eyebrow, .section-head p, .about .eyebrow, .about p.muted');
        group('.services'); group('.grid3'); group('.fleet'); group('.points'); group('.foot-grid'); group('.tabs');
        mark('.panel, .staff'); mark('.info-wrap', 'left'); mark('.map', 'right'); mark('.quote .glass', 'zoom');
        $('.about-photo').forEach(function (el) { el.setAttribute('data-unveil', ''); });

        // 2. Split headings into words.
        function split(el) {
            var i = 0;
            (function walk(node) {
                Array.prototype.slice.call(node.childNodes).forEach(function (child) {
                    if (child.nodeType === 3) {
                        var frag = document.createDocumentFragment();
                        child.textContent.split(/(\s+)/).forEach(function (part) {
                            if (!part) { return; }
                            if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(' ')); return; }
                            var w = document.createElement('span'), inner = document.createElement('span');
                            w.className = 'w'; inner.textContent = part; inner.style.setProperty('--i', i++);
                            w.appendChild(inner); frag.appendChild(w);
                        });
                        node.replaceChild(frag, child);
                    } else if (child.nodeType === 1) { walk(child); }
                });
            })(el);
            el.classList.add('split');
        }
        $('section h2, .quote h2').forEach(split);

        // 3. Reveal when 15% of an element enters the screen (once).
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
        $('[data-reveal], .split, [data-unveil]').forEach(function (el) { io.observe(el); });

        // 4. Parallax: photos drift slower than the page.
        var layers = [];
        $('.hero-slides').forEach(function (el) { layers.push({ el: el, speed: 0.35, hero: true }); });
        $('.service > img, .panel .pic img').forEach(function (el) { el.style.transition = 'none'; layers.push({ el: el, speed: 0.12 }); });
        $('.quote, .info-wrap').forEach(function (el) { layers.push({ el: el, speed: 0.15, bg: true }); });
        var bar = document.querySelector('.progress');
        function frame() {
            var vh = window.innerHeight, y = window.scrollY, max = document.documentElement.scrollHeight - vh;
            bar.style.transform = 'scaleX(' + (max > 0 ? y / max : 0) + ')';
            layers.forEach(function (l) {
                var r = l.el.getBoundingClientRect();
                if (r.bottom < -100 || r.top > vh + 100) { return; }
                if (l.hero) { l.el.style.transform = 'translate3d(0,' + (y * l.speed) + 'px,0)'; return; }
                var off = (r.top + r.height / 2 - vh / 2) * -l.speed;
                if (l.bg) { l.el.style.backgroundPosition = 'center calc(50% + ' + off + 'px)'; }
                else { l.el.style.transform = 'translate3d(0,' + off + 'px,0) scale(1.12)'; }
            });
        }

        // 5. Smooth scrolling (Lenis), with in-page links handled by it.
        var lenis = null;
        if (window.Lenis) {
            lenis = new Lenis({ duration: 1.15, smoothWheel: true });
            lenis.on('scroll', frame);
            (function raf(t) { lenis.raf(t); requestAnimationFrame(raf); })(performance.now());
            $('a[href^="#"]').forEach(function (a) {
                a.addEventListener('click', function (e) {
                    var id = a.getAttribute('href');
                    if (id.length < 2 || !document.querySelector(id)) { return; }
                    e.preventDefault();
                    lenis.scrollTo(id === '#top' ? 0 : id, { offset: id === '#top' ? 0 : -72 });
                });
            });
            $('.lightbox').forEach(function (el) { el.setAttribute('data-lenis-prevent', ''); });
        } else {
            window.addEventListener('scroll', function () { requestAnimationFrame(frame); }, { passive: true });
        }
        window.addEventListener('resize', frame);
        frame();
    })();
</script>
</body>
</html>
