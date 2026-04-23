<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Antigravity - The Ultimate Restaurant SaaS</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

        <!-- Bootstrap 5 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        
        <style>
            :root {
                --primary: #0ea5e9;
                --primary-hover: #0284c7;
                --primary-glow: rgba(14, 165, 233, 0.2);
                --bg-color: #05070a;
            }
            body {
                background-color: var(--bg-color);
                color: #ffffff;
                font-family: 'Outfit', sans-serif;
            }
            
            /* Custom Aesthetics */
            .glass {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            .glass-btn {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                color: #fff;
                transition: all 0.3s ease;
            }
            .glass-btn:hover {
                background: rgba(255, 255, 255, 0.1);
                color: #fff;
            }
            .btn-primary-custom {
                background-color: var(--primary);
                border-color: var(--primary);
                color: #fff;
                box-shadow: 0 10px 25px var(--primary-glow);
                transition: all 0.3s ease;
            }
            .btn-primary-custom:hover {
                background-color: var(--primary-hover);
                border-color: var(--primary-hover);
                transform: scale(1.05);
                color: #fff;
            }
            
            .gradient-text {
                background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .card-gradient {
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0) 100%);
                border: 1px solid rgba(255,255,255,0.05);
            }
            .card-gradient:hover {
                border-color: rgba(14, 165, 233, 0.5);
            }
            
            .text-muted-custom { color: #9ca3af; }
            .bg-dark-custom { background-color: rgba(255,255,255,0.05); }
            
            @keyframes float { 
                0% { transform: translateY(0px); } 
                50% { transform: translateY(-20px); } 
                100% { transform: translateY(0px); } 
            }
            .animate-float { animation: float 6s ease-in-out infinite; }
            
            .pulse-dot {
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: .5; }
            }
            
            .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
            .reveal.active { opacity: 1; transform: translateY(0); }
            
            .hero-blur {
                position: absolute;
                top: 0; right: 0;
                width: 800px; height: 800px;
                background: rgba(14, 165, 233, 0.1);
                filter: blur(120px);
                border-radius: 50%;
                transform: translate(50%, -50%);
                z-index: 0;
            }
            
            .nav-glass {
                margin: 1.5rem auto;
                max-width: 1200px;
                border-radius: 2rem;
            }
            
            a { text-decoration: none; }
        </style>
    </head>
    <body class="position-relative">
        <!-- Navigation -->
        <nav class="navbar fixed-top w-100 p-0">
            <div class="container nav-glass glass px-4 py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded d-flex align-items-center justify-content-center shadow-lg" style="width:40px; height:40px; background-color: var(--primary);">
                        <svg viewBox="0 0 24 24" fill="none" width="24" height="24" stroke="currentColor" stroke-width="2.5">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" />
                            <path d="M2 17L12 22L22 17" />
                            <path d="M2 12L12 17L22 12" />
                        </svg>
                    </div>
                    <span class="fs-4 fw-black text-uppercase tracking-widest text-white m-0" style="font-weight: 900; letter-spacing: 2px;">Antigravity</span>
                </div>
                
                <div class="d-none d-md-flex align-items-center gap-4">
                    <div class="d-flex bg-dark-custom p-1 rounded border border-white-50">
                        <a href="/?locale=en" class="px-3 py-1 rounded {{ $appLocale === 'en' ? 'bg-white text-dark' : 'text-muted-custom' }} fw-bold" style="font-size: 10px; letter-spacing: 2px;">EN</a>
                        <a href="/?locale=bn" class="px-3 py-1 rounded {{ $appLocale === 'bn' ? 'bg-white text-dark' : 'text-muted-custom' }} fw-bold" style="font-size: 10px; letter-spacing: 2px;">বাংলা</a>
                    </div>
                    <a href="#features" class="text-muted-custom fw-bold">Features</a>
                    <a href="#solutions" class="text-muted-custom fw-bold">Solutions</a>
                    <a href="#pricing" class="text-muted-custom fw-bold">Pricing</a>
                    <a href="/super-admin/login" class="btn btn-light rounded-pill px-4 py-2 fw-bold shadow-sm">Launch Dashboard</a>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="position-relative overflow-hidden" style="padding-top: 15rem; padding-bottom: 8rem;">
            <div class="hero-blur"></div>
            <div class="container text-center position-relative z-1">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-4" style="background: rgba(14,165,233,0.1); border: 1px solid rgba(14,165,233,0.2);">
                    <span class="rounded-circle pulse-dot" style="width: 8px; height: 8px; background-color: var(--primary);"></span>
                    <span class="fw-black text-uppercase" style="font-size: 12px; letter-spacing: 2px; color: var(--primary);">Final Core 2026 Update Live</span>
                </div>
                <h1 class="display-1 fw-black tracking-tighter mb-4" style="font-weight: 900;">
                    {!! __('landing.hero_title') !!}
                </h1>
                <p class="fs-4 text-muted-custom mx-auto mb-5 fw-medium" style="max-width: 800px;">
                    {{ __('landing.hero_subtitle') }}
                </p>
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-3">
                    <a href="/super-admin/login" class="btn btn-primary-custom rounded-pill px-5 py-3 fs-5 fw-bold d-flex align-items-center gap-2">
                        {{ __('landing.start_free_trial') }}
                    </a>
                    <a href="http://bdt-restaurant.localhost:8000/guest-order/1" target="_blank" class="btn glass-btn rounded-pill px-5 py-3 fs-5 fw-bold d-flex align-items-center gap-2" style="color: var(--primary)">
                        Trial Live Demo
                    </a>
                    <a href="#features" class="btn glass-btn rounded-pill px-5 py-3 fs-5 fw-bold text-white">
                        {{ __('landing.view_ecosystem') }}
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Grid -->
        <section id="features" class="container py-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-end mb-5 gap-4">
                <div style="max-width: 700px;">
                    <h2 class="display-4 fw-black text-uppercase mb-3" style="font-weight: 900; letter-spacing: 2px;">{!! __('landing.built_for_scale') !!}</h2>
                    <p class="fs-5 text-muted-custom m-0">{{ __('landing.built_for_scale_desc') }}</p>
                </div>
                <div class="d-flex gap-4">
                     <div class="text-center">
                        <p class="fs-2 fw-black m-0" style="font-weight: 900;">99.9%</p>
                        <p class="text-muted-custom fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Uptime</p>
                     </div>
                     <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
                     <div class="text-center">
                        <p class="fs-2 fw-black m-0" style="font-weight: 900;">25ms</p>
                        <p class="text-muted-custom fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Latency</p>
                     </div>
                </div>
            </div>

            <div class="row g-4 reveal">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="card card-gradient h-100 rounded-4 p-4 text-white" style="background-color: transparent;">
                        <div class="card-body">
                            <h3 class="fs-4 fw-black text-uppercase mb-3" style="font-weight: 900;">{{ __('landing.feature_multi_tenant') }}</h3>
                            <p class="text-muted-custom m-0">{{ __('landing.feature_multi_tenant_desc') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="card card-gradient h-100 rounded-4 p-4 text-white" style="background-color: transparent;">
                        <div class="card-body">
                            <h3 class="fs-4 fw-black text-uppercase mb-3" style="font-weight: 900;">{{ __('landing.feature_qr_order') }}</h3>
                            <p class="text-muted-custom m-0">{{ __('landing.feature_qr_order_desc') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="card card-gradient h-100 rounded-4 p-4 text-white" style="background-color: transparent;">
                        <div class="card-body">
                            <h3 class="fs-4 fw-black text-uppercase mb-3" style="font-weight: 900;">{{ __('landing.feature_kitchen') }}</h3>
                            <p class="text-muted-custom m-0">{{ __('landing.feature_kitchen_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="container py-5 mt-5" style="background: rgba(255,255,255,0.02); padding: 5rem 2rem; border-radius: 3rem;">
            <div class="text-center mb-5">
                 <h2 class="display-3 fw-black text-uppercase mb-3" style="font-weight: 900;">{{ __('landing.pricing_title') }}</h2>
                 <p class="fs-5 text-muted-custom">{{ __('landing.pricing_subtitle') }}</p>
            </div>
            
            <div class="row g-4 reveal align-items-center">
                 <!-- Starter -->
                 <div class="col-md-4">
                    <div class="card glass text-white text-center p-5 border-0" style="border-radius: 2rem;">
                        <div class="card-body">
                            <p class="fw-bold text-muted-custom text-uppercase mb-3" style="letter-spacing: 3px; font-size: 12px;">{{ __('landing.plan_discovery') ?? 'Discovery' }}</p>
                            <h3 class="display-4 fw-black fst-italic mb-4" style="font-weight: 900;">{{ __('landing.free') ?? 'Free' }}</h3>
                            <ul class="list-unstyled text-muted-custom fw-medium mb-5" style="line-height: 2.5;">
                                <li>1 Location</li>
                                <li>Standard POS</li>
                                <li>QR Core Menus</li>
                                <li class="text-decoration-line-through opacity-50">Atomic Inventory</li>
                            </ul>
                            <a href="/super-admin/login" class="btn glass-btn rounded-pill w-100 py-3 fw-bold">Get Started</a>
                        </div>
                    </div>
                 </div>

                 <!-- Pro -->
                 <div class="col-md-4">
                    <div class="card text-white text-center p-5 position-relative shadow-lg border-0" style="border-radius: 2rem; background-color: var(--primary); z-index: 10;">
                        <span class="position-absolute top-0 end-0 mt-4 me-4 bg-white text-primary rounded-pill px-3 py-1 fw-bold text-uppercase" style="font-size: 10px; letter-spacing: 1px;">Popular</span>
                        
                        <div class="card-body">
                            <p class="fw-bold text-white-50 text-uppercase mb-3" style="letter-spacing: 3px; font-size: 12px;">Professional</p>
                            <h3 class="display-4 fw-black fst-italic mb-4 text-white" style="font-weight: 900;">$49<span class="fs-4">/mo</span></h3>
                            <ul class="list-unstyled fw-medium mb-5 text-white" style="line-height: 2.5;">
                                <li>Unlimited Products</li>
                                <li>Kitchen Display Sys</li>
                                <li>Atomic Inventory</li>
                                <li>Global Analytics</li>
                            </ul>
                            <a href="/super-admin/login" class="btn btn-light rounded-pill w-100 py-3 fw-bold text-dark">Start 14-Day Free Trial</a>
                        </div>
                    </div>
                 </div>

                 <!-- Enterprise -->
                 <div class="col-md-4">
                    <div class="card glass text-white text-center p-5 border-0" style="border-radius: 2rem;">
                        <div class="card-body">
                            <p class="fw-bold text-muted-custom text-uppercase mb-3" style="letter-spacing: 3px; font-size: 12px;">Enterprise</p>
                            <h3 class="display-4 fw-black fst-italic mb-4" style="font-weight: 900;">$199<span class="fs-4">/mo</span></h3>
                            <ul class="list-unstyled text-muted-custom fw-medium mb-5" style="line-height: 2.5;">
                                <li>Multi-Outlet Logic</li>
                                <li>Custom Domains</li>
                                <li>24/7 Concierge</li>
                                <li>API Access</li>
                            </ul>
                            <a href="/super-admin/login" class="btn glass-btn rounded-pill w-100 py-3 fw-bold">Contact Sales</a>
                        </div>
                    </div>
                 </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="mt-5 py-5 text-center" style="border-top: 1px solid rgba(255,255,255,0.05);">
            <div class="container">
                <!-- Ecosystem Live Ticker -->
                <div class="d-inline-flex flex-column flex-md-row align-items-md-center gap-4 glass px-5 py-4 mb-5 reveal" style="border-radius: 2rem;">
                    <div class="text-md-start">
                        <p class="text-muted-custom fw-bold text-uppercase mb-1" style="font-size: 10px; letter-spacing: 2px;">{{ __('landing.ecosystem_scale') }}</p>
                        <p class="fs-3 fw-black m-0" style="font-weight: 900;">{{ $tenantCount }} {{ __('landing.active_tenants') }}</p>
                    </div>
                    <div class="d-none d-md-block" style="width: 1px; height: 40px; background: rgba(255,255,255,0.1);"></div>
                    <div class="text-md-start">
                        <p class="text-muted-custom fw-bold text-uppercase mb-1" style="font-size: 10px; letter-spacing: 2px;">{{ __('landing.global_gtv') }}</p>
                        <p class="fs-3 fw-black m-0" style="color: var(--primary); font-weight: 900;">৳{{ number_format($ecosystemGtv, 0) }}+</p>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-center gap-2 mb-4 opacity-50">
                    <span class="fs-5 fw-black text-uppercase text-white-50" style="letter-spacing: 2px; font-weight: 900;">Antigravity</span>
                </div>
                <p class="text-muted-custom m-0">&copy; 2026 Antigravity SaaS Engine. Built for the future of Dining.</p>
            </div>
        </footer>

        <script>
            // Scroll Reveal Logic
            const observerOptions = { threshold: 0.1 };
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));
        </script>
    </body>
</html>
