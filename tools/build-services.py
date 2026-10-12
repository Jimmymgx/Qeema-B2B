"""
One-off generator for the six service pages (content + template).
Run:  python tools/build-services.py      (from the project root)
Writes site/<slug>.html. After generating, the pages are edited by hand; this
script is NOT part of the website.
"""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
SITE = os.path.join(ROOT, 'site')

# ---------------------------------------------------------------- content
SERVICES = [
    dict(
        slug='website-design-development', name='Website Design &amp; Development', short='Website Design',
        meta='Website design and development by Qeematech: strategy, UX, responsive development, CMS, performance and launch.',
        intro='We design and develop modern, responsive websites that combine strong visual identity with seamless user experiences.',
        tags=['UI Design', 'UX Design', 'Corporate Websites', 'Responsive'],
        strip='A complete website, from idea to launch.',
        list_title='Every detail <strong>Working together.</strong>',
        list_lead='Design and development, connected from the start. Built around what your business needs.',
        rows=[
            ('Strategy &amp; structure', 'We start with your goals, audience and content, then map the pages and journeys that will move visitors toward action.'),
            ('UX &amp; interface design', 'Clear layouts, strong typography and a consistent visual system that reflect your brand on every screen size.'),
            ('Responsive development', 'Clean, fast front-end code that adapts smoothly from large desktops to the smallest phones.'),
            ('Content management', 'An easy-to-use CMS so your team can edit pages, publish news and manage media without a developer.'),
            ('Performance &amp; SEO', 'Optimised images, lean code and technical SEO foundations so the site loads fast and gets found.'),
            ('Accessibility &amp; quality', 'Tested across browsers and devices, with accessible markup, contrast and keyboard support built in.'),
        ],
        approach_title='A Clear Path From <strong>Idea To Live.</strong>',
        steps=[
            ('Discover', 'We align on your goals, audience, content, and the role your website needs to play.'),
            ('Design', 'We turn the strategy into wireframes and a visual system, shaped together through feedback.'),
            ('Develop', 'We build the approved experience, connect your CMS, and test across devices and browsers.'),
            ('Launch &amp; hand over', 'We prepare for go-live and give your team the knowledge to manage what comes next.'),
        ],
        deliver_title='Built To Launch, <strong>Ready To Evolve.</strong>',
        deliver_leads=['More than a finished website. You get the tools, ownership, and confidence to keep moving forward.',
                       'Your exact scope and deliverables are agreed together at the start of the project.'],
        checks=[
            ('Design files &amp; component library', 'Organized Figma files, responsive layouts, and reusable UI.'),
            ('Production-ready website', 'Tested front-end code, CMS setup, and agreed integrations.'),
            ('Launch essentials', 'Technical SEO, analytics setup, and a go-live checklist.'),
            ('Documentation &amp; handover', 'Editing guidance, team training, and a plan for ongoing care.'),
        ],
        art='website',
        cap='One experience. Every screen.',
    ),
    dict(
        slug='mobile-app-development', name='Mobile App Development', short='Mobile Apps',
        meta='iOS and Android app development by Qeematech: product strategy, app UX, native and cross-platform builds, store launch and support.',
        intro='We build high-performance mobile applications designed to deliver smooth, scalable experiences across iOS and Android.',
        tags=['iOS &amp; Android', 'Flutter', 'React Native', 'UI/UX'],
        strip='A complete app, from first sketch to store listing.',
        list_title='Every screen <strong>Built to perform.</strong>',
        list_lead='Product thinking, design and engineering working as one team, from the first flow to the live app.',
        rows=[
            ('Product strategy &amp; scope', 'We define the audience, the core features and a realistic first release so you launch what matters most.'),
            ('App UX &amp; interface design', 'Intuitive flows and polished interfaces that follow iOS and Android conventions and feel natural to use.'),
            ('iOS &amp; Android development', 'Native or cross-platform builds with Flutter or React Native, chosen for your budget, speed and performance needs.'),
            ('Backend, APIs &amp; integrations', 'Secure APIs, databases, push notifications, payments and third-party services connected behind the scenes.'),
            ('Testing &amp; performance', 'Device testing, crash monitoring and performance tuning so the app stays fast and stable as it grows.'),
            ('Store launch &amp; support', 'Store assets, review submission and release management, followed by updates and ongoing technical support.'),
        ],
        approach_title='A Clear Path From <strong>Idea To App Store.</strong>',
        steps=[
            ('Discover', 'We clarify the problem, the users and the must-have features, then agree a scope for the first release.'),
            ('Design', 'We prototype the key flows and shape the interface until it is clear, fast and easy to use.'),
            ('Build', 'We develop the app and its backend in short cycles, so you can review working builds along the way.'),
            ('Launch &amp; grow', 'We handle store submission, release and monitoring, then keep improving based on real usage.'),
        ],
        deliver_title='Built To Ship, <strong>Ready To Scale.</strong>',
        deliver_leads=['More than a working build. You get a product that is ready for real users and ready for the next release.',
                       'Features, platforms and timelines are agreed together at the start of the project.'],
        checks=[
            ('Interactive prototype &amp; design files', 'Clickable flows and organised design assets for every screen.'),
            ('iOS and Android apps', 'Tested builds ready for submission, with source code ownership.'),
            ('Backend &amp; admin tools', 'APIs, database and an admin panel to manage content and users.'),
            ('Store launch &amp; handover', 'Store listings, release notes, documentation and a plan for updates.'),
        ],
        art='mobile',
        cap='Native feel. Every device.',
    ),
    dict(
        slug='e-commerce-solutions', name='E-Commerce Solutions', short='E-Commerce',
        meta='E-commerce design and development by Qeematech: online stores, secure payments, product catalogs, checkout and conversion optimisation.',
        intro='We create conversion-focused online stores that make browsing, purchasing, and managing products simple and effective.',
        tags=['WooCommerce', 'Shopify', 'Checkout', 'Conversion'],
        strip='A complete store, from first product to first order.',
        list_title='Every step <strong>Built to convert.</strong>',
        list_lead='From the shelf to the checkout, every part of the store is designed to help customers buy with confidence.',
        rows=[
            ('Store strategy &amp; structure', 'We plan categories, navigation and the buying journey around how your customers actually shop.'),
            ('Catalog &amp; product experience', 'Clear product pages, filters, search and variants that make choosing quick and easy.'),
            ('Checkout &amp; payments', 'A short, secure checkout with the local and international payment methods your customers expect.'),
            ('Platform development', 'WooCommerce, Shopify or a custom build, selected and configured for your products and growth plans.'),
            ('Speed, SEO &amp; conversion', 'Fast pages, search-friendly structure and conversion tuning to turn more visitors into buyers.'),
            ('Orders, shipping &amp; operations', 'Order management, inventory, shipping rules and reporting that keep daily operations running smoothly.'),
        ],
        approach_title='A Clear Path From <strong>Catalog To Checkout.</strong>',
        steps=[
            ('Plan', 'We review your products, customers and sales channels, then define the store structure and key features.'),
            ('Design', 'We design the storefront, product pages and checkout to feel on-brand and be effortless to use.'),
            ('Build', 'We develop the store, connect payments, shipping and inventory, and load your products.'),
            ('Launch &amp; optimise', 'We go live, track the numbers and keep improving speed, journeys and conversion.'),
        ],
        deliver_title='Built To Sell, <strong>Ready To Grow.</strong>',
        deliver_leads=['More than a store that looks good. You get a sales channel you can manage and expand yourself.',
                       'Platform, payment methods and integrations are agreed together at the start of the project.'],
        checks=[
            ('Store design &amp; theme', 'A branded, responsive storefront and reusable page templates.'),
            ('Payments, shipping &amp; taxes', 'Configured payment gateways, delivery rules and tax settings.'),
            ('Product &amp; data setup', 'Catalog structure, product import and inventory management.'),
            ('Training &amp; handover', 'Admin training, documentation and support after launch.'),
        ],
        art='ecommerce',
        cap='Browse. Choose. Checkout.',
    ),
    dict(
        slug='custom-software', name='Custom Software', short='Custom Software',
        meta='Custom software development by Qeematech: web platforms, dashboards, integrations and APIs built around how your business works.',
        intro='Tailor-made systems and platforms that streamline operations and scale with your business.',
        tags=['Web Platforms', 'Dashboards', 'Integrations', 'APIs'],
        strip='A complete system, built around how you work.',
        list_title='Every process <strong>Working together.</strong>',
        list_lead='We turn complex workflows into clear, reliable software that your team can trust every day.',
        rows=[
            ('Discovery &amp; system analysis', 'We study your workflows, users and existing tools to define exactly what the system must do.'),
            ('Architecture &amp; data modeling', 'A scalable foundation: clear modules, a solid database design and a stack chosen for the long term.'),
            ('Dashboards &amp; admin panels', 'Focused interfaces with live data, reports and role-based views that make daily work faster.'),
            ('Integrations &amp; APIs', 'Connections to your ERP, payments, CRM, messaging and any third-party service your team relies on.'),
            ('Security &amp; access control', 'Roles, permissions, audit trails and secure data handling to protect your business and your customers.'),
            ('Deployment &amp; ongoing support', 'Reliable hosting, monitoring, backups and continued improvements as your needs change.'),
        ],
        approach_title='A Clear Path From <strong>Process To Platform.</strong>',
        steps=[
            ('Analyse', 'We map your processes and requirements, and agree the scope and priorities with your team.'),
            ('Architect', 'We design the system structure, data model and interface so everything fits together from day one.'),
            ('Build', 'We develop in short iterations, with regular demos so the system matches how you really work.'),
            ('Deploy &amp; support', 'We launch, train your users and keep the system secure, fast and up to date.'),
        ],
        deliver_title='Built To Work, <strong>Ready To Scale.</strong>',
        deliver_leads=['More than delivered code. You get a system your team understands, owns and can keep extending.',
                       'Modules, integrations and milestones are agreed together at the start of the project.'],
        checks=[
            ('Working system &amp; source code', 'Tested software deployed to your environment, with full code ownership.'),
            ('Admin &amp; reporting tools', 'Dashboards, user management and exportable reports.'),
            ('Integrations &amp; API documentation', 'Documented connections so other tools can plug in safely.'),
            ('Training &amp; maintenance plan', 'User training, technical documentation and an agreed support plan.'),
        ],
        art='software',
        cap='Complex work. Clear systems.',
    ),
    dict(
        slug='branding-identity', name='Branding &amp; Identity', short='Branding',
        meta='Branding and identity design by Qeematech: logo, visual identity, tone of voice, brand guidelines and marketing collateral.',
        intro='Memorable brand systems, from logo and tone of voice to guidelines that keep every touchpoint consistent.',
        tags=['Logo', 'Brand Guidelines', 'Messaging', 'Collateral'],
        strip='A complete brand, from first idea to every touchpoint.',
        list_title='Every detail <strong>Telling one story.</strong>',
        list_lead='Strategy, design and language working together so your brand is recognised and remembered.',
        rows=[
            ('Brand strategy &amp; positioning', 'We clarify who you are, who you serve and what makes you different, so the identity has a clear purpose.'),
            ('Logo &amp; visual identity', 'A distinctive logo and identity system designed to work across print, digital and every size.'),
            ('Colour, type &amp; graphic language', 'A considered palette, typography and visual elements that make your brand instantly recognisable.'),
            ('Tone of voice &amp; messaging', 'Clear brand messaging and a consistent voice for your website, social media and sales material.'),
            ('Brand guidelines', 'A practical guide that shows your team and partners exactly how to use the brand correctly.'),
            ('Collateral &amp; digital assets', 'Business cards, presentations, social templates and other ready-to-use assets in your new identity.'),
        ],
        approach_title='A Clear Path From <strong>Idea To Identity.</strong>',
        steps=[
            ('Listen', 'We learn about your business, audience and competitors through workshops and research.'),
            ('Define', 'We shape the positioning, personality and message that the identity will express.'),
            ('Design', 'We create and refine the logo and visual system with your feedback at each stage.'),
            ('Roll out', 'We deliver guidelines and assets, and help you apply the brand across your channels.'),
        ],
        deliver_title='Built To Last, <strong>Ready To Use.</strong>',
        deliver_leads=['More than a logo. You get a complete system your team can apply with confidence, everywhere.',
                       'The identity scope and the list of assets are agreed together at the start of the project.'],
        checks=[
            ('Logo suite &amp; identity files', 'Primary and secondary logos in every format you need.'),
            ('Brand guidelines document', 'Colours, typography, usage rules and examples in one place.'),
            ('Messaging &amp; tone of voice', 'Core messages, taglines and writing guidance.'),
            ('Collateral &amp; templates', 'Stationery, presentation and social media templates, ready to use.'),
        ],
        art='branding',
        cap='One brand. Every touchpoint.',
    ),
    dict(
        slug='digital-marketing', name='Digital Marketing', short='Digital Marketing',
        meta='Digital marketing by Qeematech: SEO, social media, paid advertising, content and analytics that bring the right audience to your product.',
        intro='Data-driven campaigns, SEO and content that bring the right audience to your product.',
        tags=['SEO', 'Social Media', 'Paid Ads', 'Analytics'],
        strip='A complete growth plan, from audience to results.',
        list_title='Every channel <strong>Working together.</strong>',
        list_lead='Search, social, ads and content planned as one system, measured against the goals that matter to you.',
        rows=[
            ('Strategy &amp; audience research', 'We study your market, competitors and customers, then set clear goals and the channels that will reach them.'),
            ('Technical &amp; content SEO', 'Site fixes, keyword strategy and useful content that help you rank for what your customers search.'),
            ('Social media management', 'A consistent content calendar, community management and creative that builds a real following.'),
            ('Paid campaigns', 'Search and social advertising set up, tested and optimised to bring qualified leads at a sensible cost.'),
            ('Content &amp; creative', 'Articles, visuals and short videos that explain your value and give people a reason to act.'),
            ('Analytics &amp; reporting', 'Clear dashboards and monthly reports that connect marketing activity to leads and sales.'),
        ],
        approach_title='A Clear Path From <strong>Audience To Results.</strong>',
        steps=[
            ('Audit', 'We review your website, channels and competitors to find the biggest opportunities first.'),
            ('Plan', 'We set goals, budgets and a channel plan, with a content and campaign calendar.'),
            ('Launch', 'We publish content and run campaigns, adjusting quickly based on early results.'),
            ('Measure &amp; improve', 'We report on what works and keep refining to grow results month after month.'),
        ],
        deliver_title='Built To Perform, <strong>Ready To Compound.</strong>',
        deliver_leads=['More than activity. You get clear numbers, learnings and a plan that gets stronger every month.',
                       'Goals, channels and reporting are agreed together at the start of the engagement.'],
        checks=[
            ('Marketing strategy &amp; calendar', 'Goals, audiences, channels and a clear publishing plan.'),
            ('SEO &amp; campaign setup', 'Technical fixes, keyword targets, tracking and ad account structure.'),
            ('Content &amp; creative assets', 'Posts, articles, ad creatives and landing page content.'),
            ('Reports &amp; recommendations', 'Monthly performance reports with the next steps to take.'),
        ],
        art='marketing',
        cap='Reach. Engage. Grow.',
    ),
]

