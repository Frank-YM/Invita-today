<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invita · Invitaciones digitales para cualquier evento</title>
    <link rel="icon" type="image/png" href="{{ asset('images/themes/icono.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        *{ box-sizing:border-box; margin:0; padding:0; }
        html,body{ height:100%; }
        body{
            font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color:#0f172a; background:#fafaf7;
            -webkit-font-smoothing:antialiased;
            overflow-x:hidden;
        }

        /* ========= LAYOUT ========= */
        .page{
            min-height:100vh; display:flex; flex-direction:column;
            background-image:
              radial-gradient(circle at 12% 8%, rgba(232,139,58,0.06), transparent 40%),
              radial-gradient(circle at 88% 92%, rgba(159,184,199,0.08), transparent 40%);
        }
        .container{ width:100%; max-width:1140px; margin:0 auto; padding:0 28px; }

        /* Header */
        header{
            padding:20px 0; border-bottom:1px solid rgba(15,23,42,0.06);
            background:rgba(250,250,247,0.75); backdrop-filter:blur(6px);
            position:sticky; top:0; z-index:10;
        }
        header .container{ display:flex; align-items:center; justify-content:space-between; }
        .brand{ display:flex; align-items:center; gap:10px; }
        .brand-mark{
            width:36px; height:36px; border-radius:9px;
            background:#fff;
            display:flex; align-items:center; justify-content:center;
            overflow:hidden;
            box-shadow:0 4px 14px rgba(15,23,42,0.08);
        }
        .brand-mark img{ width:100%; height:100%; object-fit:contain; }
        .brand-name{
            font-family:'Playfair Display', serif; font-weight:700;
            font-size:1.2rem; letter-spacing:-0.5px; color:#0f172a;
        }
        .header-links{ display:flex; gap:22px; align-items:center; }
        .header-links a{
            font-size:0.85rem; color:#475569; text-decoration:none; font-weight:500;
            transition:color .15s;
        }
        .header-links a:hover{ color:#0f172a; }

        /* ========= HERO (minimalista) ========= */
        .hero{
            padding-top:56px; padding-bottom:44px;
            display:grid; grid-template-columns:1fr 400px; gap:56px;
            align-items:center;
        }
        .eyebrow{
            font-size:0.72rem; letter-spacing:2px; text-transform:uppercase;
            color:#e88b3a; font-weight:600; margin-bottom:14px;
            display:inline-flex; align-items:center; gap:8px;
        }
        .eyebrow::before{ content:""; width:20px; height:1.5px; background:#e88b3a; }
        h1{
            font-family:'Playfair Display', serif;
            font-size:2.8rem; font-weight:700; letter-spacing:-1.5px;
            line-height:1.05; color:#0f172a; margin-bottom:18px;
        }
        h1 .highlight{
            background:linear-gradient(135deg, #e88b3a 0%, #d4a3b3 100%);
            -webkit-background-clip:text; background-clip:text;
            -webkit-text-fill-color:transparent;
            font-style:italic;
        }
        .lead{
            font-size:1.02rem; color:#475569; line-height:1.6;
            max-width:520px; margin-bottom:26px;
        }

        .event-chips{ display:flex; flex-wrap:wrap; gap:8px; margin-bottom:30px; }
        .event-chip{
            font-size:0.82rem; color:#334155;
            background:#fff; border:1px solid rgba(15,23,42,0.08);
            padding:7px 13px; border-radius:999px; font-weight:500;
        }

        .stats{ display:flex; gap:36px; }
        .stat-num{
            font-family:'Playfair Display', serif; font-weight:700;
            font-size:1.6rem; color:#0f172a; line-height:1;
        }
        .stat-lbl{ font-size:0.72rem; color:#64748b; margin-top:4px; letter-spacing:0.4px; }

        /* ========= LOGIN CARD (minimalista) ========= */
        .card{
            background:#fff; border-radius:20px;
            padding:30px 28px;
            border:1px solid rgba(15,23,42,0.06);
            box-shadow:
              0 1px 2px rgba(15,23,42,0.04),
              0 20px 50px rgba(15,23,42,0.08);
        }
        .card h3{
            font-family:'Playfair Display', serif;
            font-size:1.3rem; font-weight:700; letter-spacing:-0.3px;
            color:#0f172a; margin-bottom:6px;
        }
        .card p.sub{
            font-size:0.86rem; color:#64748b; margin-bottom:20px; line-height:1.5;
        }
        .btn-google{
            display:inline-flex; align-items:center; justify-content:center; gap:10px;
            width:100%; padding:13px 18px;
            background:#0f172a; color:#fff;
            border:none; border-radius:12px;
            font-family:inherit; font-size:0.95rem; font-weight:600;
            text-decoration:none; cursor:pointer;
            transition:transform .15s ease, box-shadow .15s ease, background .15s ease;
            box-shadow:0 4px 14px rgba(15,23,42,0.15);
        }
        .btn-google:hover{ transform:translateY(-1px); box-shadow:0 8px 20px rgba(15,23,42,0.22); background:#1e293b; }
        .btn-google .g-badge{
            width:22px; height:22px; border-radius:6px;
            background:#fff; display:flex; align-items:center; justify-content:center;
        }
        .btn-google svg{ width:14px; height:14px; }

        .fine-print{
            margin-top:14px; text-align:center;
            font-size:0.7rem; color:#94a3b8; line-height:1.5;
        }
        .alert-error{
            background:#fef2f2; color:#991b1b;
            padding:10px 12px; border-radius:10px;
            border:1px solid #fecaca;
            margin-bottom:16px; font-size:0.85rem;
        }

        /* ========= SHOWCASE (mockups) ========= */
        .showcase{
            padding:50px 0 30px;
            border-top:1px solid rgba(15,23,42,0.06);
            background:linear-gradient(180deg, transparent, rgba(255,244,225,0.35), transparent);
        }
        .mockup-row{
            display:flex; justify-content:center; gap:22px;
            max-width:440px; margin:0 auto;
        }
        .phone{
            position:relative; width:100%; max-width:170px; aspect-ratio:9/18;
            background:#111;
            border-radius:24px;
            padding:6px;
            box-shadow:
              0 18px 36px rgba(15,23,42,0.16),
              0 8px 14px rgba(15,23,42,0.08),
              inset 0 0 0 1.5px rgba(255,255,255,0.05);
            transform:rotate(-2deg);
            transition:transform .3s cubic-bezier(.2,.8,.2,1);
        }
        .phone:nth-child(2){ transform:rotate(2deg); }
        .phone:hover{ transform:rotate(0) translateY(-6px); }
        .phone::before{
            content:""; position:absolute; top:8px; left:50%; transform:translateX(-50%);
            width:46px; height:11px; background:#000; border-radius:8px; z-index:5;
        }
        .phone-screen{
            width:100%; height:100%;
            border-radius:18px; overflow:hidden;
            position:relative;
            display:flex; flex-direction:column;
            padding:22px 10px 10px;
            text-align:center;
        }

        .phone-screen.mock-baby{
            background:#fcf3f5;
            background-image:
              radial-gradient(circle at 20% 10%, rgba(212,163,179,0.25), transparent 40%),
              radial-gradient(circle at 80% 90%, rgba(159,184,199,0.2), transparent 40%);
        }
        .mock-baby .m-eyebrow{
            font-family:'Playfair Display', serif; letter-spacing:6px;
            font-size:0.55rem; color:#6e5a63; text-transform:uppercase; margin-top:6px;
        }
        .mock-baby .m-title{
            font-family:'Playfair Display', serif; font-style:italic;
            font-size:1rem; color:#d4a3b3; margin:2px 0; line-height:1.05;
        }
        .mock-baby .m-char{
            width:42px; height:42px; margin:4px auto; border-radius:50%;
            background:#fff; border:3px solid #d4a3b3;
            display:flex; align-items:center; justify-content:center;
            font-size:2rem; box-shadow:0 6px 14px rgba(212,163,179,0.35);
            animation:mock-float 3s ease-in-out infinite;
        }
        .mock-baby .m-count{
            display:flex; justify-content:center; gap:6px; margin-top:8px;
        }
        .mock-baby .m-count > div{
            background:#6e5a63; color:#fff; border-radius:6px;
            padding:4px 6px; min-width:32px; font-size:0.55rem; font-weight:600;
        }
        .mock-baby .m-count > div big{ display:block; font-size:0.85rem; font-weight:700; }
        .mock-baby .m-btn{
            margin-top:14px; background:#d4a3b3; color:#fff;
            padding:6px 14px; border-radius:999px; font-size:0.6rem; font-weight:600;
            display:inline-block;
        }

        .phone-screen.mock-dragon{
            background:#fefaf5;
            background-image:
              radial-gradient(circle at 15% 12%, rgba(255,215,110,0.18), transparent 45%),
              radial-gradient(circle at 85% 88%, rgba(255,215,110,0.12), transparent 45%);
        }
        .mock-dragon .m-eyebrow{
            font-family:'Playfair Display', serif; letter-spacing:5px;
            font-size:0.55rem; color:#3a2b4d; text-transform:uppercase;
            font-weight:700; margin-top:4px;
        }
        .mock-dragon .m-title{
            font-family:'Great Vibes', 'Playfair Display', cursive;
            font-size:1.2rem; color:#e88b3a; margin:4px 0 2px; line-height:1;
        }
        .mock-dragon .m-pair{
            display:flex; justify-content:center; gap:5px; margin:4px auto;
        }
        .mock-dragon .m-pair span{
            width:28px; height:28px; border-radius:50%;
            background:#fff; border:2px solid #fff;
            display:flex; align-items:center; justify-content:center;
            font-size:1.4rem; box-shadow:0 4px 10px rgba(232,139,58,0.25), 0 0 0 2px rgba(232,139,58,0.15);
            animation:mock-float 3s ease-in-out infinite;
        }
        .mock-dragon .m-pair span:nth-child(2){ animation-delay:-1.5s; }
        .mock-dragon .m-dots{
            margin:8px auto 4px; display:flex; gap:5px; justify-content:center;
        }
        .mock-dragon .m-dots i{
            width:6px; height:6px; border-radius:50%; background:#e88b3a;
            box-shadow:0 0 6px rgba(232,139,58,0.5);
        }
        .mock-dragon .m-btn{
            margin-top:14px;
            background:linear-gradient(135deg, #f5b06d, #e88b3a);
            color:#fff; padding:7px 16px; border-radius:999px;
            font-size:0.62rem; font-weight:700; letter-spacing:0.5px;
            display:inline-block; box-shadow:0 4px 12px rgba(232,139,58,0.4);
        }

        @keyframes mock-float{
            0%,100%{ transform:translateY(0); }
            50%    { transform:translateY(-5px); }
        }

        /* ========= FEATURES ========= */
        .features-section{
            padding:60px 0 30px;
            border-top:1px solid rgba(15,23,42,0.06);
        }
        .section-eyebrow{
            font-size:0.72rem; letter-spacing:2px; text-transform:uppercase;
            color:#e88b3a; font-weight:600; text-align:center; margin-bottom:12px;
        }
        .section-title{
            font-family:'Playfair Display', serif;
            font-size:2rem; font-weight:700; letter-spacing:-0.5px;
            text-align:center; color:#0f172a; margin-bottom:8px;
        }
        .section-sub{
            font-size:0.95rem; color:#64748b; text-align:center;
            margin:0 auto 42px; max-width:560px;
        }
        .features-grid{
            display:grid; grid-template-columns:repeat(3, 1fr); gap:24px;
        }
        .feature{
            padding:26px; background:#fff;
            border:1px solid rgba(15,23,42,0.06); border-radius:16px;
            transition:transform .15s, box-shadow .15s;
        }
        .feature:hover{ transform:translateY(-3px); box-shadow:0 10px 30px rgba(15,23,42,0.06); }
        .feature-icon{
            width:40px; height:40px; border-radius:10px;
            display:flex; align-items:center; justify-content:center;
            margin-bottom:14px;
            background:#f8f4ec;
            color:#e88b3a;
        }
        .feature-icon svg{ width:20px; height:20px; }
        .feature h4{
            font-family:'Playfair Display', serif;
            font-size:1.05rem; font-weight:700; letter-spacing:-0.3px;
            color:#0f172a; margin-bottom:6px;
        }
        .feature p{
            font-size:0.85rem; color:#64748b; line-height:1.55;
        }

        /* ========= HOW ========= */
        .how-section{ padding:50px 0 70px; }
        .how-grid{
            display:grid; grid-template-columns:repeat(3, 1fr); gap:24px;
        }
        .step{ padding:24px; text-align:left; position:relative; }
        .step-num{
            font-family:'Playfair Display', serif;
            font-weight:700; font-size:2.4rem; line-height:1;
            color:#e88b3a; opacity:0.35; margin-bottom:10px;
        }
        .step h4{
            font-family:'Playfair Display', serif;
            font-size:1.05rem; font-weight:700; color:#0f172a; margin-bottom:6px;
        }
        .step p{ font-size:0.85rem; color:#64748b; line-height:1.55; }

        /* ========= FOOTER ========= */
        footer{
            padding:26px 0; border-top:1px solid rgba(15,23,42,0.06);
            font-size:0.8rem; color:#94a3b8;
        }
        footer .container{ display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; }

        /* ========= RESPONSIVE ========= */
        @media (max-width:960px){
            .hero{ grid-template-columns:1fr; gap:28px; padding-top:36px; padding-bottom:12px; }
            h1{ font-size:2rem; }
            .features-grid, .how-grid{ grid-template-columns:1fr; gap:10px; }
            .features-section{ padding:36px 0 12px; }
            .how-section{ padding:24px 0 40px; }
            .showcase{ padding:36px 0 20px; }
            .stats{ gap:20px; }
            .stat-num{ font-size:1.3rem; }
            .mockup-row{ gap:16px; max-width:400px; }
        }
        @media (max-width:560px){
            .container{ padding-left:28px !important; padding-right:28px !important; }
            header{ padding:14px 0; }
            .brand-mark{ width:28px; height:28px; font-size:0.9rem; border-radius:8px; }
            .brand-name{ font-size:1rem; }
            .header-links{ gap:14px; }
            .header-links a{ font-size:0.75rem; }

            .hero{ padding-top:34px; padding-bottom:26px; gap:26px; }
            .eyebrow{ font-size:0.65rem; letter-spacing:1.5px; margin-bottom:14px; }
            h1{ font-size:1.6rem; letter-spacing:-0.8px; margin-bottom:16px; line-height:1.15; }
            .lead{ font-size:0.92rem; line-height:1.65; margin-bottom:24px; }
            .event-chip{ font-size:0.75rem; padding:8px 12px; }
            .event-chips{ gap:8px; margin-bottom:28px; }
            .stats{ gap:24px; margin-top:8px; }
            .stat-num{ font-size:1.25rem; }
            .stat-lbl{ font-size:0.7rem; margin-top:6px; }

            .card{ padding:22px 18px; border-radius:14px; }
            .card h3{ font-size:1.1rem; }
            .card p.sub{ font-size:0.8rem; margin-bottom:16px; }
            .btn-google{ padding:11px 14px; font-size:0.88rem; }

            .section-eyebrow{ font-size:0.65rem; letter-spacing:1.5px; margin-bottom:8px; }
            .section-title{ font-size:1.3rem; letter-spacing:-0.3px; }
            .section-sub{ font-size:0.82rem; margin-bottom:24px; }

            .features-section{ padding:28px 0 8px; }
            .how-section{ padding:20px 0 32px; }
            .feature{ padding:16px; border-radius:12px; }
            .feature-icon{ width:32px; height:32px; margin-bottom:10px; border-radius:8px; }
            .feature-icon svg{ width:16px; height:16px; }
            .feature h4{ font-size:0.95rem; }
            .feature p{ font-size:0.78rem; line-height:1.5; }

            .step{ padding:14px 0; }
            .step-num{ font-size:1.8rem; margin-bottom:6px; }
            .step h4{ font-size:0.95rem; }
            .step p{ font-size:0.78rem; }

            .mockup-row{ gap:12px; max-width:320px; }
            .phone{ border-radius:22px; padding:5px; }
            .phone-screen{ border-radius:16px; padding:20px 8px 10px; }
            .phone::before{ width:40px; height:10px; top:6px; }
            .mock-baby .m-title{ font-size:0.9rem; }
            .mock-dragon .m-title{ font-size:1.1rem; }
            .mock-baby .m-char{ width:40px; height:40px; font-size:1.2rem; }
            .mock-dragon .m-pair span{ width:26px; height:26px; font-size:0.95rem; }
            .m-btn{ font-size:0.5rem !important; padding:4px 10px !important; }
            .mock-baby .m-count > div{ padding:3px 5px; min-width:26px; font-size:0.5rem; }
            .mock-baby .m-count > div big{ font-size:0.72rem; }

            footer{ padding:20px 0; font-size:0.72rem; }
            footer .container{ flex-direction:column; text-align:center; gap:6px; }
        }
        @media (max-width:380px){
            .container{ padding-left:22px !important; padding-right:22px !important; }
            h1{ font-size:1.4rem; }
            .section-title{ font-size:1.2rem; }
            .card{ padding:18px 14px; }
        }
    </style>
</head>
<body>
    <div class="page">

        <header>
            <div class="container">
                <div class="brand">
                    <div class="brand-mark"><img src="{{ asset('images/themes/icono.png') }}" alt="Invita"></div>
                    <div class="brand-name">Invita</div>
                </div>
                <div class="header-links">
                    <a href="#features">Características</a>
                    <a href="#how">Cómo funciona</a>
                    <a href="{{ route('auth.google') }}">Ingresar</a>
                </div>
            </div>
        </header>

        {{-- HERO minimalizado (solo lo pedido) --}}
        <main class="container hero">
            <div>
                <span class="eyebrow">Invitaciones digitales</span>
                <h1>Creá tu invitación para <span class="highlight">cualquier evento</span>, en minutos.</h1>
                <p class="lead">Baby shower, cumpleaños, bautizo, revelación de género, aniversario… Elegí un modelo, personalizalo con fotos e imágenes, compartí el enlace y mirá quién confirma en tiempo real.</p>
            </div>

            <div class="card">
                <h3>Empezá gratis</h3>
                <p class="sub">Ingresá con Google y creá tu primera invitación en menos de un minuto.</p>

                @if(session('error'))
                    <div class="alert-error">{{ session('error') }}</div>
                @endif

                <a href="{{ route('auth.google') }}" class="btn-google">
                    <span class="g-badge">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                        </svg>
                    </span>
                    Continuar con Google
                </a>

                <div class="fine-print">
                    Al continuar aceptás nuestros términos.
                </div>
            </div>
        </main>

        {{-- SHOWCASE (mockups de invitaciones) --}}
        <section class="showcase">
            <div class="container">
                <div class="section-eyebrow">Así se ven</div>
                <h2 class="section-title">Diseños listos para enamorar</h2>
                <p class="section-sub">Cada modelo tiene su propia personalidad. Aquí tenés dos ejemplos: uno tierno para baby shower y otro tipo revelación temática.</p>

                <div class="mockup-row">
                    <div class="phone">
                        <div class="phone-screen mock-baby">
                            <div class="m-eyebrow">Invitación</div>
                            <div class="m-title">Baby Shower</div>
                            <div class="m-char">🐘</div>
                            <div class="m-count">
                                <div><big>15</big>días</div>
                                <div><big>04</big>hrs</div>
                                <div><big>22</big>min</div>
                            </div>
                            <div class="m-btn">Confirmar asistencia</div>
                        </div>
                    </div>
                    <div class="phone">
                        <div class="phone-screen mock-dragon">
                            <div class="m-eyebrow">— Revelación —</div>
                            <div class="m-title">Nombre &amp; Nombre</div>
                            <div class="m-pair">
                                <span>👶</span>
                                <span>👶</span>
                            </div>
                            <div class="m-dots"><i></i><i></i><i></i><i></i></div>
                            <div class="m-btn">Confirmar asistencia</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- FEATURES --}}
        <section class="features-section" id="features">
            <div class="container">
                <div class="section-eyebrow">Todo lo que necesitás</div>
                <h2 class="section-title">Diseñado para verse hermoso</h2>
                <p class="section-sub">Modelos animados listos para usar, con imágenes, música, mapa y confirmación de asistencia integrada.</p>

                <div class="features-grid">
                    <div class="feature">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"/>
                                <circle cx="8" cy="10" r="1.2" fill="currentColor" stroke="none"/>
                                <circle cx="16" cy="10" r="1.2" fill="currentColor" stroke="none"/>
                                <circle cx="12" cy="16" r="1.2" fill="currentColor" stroke="none"/>
                                <path d="M18 18l2 2"/>
                            </svg>
                        </div>
                        <h4>10 modelos únicos</h4>
                        <p>Elegí un estilo (polaroid, ticket, dragon, sticker, elegante…) y aplicá al instante colores, formas y tipografías.</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 8h3l1.5-2h7L17 8h3a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/>
                                <circle cx="12" cy="13" r="3.5"/>
                            </svg>
                        </div>
                        <h4>Fotos e imágenes</h4>
                        <p>Subí tus propias fotos o buscá imágenes en la web para armar el estilo perfecto de tu invitación.</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 21s-7-6.5-7-12a7 7 0 0 1 14 0c0 5.5-7 12-7 12z"/>
                                <circle cx="12" cy="9" r="2.5"/>
                            </svg>
                        </div>
                        <h4>Mapa interactivo</h4>
                        <p>Ubicación del evento con mapa embebido, dirección legible y botón para abrir en Google Maps.</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M8 12.5l3 3 5-6"/>
                            </svg>
                        </div>
                        <h4>RSVP integrado</h4>
                        <p>Tus invitados confirman con un click. Vos ves quién viene, con cuántos acompañantes y sus mensajes.</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 14a4 4 0 0 0 5.66 0l3.34-3.34a4 4 0 0 0-5.66-5.66L12 6.34"/>
                                <path d="M14 10a4 4 0 0 0-5.66 0L5 13.34a4 4 0 0 0 5.66 5.66L12 17.66"/>
                            </svg>
                        </div>
                        <h4>Enlace único</h4>
                        <p>Cada invitación tiene su propia URL corta. Copiala, mandala por WhatsApp y listo.</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="7" y="3" width="10" height="18" rx="2"/>
                                <path d="M11 18h2"/>
                            </svg>
                        </div>
                        <h4>Perfecto en el cel</h4>
                        <p>Todas las plantillas están pensadas para que se vean impecables en el celular de tus invitados.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- HOW --}}
        <section class="how-section" id="how">
            <div class="container">
                <div class="section-eyebrow">Cómo funciona</div>
                <h2 class="section-title">De cero a compartido en 3 pasos</h2>
                <p class="section-sub">Sin instalar apps, sin registros complicados, sin plantillas rotas.</p>

                <div class="how-grid">
                    <div class="step">
                        <div class="step-num">01</div>
                        <h4>Ingresá con Google</h4>
                        <p>Un click y quedás dentro. Sin passwords, sin formularios largos.</p>
                    </div>
                    <div class="step">
                        <div class="step-num">02</div>
                        <h4>Personalizá el diseño</h4>
                        <p>Elegí modelo, subí fotos, ajustá fecha y dirección. La vista previa se actualiza al instante.</p>
                    </div>
                    <div class="step">
                        <div class="step-num">03</div>
                        <h4>Compartí el enlace</h4>
                        <p>Publicá la invitación y copiá tu URL. Los invitados confirman desde su celular.</p>
                    </div>
                </div>
            </div>
        </section>

        <footer>
            <div class="container">
                <div>© {{ date('Y') }} Invita · Invitaciones digitales</div>
                <div><a href="{{ route('invitation') }}" style="color:inherit; text-decoration:none;">← Ver invitación de ejemplo</a></div>
            </div>
        </footer>

    </div>
</body>
</html>
