@php
    $name = optional($company)->name ?: 'Oppah Logistics & Timber Supply';
    $phone = optional($company)->phone_number ?: '768952479';
    $phoneIntl = '+255'.ltrim(preg_replace('/\D/', '', $phone), '0');
    $email = optional($company)->email_address ?: 'info@oppah01.co.tz';
    $address = optional($company)->postal_address ?: 'Kongowe, Dar es Salaam';
@endphp
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $name }}</title>
<meta name="description" content="{{ $name }}: timber (mbao) supply and truck logistics in Tanzania.">
<link rel="icon" href="{{ asset('assets/images/logo/oppah.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --blue: #4c73aa; --blue-dark: #2f4f7f; --green: #3f8f3a; --green-soft: #eaf4e8;
        --ink: #1d2733; --muted: #5d6b7a; --bg: #ffffff; --soft: #f4f7fb; --line: #e3e8ef;
    }
    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body { margin: 0; font-family: Inter, system-ui, sans-serif; color: var(--ink); background: var(--bg); line-height: 1.6; }
    a { color: var(--blue); text-decoration: none; }
    .wrap { max-width: 1120px; margin: 0 auto; padding: 0 16px; }

    header { position: sticky; top: 0; z-index: 10; background: rgba(255,255,255,.95); backdrop-filter: blur(6px); border-bottom: 1px solid var(--line); }
    header .wrap { display: flex; align-items: center; justify-content: space-between; height: 68px; gap: 12px; }
    .brand { display: flex; align-items: center; gap: 10px; font-weight: 700; color: var(--ink); }
    .brand img { height: 44px; }
    nav a { margin-left: 22px; color: var(--muted); font-weight: 500; font-size: 15px; }
    nav a:hover { color: var(--blue); }
    .btn { display: inline-block; padding: 12px 22px; border-radius: 8px; font-weight: 600; font-size: 15px; }
    .btn-primary { background: var(--blue); color: #fff; }
    .btn-primary:hover { background: var(--blue-dark); }
    .btn-ghost { border: 1.5px solid var(--blue); color: var(--blue); }
    nav .btn { margin-left: 22px; padding: 8px 16px; color: #fff; }

    .hero { background: linear-gradient(135deg, var(--soft) 0%, #fff 60%, var(--green-soft) 100%); padding: 80px 0 72px; }
    .hero .wrap { display: grid; grid-template-columns: 1.2fr .8fr; gap: 48px; align-items: center; }
    .eyebrow { color: var(--green); font-weight: 700; letter-spacing: .08em; text-transform: uppercase; font-size: 13px; }
    h1 { font-size: 46px; line-height: 1.1; margin: 10px 0 18px; letter-spacing: -.02em; }
    h1 span { color: var(--blue); }
    .lead { font-size: 18px; color: var(--muted); max-width: 560px; }
    .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 28px; }
    .hero-photo { position: relative; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 50px rgba(47,79,127,.18); aspect-ratio: 3 / 4; max-height: 520px; margin-left: auto; }
    .hero-photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .hero-photo figcaption { position: absolute; left: 0; right: 0; bottom: 0; padding: 14px 18px; color: #fff; font-weight: 600; background: linear-gradient(transparent, rgba(0,0,0,.65)); }

    .fleet { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
    .fleet button { padding: 0; border: 0; background: none; cursor: zoom-in; border-radius: 12px; overflow: hidden; aspect-ratio: 3 / 4; display: block; }
    .fleet button:first-child { grid-column: span 2; grid-row: span 2; aspect-ratio: auto; }
    .fleet img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .3s; }
    .fleet button:hover img, .fleet button:focus-visible img { transform: scale(1.04); }
    .lightbox { position: fixed; inset: 0; z-index: 50; background: rgba(10,15,25,.92); display: none; align-items: center; justify-content: center; padding: 16px; }
    .lightbox.open { display: flex; }
    .lightbox img { max-width: 100%; max-height: 90vh; border-radius: 8px; }
    .lightbox .close { position: absolute; top: 12px; right: 16px; background: none; border: 0; color: #fff; font-size: 36px; line-height: 1; cursor: pointer; }

    section { padding: 72px 0; }
    section.alt { background: var(--soft); }
    .section-head { text-align: center; max-width: 680px; margin: 0 auto 44px; }
    h2 { font-size: 32px; margin: 6px 0 12px; letter-spacing: -.01em; }
    .section-head p { color: var(--muted); margin: 0; }

    .split { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .biz { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 32px; border-top: 5px solid var(--green); }
    .biz.trucks { border-top-color: var(--blue); }
    .biz .icon { width: 52px; height: 52px; border-radius: 12px; display: grid; place-items: center; background: var(--green-soft); color: var(--green); margin-bottom: 14px; }
    .biz.trucks .icon { background: #e8eef7; color: var(--blue); }
    .biz h3 { margin: 0 0 8px; font-size: 22px; }
    .biz p { color: var(--muted); margin: 0 0 14px; }
    .biz ul { margin: 0; padding-left: 20px; }
    .biz li { margin: 6px 0; }

    .grid3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .feature { background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: 24px; }
    .feature h4 { margin: 0 0 6px; font-size: 17px; }
    .feature p { margin: 0; color: var(--muted); font-size: 15px; }
    .tag { display: inline-block; font-size: 12px; font-weight: 600; padding: 2px 10px; border-radius: 99px; margin-bottom: 10px; }
    .tag.t { background: var(--green-soft); color: var(--green); }
    .tag.l { background: #e8eef7; color: var(--blue); }
    .tag.b { background: #f1ecf8; color: #6a4a9a; }

    .steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; counter-reset: step; }
    .step { text-align: center; padding: 0 8px; }
    .step::before { counter-increment: step; content: counter(step); display: grid; place-items: center; width: 44px; height: 44px; margin: 0 auto 12px; border-radius: 50%; background: var(--blue); color: #fff; font-weight: 700; }
    .step h4 { margin: 0 0 4px; }
    .step p { margin: 0; color: var(--muted); font-size: 15px; }

    .contact { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .contact div { background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: 24px; text-align: center; }
    .contact strong { display: block; margin-bottom: 4px; }
    .contact span, .contact a { color: var(--muted); word-break: break-word; }
    .cta { margin-top: 32px; text-align: center; }

    footer { background: var(--ink); color: #b9c3cf; padding: 28px 0; font-size: 14px; }
    footer .wrap { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    footer a { color: #fff; }

    @media (max-width: 900px) {
        .hero .wrap, .split { grid-template-columns: 1fr; }
        .grid3, .contact { grid-template-columns: 1fr 1fr; }
        .fleet { grid-template-columns: repeat(2, 1fr); }
        .hero-photo { margin: 0 auto; max-width: 420px; }
        .steps { grid-template-columns: 1fr 1fr; row-gap: 28px; }
        h1 { font-size: 36px; }
        nav a:not(.btn) { display: none; }
    }
    @media (max-width: 560px) {
        .grid3, .contact, .steps { grid-template-columns: 1fr; }
        .hero { padding: 48px 0; }
        h1 { font-size: 30px; }
        section { padding: 52px 0; }
        .brand span { display: none; }
    }
</style>
</head>
<body>

<header>
    <div class="wrap">
        <a class="brand" href="#top"><img src="{{ asset('assets/images/logo/oppah.png') }}" alt="{{ $name }} logo"><span>Oppah</span></a>
        <nav>
            <a href="#business">What we do</a>
            <a href="#fleet">Our fleet</a>
            <a href="#system">Our system</a>
            <a href="#contact">Contact</a>
            <a class="btn btn-primary" href="{{ route('login') }}" target="_top">Staff login</a>
        </nav>
    </div>
</header>

<div class="hero" id="top">
    <div class="wrap">
        <div>
            <div class="eyebrow">Timber supply &middot; Truck logistics</div>
            <h1>Quality timber, <span>delivered by our own trucks</span>.</h1>
            <p class="lead">{{ $name }} supplies timber (mbao) to builders, carpenters and businesses, and runs a fleet of trucks that moves goods across Tanzania. One company, one team, one system.</p>
            <div class="actions">
                <a class="btn btn-primary" href="#contact">Get a quote</a>
                <a class="btn btn-ghost" href="#business">See what we do</a>
            </div>
        </div>
        <figure class="hero-photo">
            <img src="{{ asset('assets/images/website/truck-2.jpg') }}" alt="Oppah Scania truck loaded with timber">
            <figcaption>Our trucks, our timber, delivered</figcaption>
        </figure>
    </div>
</div>

<section id="business">
    <div class="wrap">
        <div class="section-head">
            <div class="eyebrow">What we do</div>
            <h2>Two businesses, working together</h2>
            <p>Our timber yard and our trucks support each other: we buy and deliver timber with our own fleet, and our trucks also carry cargo for other customers.</p>
        </div>
        <div class="split">
            <div class="biz">
                <div class="icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="5" rx="1"/><rect x="3" y="10" width="18" height="5" rx="1"/><rect x="3" y="16" width="18" height="5" rx="1"/></svg></div>
                <h3>Timber supply (Mbao)</h3>
                <p>Sawn timber in common sizes for construction, roofing, furniture and formwork, sold from our main yard at Mzinga.</p>
                <ul>
                    <li>Many sizes and grades, ready in stock</li>
                    <li>Cash and credit sales with proper invoices</li>
                    <li>Delivery to your site with our trucks</li>
                    <li>Clear customer statements and receipts</li>
                </ul>
            </div>
            <div class="biz trucks">
                <div class="icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 6h13v10H1z"/><path d="M14 9h4l4 4v3h-8z"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="17.5" cy="18.5" r="2"/></svg></div>
                <h3>Trucks &amp; logistics</h3>
                <p>Our own trucks and drivers carry timber and general cargo, with every trip planned, costed and followed.</p>
                <ul>
                    <li>Trucks for hire, short and long distance</li>
                    <li>Trips planned with routes and costs</li>
                    <li>Live trip locations from the driver app</li>
                    <li>Trip ledgers and expense records</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="alt" id="fleet">
    <div class="wrap">
        <div class="section-head">
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
                <button type="button" data-full="{{ asset('assets/images/website/'.$file.'.jpg') }}" aria-label="View larger: {{ $alt }}">
                    <img src="{{ asset('assets/images/website/'.$file.'.jpg') }}" alt="{{ $alt }}" loading="lazy">
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
        <div class="section-head">
            <div class="eyebrow">Our system</div>
            <h2>One system runs the whole company</h2>
            <p>Timber sales, stock, trucks, trips and money are all managed in the same Oppah portal, so the office always has one true picture of the business.</p>
        </div>
        <div class="grid3">
            <div class="feature"><span class="tag t">Timber</span><h4>Stock &amp; stores</h4><p>Stock in every store, transfers between stores, and low-stock alerts.</p></div>
            <div class="feature"><span class="tag t">Timber</span><h4>Quick sales &amp; invoices</h4><p>Fast sales at the counter, printed invoices, and payments recorded against each invoice.</p></div>
            <div class="feature"><span class="tag t">Timber</span><h4>Customer statements</h4><p>Unpaid invoices by customer, statements and payment reminders with a QR code.</p></div>
            <div class="feature"><span class="tag l">Trucks</span><h4>Trips &amp; routes</h4><p>Each trip has a truck, driver, route plan, cargo and income, from start to finish.</p></div>
            <div class="feature"><span class="tag l">Trucks</span><h4>Driver app &amp; map</h4><p>Drivers use the Oppah app on their phone; the office sees each truck on the trips map.</p></div>
            <div class="feature"><span class="tag l">Trucks</span><h4>Trip expenses</h4><p>Fuel, allowances and other trip costs recorded and checked against each trip.</p></div>
            <div class="feature"><span class="tag b">Finance</span><h4>Daily expenses</h4><p>Day-to-day costs recorded and reported by date.</p></div>
            <div class="feature"><span class="tag b">Finance</span><h4>Bank deposits</h4><p>Deposits with bank slips, partial deposits and the amount still to bank.</p></div>
            <div class="feature"><span class="tag b">Finance</span><h4>End of day</h4><p>A daily closing report and timber ledger, so every day is checked and closed.</p></div>
        </div>
    </div>
</section>

<section class="alt">
    <div class="wrap">
        <div class="section-head">
            <div class="eyebrow">How it works</div>
            <h2>From order to delivery</h2>
        </div>
        <div class="steps">
            <div class="step"><h4>Order</h4><p>Call or visit us with the sizes and amount you need.</p></div>
            <div class="step"><h4>Invoice</h4><p>We prepare your invoice from our live stock.</p></div>
            <div class="step"><h4>Load &amp; deliver</h4><p>Our truck carries the timber to your site.</p></div>
            <div class="step"><h4>Statement</h4><p>You get clear receipts and statements for every payment.</p></div>
        </div>
    </div>
</section>

<section id="contact">
    <div class="wrap">
        <div class="section-head">
            <div class="eyebrow">Contact us</div>
            <h2>Need timber or a truck?</h2>
            <p>Talk to our team for prices, stock and transport.</p>
        </div>
        <div class="contact">
            <div><strong>Phone</strong><a href="tel:{{ $phoneIntl }}">{{ $phoneIntl }}</a></div>
            <div><strong>Email</strong><a href="mailto:{{ $email }}">{{ $email }}</a></div>
            <div><strong>Location</strong><span>{{ $address }}</span></div>
        </div>
        <div class="cta"><a class="btn btn-primary" href="tel:{{ $phoneIntl }}">Call us now</a></div>
    </div>
</section>

<footer>
    <div class="wrap">
        <span>&copy; {{ date('Y') }} {{ $name }}</span>
        <a href="{{ route('login') }}" target="_top">Staff login</a>
    </div>
</footer>

<script>
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
</body>
</html>