THUMBS = ['results-hand.webp', 'project-screenshot.webp', 'project-screenshot1.webp', 'project-screenshot2.webp', 'results-hand.webp', 'project-screenshot1.webp']

# ---------------------------------------------------------------- hero art (per service)
def laptop(screen_html):
    return ('<div class="sd-laptop">\n            %s\n            <img fetchpriority="high" decoding="async" class="sd-laptop__frame" src="assets/img/laptop-mockup.webp" alt="" width="612" height="612">\n          </div>' % screen_html)

def phone(cls, screen):
    return ('<div class="sd-phone %s">\n            <img decoding="async" class="sd-phone__frame" src="assets/img/phone-mockup.png" alt="" width="216" height="459">\n'
            '            <img decoding="async" class="sd-phone__screen" src="assets/img/%s" alt="" width="194" height="438">\n          </div>' % (cls, screen))

def chip(cls, style, inner):
    return '<div class="sd-chip %s" style="%s">%s</div>' % (cls, style, inner)

ART = {}
ART['website'] = '\n          '.join([
    laptop('<img fetchpriority="high" decoding="async" class="sd-laptop__screen" src="assets/img/laptop-screen.jpg" alt="" width="436" height="271">'),
    phone('', 'phone-screen.jpg'),
])
ART['mobile'] = '\n          '.join([
    phone('sd-phone--a', 'phone-screen.jpg'),
    phone('sd-phone--b', 'project-screenshot1.webp'),
    chip('sd-chip--c', 'left:392px;top:70px', '<b>New order</b><span>#2481 · confirmed</span>'),
    chip('sd-chip--d', 'left:20px;top:300px', '<span class="sd-dot"></span><b>Push</b><span>Your order is on its way</span>'),
])
ART['ecommerce'] = '\n          '.join([
    laptop('<img fetchpriority="high" decoding="async" class="sd-laptop__screen" src="assets/img/project-screenshot.webp" alt="" width="436" height="271">'),
    chip('sd-chip--cart', 'left:372px;top:210px', '<b>Cart · 3 items</b><span class="sd-price">$120.00</span><i class="sd-btn">Checkout</i>'),
    chip('sd-chip--c', 'left:18px;top:70px', '<span class="sd-tick">✓</span><b>Order confirmed</b>'),
])
ART['software'] = '\n          '.join([
    laptop('''<div class="sd-laptop__screen sd-dash" aria-hidden="true">
              <span class="sd-dash__side"></span>
              <span class="sd-dash__kpi sd-dash__kpi--1"></span><span class="sd-dash__kpi sd-dash__kpi--2"></span><span class="sd-dash__kpi sd-dash__kpi--3"></span>
              <span class="sd-dash__bars"><i style="--h:42%"></i><i style="--h:64%"></i><i style="--h:50%"></i><i style="--h:78%"></i><i style="--h:58%"></i><i style="--h:90%"></i><i style="--h:70%"></i><i style="--h:96%"></i></span>
              <svg class="sd-dash__line" viewBox="0 0 200 60" preserveAspectRatio="none"><polyline pathLength="1" points="0,50 28,40 56,44 86,26 116,32 150,14 200,6" /></svg>
            </div>'''),
    chip('sd-chip--c', 'left:380px;top:96px', '<span class="sd-dot"></span><b>99.9%</b><span>uptime</span>'),
    chip('sd-chip--d', 'left:30px;top:300px', '<span class="sd-tick">✓</span><b>API</b><span>connected</span>'),
])
ART['branding'] = '\n          '.join([
    '<div class="sd-tile sd-tile--logo"><img decoding="async" src="assets/svg/logo.svg" alt="" width="150" height="76"></div>',
    '<div class="sd-tile sd-tile--type"><b>Aa</b><span>Roboto</span></div>',
    '<div class="sd-tile sd-tile--card"><img decoding="async" src="assets/svg/logo-white.svg" alt="" width="120" height="61"><span>qeematech.net</span></div>',
    '<div class="sd-swatches"><i style="--c:#00247e"></i><i style="--c:#0c5adb"></i><i style="--c:#00cedc"></i><i style="--c:#1e1e1e"></i></div>',
])
ART['marketing'] = '\n          '.join([
    '''<div class="sd-chart" aria-hidden="true">
            <span class="sd-chart__title">Organic traffic</span>
            <span class="sd-chart__bars"><i style="--h:30%"></i><i style="--h:42%"></i><i style="--h:38%"></i><i style="--h:58%"></i><i style="--h:52%"></i><i style="--h:74%"></i><i style="--h:92%"></i></span>
            <svg class="sd-chart__line" viewBox="0 0 300 120" preserveAspectRatio="none"><polyline pathLength="1" points="0,100 50,88 100,92 150,66 200,72 250,36 300,12" /></svg>
          </div>''',
    chip('sd-chip--c', 'left:330px;top:44px', '<b>+128%</b><span>organic traffic</span>'),
    chip('sd-chip--d', 'left:10px;top:326px', '<b>4.8×</b><span>return on ad spend</span>'),
    chip('sd-chip--e', 'left:372px;top:340px', '<span class="sd-tick">#1</span><b>Ranking</b>'),
])

# ---------------------------------------------------------------- template
def e(s):
    return s

def build(i, s):
    nxt = SERVICES[(i + 1) % len(SERVICES)]
    prv = SERVICES[(i - 1) % len(SERVICES)]
    about = open(os.path.join(SITE, 'about.html'), encoding='utf-8').read()
    head = about[:about.index('<body')]
    title = re.sub('&amp;', '&amp;', s['name'])
    head = head.replace('About Us | Qeematech', '%s | Services | Qeematech' % title)
    head = head.replace('Qeematech is one of the leading entities in the development and design of websites in Egypt and the Arab world.', s['meta'])
    head = head.replace('<link rel="stylesheet" href="css/home.css">\n', '')
    head = head.replace('<link rel="stylesheet" href="css/about.css">', '<link rel="stylesheet" href="css/about.css">\n  <link rel="stylesheet" href="css/services.css">\n  <link rel="stylesheet" href="css/service-art.css">')

    proj = open(os.path.join(SITE, 'project.html'), encoding='utf-8').read()
    clip = re.search(r'<svg width="0" height="0".*?</svg>', proj, re.S).group(0) if s['art'] in ('website', 'mobile') else ''

    tags = ''.join('<span class="tag">%s</span>' % t for t in s['tags'])
    rows = ''
    for k, (t, d) in enumerate(s['rows']):
        rows += '''        <li class="sd-row%s" data-reveal%s>
          <div class="sd-row__text"><h3>%s</h3><p>%s</p></div>
          <span class="sd-row__img" aria-hidden="true"><img decoding="async" src="assets/img/%s" alt="" loading="lazy" width="228" height="120"></span>
          <span class="sd-row__num" aria-hidden="true">%02d</span>
        </li>
''' % (' is-active' if k == 0 else '', '' if k == 0 else ' style="--d:.05s"', t, d, THUMBS[k], k + 1)
    steps = ''.join('''        <li class="why__card" data-reveal%s>
          <span class="why__icon"><img decoding="async" src="assets/img/why-icon.png" alt="" width="36" height="36"></span>
          <h3>%s</h3>
          <p>%s</p>
        </li>
''' % ('' if k == 0 else ' style="--d:.%ds"' % k, t, d) for k, (t, d) in enumerate(s['steps']))
    checks = ''.join('        <li data-reveal%s><h3>%s</h3><p>%s</p></li>\n' % ('' if k == 0 else ' style="--d:.%ds"' % k, t, d) for k, (t, d) in enumerate(s['checks']))
    leads = ''.join('        <p class="lead" data-reveal style="--d:.%ds">%s</p>\n' % (14 + 6 * k, l) for k, l in enumerate(s['deliver_leads']))
    # normalise the lead delays to valid CSS (.14s, .2s)
    leads = leads.replace('--d:.14s', '--d:.14s').replace('--d:.20s', '--d:.2s')

    body = '''<body class="page-service" data-page="services">

  <div data-include="header"></div>

  <main class="inner inner--svc">
    %(clip)s
    <div class="inner-glow" aria-hidden="true">
      <div class="inner-glow__stage">
        <div class="glow-field glow-field--full"></div>
        <div class="glow-drift"></div>
        <span class="inner-glow__vline inner-glow__vline--l"></span>
        <span class="inner-glow__vline inner-glow__vline--r"></span>
      </div>
    </div>

    <!-- =============== Hero =============== -->
    <section class="sd-hero" aria-labelledby="sdTitle">
      <nav class="crumb crumb--3 rise-in" aria-label="Breadcrumb"><ol><li><a href="index.html">Home</a></li><li><a href="services.html">Services</a></li><li aria-current="page">%(name)s</li></ol></nav>
      <div class="sd-hero__row">
        <div class="sd-hero__copy">
          <h1 id="sdTitle" class="rise-in" style="--d:.1s">%(name)s</h1>
          <p class="rise-in" style="--d:.2s">%(intro)s</p>
          <div class="svc-card__tags rise-in" style="--d:.3s">%(tags)s</div>
          <div class="sd-hero__actions rise-in" style="--d:.4s">
            <a class="btn" href="book-a-call.html">Discuss your project</a>
            <a class="btn btn--outline" href="#approach"><span>Explore Our Approach</span></a>
          </div>
        </div>
        <div class="sd-art sd-art--%(art)s rise-in" style="--d:.25s" aria-hidden="true">
          <img decoding="async" class="sd-art__rings" src="assets/svg/orbit-a.svg" alt="">
          <img decoding="async" class="sd-art__rings sd-art__rings--b" src="assets/svg/orbit-b.svg" alt="">
          %(art_html)s
          <span class="sd-art__cap">%(cap)s</span>
        </div>
      </div>
      <div class="svc-strip rise-in" style="--d:.5s">
        <p>%(strip)s</p>
        <p><a href="#included">What’s included</a> &nbsp;/&nbsp; <a href="#approach">Our approach</a> &nbsp;/&nbsp; <a href="#deliverables">Deliverables</a></p>
      </div>
    </section>

    <!-- =============== What is included =============== -->
    <section class="sd-list" id="included" aria-labelledby="sdListTitle">
      <div class="sd-list__head">
        <div>
          <span class="pill pill--grey" data-reveal>Our services</span>
          <h2 class="title" id="sdListTitle" data-reveal style="--d:.08s">%(list_title)s</h2>
        </div>
        <div class="lead-row" data-reveal="right"><p class="lead">%(list_lead)s</p></div>
      </div>
      <ol class="sd-rows" id="sdRows">
%(rows)s      </ol>
    </section>

    <!-- =============== Approach =============== -->
    <section class="sd-approach" id="approach" aria-labelledby="sdApproachTitle">
      <div class="sd-approach__head">
        <span class="pill pill--grey" data-reveal>Our approach</span>
        <h2 class="title" id="sdApproachTitle" data-reveal style="--d:.08s">%(approach_title)s</h2>
      </div>
      <ul class="why__grid">
%(steps)s      </ul>
    </section>

    <!-- =============== Deliverables =============== -->
    <section class="sd-deliver" id="deliverables" aria-labelledby="sdDeliverTitle">
      <div class="sd-deliver__copy">
        <span class="pill pill--grey" data-reveal>What you walk away with</span>
        <h2 class="title" id="sdDeliverTitle" data-reveal style="--d:.08s">%(deliver_title)s</h2>
%(leads)s      </div>
      <ul class="sd-checks">
%(checks)s      </ul>
    </section>

    <!-- =============== Previous / next service =============== -->
    <nav class="sd-next" aria-label="More services">
      <a class="sd-next__link sd-next__link--prev" href="%(prv_slug)s.html"><span>Previous service</span><b>%(prv_name)s</b></a>
      <a class="sd-next__link sd-next__link--next" href="%(nxt_slug)s.html"><span>Next service</span><b>%(nxt_name)s</b></a>
    </nav>
  </main>

  <div data-include="footer"></div>

  <script src="js/components.js"></script>
  <script src="js/main.js"></script>
  <script src="js/service-detail.js"></script>
</body>
</html>
''' % dict(clip=clip, name=s['name'], intro=s['intro'], tags=tags, art=s['art'], art_html=ART[s['art']], cap=s['cap'], strip=s['strip'],
           list_title=s['list_title'], list_lead=s['list_lead'], rows=rows, approach_title=s['approach_title'], steps=steps,
           deliver_title=s['deliver_title'], leads=leads, checks=checks,
           prv_slug=prv['slug'], prv_name=prv['name'], nxt_slug=nxt['slug'], nxt_name=nxt['name'])
    out = os.path.join(SITE, s['slug'] + '.html')
    open(out, 'w', encoding='utf-8').write(head + body)
    print('wrote', out)

if __name__ == '__main__':
    for i, s in enumerate(SERVICES):
        build(i, s)
