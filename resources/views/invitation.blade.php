<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        :root{
            --p1: {{ $event->color_primary ?? '#d4a3b3' }};
            --p2: {{ $event->color_accent ?? '#e5c1cd' }};
            --p3: {{ $event->color_secondary ?? '#6e5a63' }};
            --p4:#33d9b2; --p5:#ffe066;
            
            /* Dynamic Sky Background based on primary color theme */
            @if($event->color_primary == '#d4a3b3' || !$event->color_primary)
                --bg-sky: #fcf3f5;
            @elseif($event->color_primary == '#9fb8c7')
                --bg-sky: #f0f5f8;
            @else
                --bg-sky: #f1f6f1;
            @endif
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{
            font-family:'Montserrat','Segoe UI',sans-serif;
            min-height:100vh;
            color:#2c2235;
            overflow-x:hidden;
            background-color:#f6f4f0;
            background-image: radial-gradient(circle at 10% 20%, rgba(212, 163, 179, 0.08) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(159, 184, 199, 0.08) 0%, transparent 40%);
            background-attachment: fixed;
        }

        .wrap{position:relative;z-index:2;max-width:480px;margin:0 auto;padding:18px 12px 30px}

        /* NEW CARD DESIGN MATCHING REFERENCE */
        .card{background:#ffffff;
            border-radius:24px;overflow:hidden;text-align:center;
            box-shadow:0 15px 45px rgba(0,0,0,0.06);
            border:1px solid #e1ebf2;
            animation:pop .6s cubic-bezier(.17,.89,.32,1.2)}
        @keyframes pop{from{transform:scale(.7);opacity:0}to{transform:scale(1);opacity:1}}

        .card-banner {
            position: relative;
            width: 100%;
            height: 90px;
            overflow: hidden;
            background: var(--bg-sky);
        }

        .card-body {
            padding: 20px 20px 28px 20px;
        }

        .script-title {
            font-family: 'Great Vibes', cursive;
            font-size: 3rem;
            line-height: 1.1;
            margin: 6px 0 4px 0;
            color: var(--p1);
            font-weight: normal;
        }

        .sub-invite {
            font-family: 'Playfair Display', serif;
            letter-spacing: 5px;
            font-size: 1.1rem;
            color: #3a2b4d;
            font-weight: 700;
            text-transform: uppercase;
            margin: 4px 0;
        }

        .flourish-divider {
            margin: 6px auto 12px auto;
            display: block;
            fill: none;
            stroke: var(--p3);
            stroke-width: 1.2px;
        }

        .date-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            color: var(--p3);
            font-weight: 700;
            margin: 12px 0 4px 0;
            line-height: 1.3;
        }

        .place-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            color: #5a4b6e;
            font-weight: 600;
            margin-top: 4px;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        /* Countdown elegant styling */
        .count{display:flex;gap:10px;justify-content:center;margin:16px 0}
        .count div{background:var(--p3);color:#fff;border-radius:10px;padding:8px 4px;min-width:56px;
            box-shadow:0 6px 15px rgba(124,77,255,.2)}
        .count b{display:block;font-size:1.3rem;font-weight:700}
        .count small{font-size:.6rem;opacity:.9;text-transform:uppercase;letter-spacing:0.5px}

        /* Info container (Legacy compatibility) */
        .info{display:none;}

        /* Detalles adicionales */
        .extra-details{margin:16px 0;text-align:left;background:rgba(255,255,255,0.9);border-radius:18px;padding:14px 18px;box-shadow:0 8px 20px rgba(0,0,0,0.04);border:1.5px dashed var(--p1)}
        .extra-details-item{display:flex;align-items:flex-start;gap:12px;padding:8px 0}
        .extra-details-item:not(:last-child){border-bottom:1px solid #f3effc}
        .extra-details-item span{font-size:1.6rem;line-height:1}
        .extra-details-item div{flex:1}
        .extra-details-item strong{display:block;font-size:0.78rem;color:var(--p3);text-transform:uppercase;margin-bottom:3px;letter-spacing:0.5px}
        .extra-details-item p{font-size:0.9rem;color:#4a3b6b;line-height:1.4}

        .badge{display:inline-block;background:rgba(51, 217, 178, 0.12);color:#065f4c;font-weight:bold;
            padding:6px 14px;border-radius:999px;margin:6px 0;font-size:0.8rem;border:1px solid rgba(51, 217, 178, 0.25)}

        /* Botones */
        .btn{display:inline-block;border:none;cursor:pointer;font-size:0.84rem;font-weight:bold;
            padding:10px 18px;border-radius:999px;color:#fff;text-decoration:none;transition:.2s;
            font-family:'Montserrat', sans-serif;}
        .btn:hover{transform:translateY(-2px)}
        .btn-main{background:var(--p1);box-shadow:0 4px 12px rgba(110,90,99,0.12)}
        .btn-wa{background:rgba(37, 211, 102, 0.08);color:#086b55;border:1.5px solid rgba(37,211,102,0.25)}
        .btn-maps{background:#f6f4f0;color:#6e5a63;border:1.5px solid var(--p2)}
        .actions-container{display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-top:20px}

        /* Admin float */
        .btn-admin-float{position:fixed;bottom:20px;right:20px;background:#3c1e50;color:#fff;
            width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;
            text-decoration:none;box-shadow:0 4px 12px rgba(0,0,0,.25);z-index:99;font-size:1.3rem;
            transition:.2s;border:2px solid #fff}
        .btn-admin-float:hover{transform:scale(1.1) rotate(45deg)}

        /* Formulario (modal) */
        .modal{position:fixed;inset:0;background:rgba(60,30,80,.55);display:none;
            align-items:center;justify-content:center;z-index:20;padding:16px}
        .modal.open{display:flex;animation:fade .3s}
        @keyframes fade{from{opacity:0}to{opacity:1}}
        .form{background:#fff;border-radius:26px;padding:28px;max-width:400px;width:100%;
            animation:pop .5s;text-align:left}
        .form h3{text-align:center;color:var(--p3);margin-bottom:16px;font-size:1.4rem}
        label{display:block;font-weight:bold;margin:12px 0 6px;font-size:.9rem}
        input,textarea,select{width:100%;padding:12px;border:2px solid #eee;border-radius:12px;font-size:1rem;font-family:inherit}
        input:focus,textarea:focus,select:focus{outline:none;border-color:var(--p1)}
        .close{float:right;cursor:pointer;font-size:1.4rem;color:#aaa}

        /* Acompañantes Tags */
        .companions-tags{display:flex;gap:6px;width:100%;margin-top:4px}
        .companion-tag{flex:1;height:38px;border:2px solid #eee;background:#fff;border-radius:10px;
            font-weight:bold;font-size:0.95rem;color:#2c2235;cursor:pointer;transition:all 0.2s;font-family:inherit;
            display:flex;align-items:center;justify-content:center}
        .companion-tag:hover{border-color:var(--p1)}
        .companion-tag.active{background:var(--p1);color:#fff;border-color:var(--p1);box-shadow:0 4px 10px rgba(110,90,99,0.12)}

        .alert{background:var(--p5);color:#5a4b00;padding:14px;border-radius:16px;margin-bottom:18px;
            font-weight:bold;animation:pop .5s}
        .err{color:#e63946;font-size:.85rem;margin-top:4px}

        .msgs{margin-top:32px}
        .msgs h3{color:var(--p3);margin-bottom:12px}
        .msg{background:rgba(255,255,255,.8);border-radius:16px;padding:12px 16px;margin-bottom:10px;text-align:left}
        .msg b{color:var(--p1)}

        .emoji-big{font-size:64px;display:inline-block;animation:wiggle 2.5s ease-in-out infinite}
        @keyframes wiggle{0%,100%{transform:rotate(-8deg)}50%{transform:rotate(8deg)}}

        /* Chatbot flotante */
        .chat-bubble {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: var(--p3);
            color: #fff;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
            z-index: 999;
            transition: all 0.2s;
            border: 2px solid #fff;
        }
        .chat-bubble:hover {
            transform: scale(1.1);
        }
        .chat-window {
            position: fixed;
            bottom: 80px;
            left: 20px;
            width: 310px;
            height: 380px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border: 2px solid var(--p1);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 999;
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .chat-header {
            background: var(--p3);
            color: #fff;
            padding: 12px 16px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
        }
        .chat-close {
            cursor: pointer;
            font-size: 1.1rem;
        }
        .chat-messages {
            flex: 1;
            padding: 12px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 8px;
            background: #faf8ff;
        }
        .chat-msg {
            max-width: 85%;
            padding: 8px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            line-height: 1.3;
        }
        .chat-msg.bot {
            background: #fff;
            align-self: flex-start;
            border: 1px solid #eae3f7;
            color: #3a2b4d;
        }
        .chat-msg.user {
            background: var(--p1);
            color: #fff;
            align-self: flex-end;
        }
        .chat-input-area {
            display: flex;
            border-top: 1px solid #eee;
            padding: 8px;
            background: #fff;
        }
        .chat-input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #eee;
            border-radius: 12px;
            font-size: 0.85rem;
            outline: none;
        }
        .chat-input:focus {
            border-color: var(--p1);
        }
        .chat-send {
            background: var(--p3);
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 12px;
            font-weight: bold;
            margin-left: 6px;
            cursor: pointer;
            font-size: 0.8rem;
        }
        /* Map container styling for toggle/collapse */
        .map-wrapper {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-out, opacity 0.3s ease;
            margin: 0;
        }
        .map-wrapper.open {
            max-height: 240px;
            opacity: 1;
            margin: 14px 0;
        }
        
        /* Subtle trigger link/button style */
        .btn-map-toggle {
            background: none;
            border: none;
            color: var(--p3);
            font-size: 0.85rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: underline;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 4px;
            transition: opacity 0.2s;
        }
        .btn-map-toggle:hover {
            opacity: 0.8;
        }

        /* Fotos subidas por el usuario (dentro del card body) */
        .user-photos{
            display:flex; justify-content:center; gap:-12px; margin:-40px auto 12px;
            position:relative; z-index:6;
        }
        .user-photos .user-photo{
            width:82px; height:82px; object-fit:cover;
            border-radius:50%; border:4px solid #fff;
            box-shadow:0 6px 14px rgba(0,0,0,0.15);
            background:#f6f4f0;
        }
        .user-photos .user-photo + .user-photo{ margin-left:-16px; }

        /* Par de imágenes para revelación de género */
        .reveal-pair{
            display:flex; justify-content:center; align-items:center; gap:8px;
            margin:14px auto 20px; max-width:100%;
        }
        .reveal-pair .reveal-img{
            width:44%; max-width:180px; aspect-ratio:1/1;
            object-fit:cover; object-position:center;
            border-radius:14px; background:#fff;
            filter:drop-shadow(0 8px 16px rgba(0,0,0,0.15));
        }
        .reveal-pair .reveal-img-1{ animation:dragon-float 4.5s ease-in-out infinite; }
        .reveal-pair .reveal-img-2{ animation:dragon-float-alt 4.5s ease-in-out infinite; }

        /* Variaciones por template */
        .tpl-polaroid .user-photos .user-photo{ border-radius:4px; border-width:6px; transform:rotate(-3deg); }
        .tpl-polaroid .user-photos .user-photo + .user-photo{ transform:rotate(4deg); }
        .tpl-ticket .user-photos .user-photo{ border-radius:6px; border-color:#f4ede0; }
        .tpl-frame .user-photos .user-photo{ border-radius:0; border-color:#c9a56a; border-width:5px; }
        .tpl-postcard .user-photos .user-photo{ border-radius:2px; border-color:#fefaf0; }
        .tpl-tag .user-photos .user-photo{ border-color:#faf3d9; }
        .tpl-sticker .user-photos .user-photo{ border-width:6px; box-shadow:0 0 0 3px var(--p3), 0 8px 16px rgba(0,0,0,0.2); }
        .tpl-bubble .user-photos .user-photo{ border-color:#fff; box-shadow:3px 3px 0 var(--p3); }

        /* ============================================================
           TEMPLATES — 10 identidades muy distintas
           Selector base: body.tpl-X y su primera .card (hero)
           ============================================================ */

        /* ---------- 1. CLASSIC — baseline, elegante y limpio ---------- */
        /* usa el estilo por defecto, sin overrides */

        /* ---------- 2. POLAROID — foto instantánea sobre madera ---------- */
        body.tpl-polaroid{
            background:#e9dcc4;
            background-image:
              repeating-linear-gradient(90deg, rgba(139,90,50,0.06) 0 2px, transparent 2px 8px),
              radial-gradient(circle at 30% 20%, rgba(139,90,50,0.12), transparent 60%);
        }
        .tpl-polaroid .wrap > .card:first-of-type{
            background:#fff; padding:16px 16px 60px; border-radius:2px;
            border:none; box-shadow:0 22px 44px rgba(0,0,0,0.28), 0 4px 8px rgba(0,0,0,0.12);
            transform:rotate(-1.6deg); position:relative;
        }
        .tpl-polaroid .wrap > .card:first-of-type .card-banner{ height:260px; border-radius:0; }
        .tpl-polaroid .wrap > .card:first-of-type::before{
            content:""; position:absolute; top:-14px; left:50%; transform:translateX(-50%) rotate(-5deg);
            width:150px; height:26px; background:rgba(255,215,120,0.85);
            box-shadow:0 3px 8px rgba(0,0,0,0.2); z-index:6;
        }
        .tpl-polaroid .script-title{ font-family:'Great Vibes', cursive; font-size:2.6rem; }
        .tpl-polaroid .sub-invite{ font-family:'Fredoka', sans-serif; letter-spacing:2px; font-weight:500; }

        /* ---------- 3. TICKET — boleto de concierto ---------- */
        body.tpl-ticket{
            background:#1a1a24;
            background-image:
              radial-gradient(circle at 20% 30%, rgba(255,255,255,0.05), transparent 40%),
              radial-gradient(circle at 80% 70%, var(--p1), transparent 50%);
        }
        .tpl-ticket .wrap{ padding-top:26px; }
        .tpl-ticket .wrap > .card:first-of-type{
            background:#f4ede0; border-radius:0; border:none; position:relative;
            box-shadow:0 20px 50px rgba(0,0,0,0.5);
            clip-path: polygon(
              0 0, 100% 0, 100% calc(50% - 8px),
              calc(100% - 12px) calc(50% - 8px), calc(100% - 12px) calc(50% + 8px), 100% calc(50% + 8px),
              100% 100%, 0 100%,
              0 calc(50% + 8px), 12px calc(50% + 8px), 12px calc(50% - 8px), 0 calc(50% - 8px)
            );
        }
        .tpl-ticket .wrap > .card:first-of-type .card-banner{
            background:var(--p3); height:70px; border-bottom:3px dashed rgba(0,0,0,0.2);
        }
        .tpl-ticket .wrap > .card:first-of-type .card-body{ padding-top:22px; }
        .tpl-ticket .script-title{
            font-family:'Playfair Display', serif; font-style:italic; font-weight:900;
            font-size:2.2rem; letter-spacing:-1px; color:var(--p3);
        }
        .tpl-ticket .sub-invite{
            font-family:'Courier New', monospace; letter-spacing:4px; font-size:0.85rem;
        }

        /* ---------- 4. FRAME — galería de museo ---------- */
        body.tpl-frame{
            background:#2a2028;
            background-image:
              radial-gradient(ellipse at center, rgba(255,255,255,0.04), transparent 60%);
        }
        .tpl-frame .wrap{ padding:32px 20px; }
        .tpl-frame .wrap > .card:first-of-type{
            background:#faf6ec; border-radius:0;
            border:12px solid #c9a56a;
            outline:2px solid #2a2028; outline-offset:-16px;
            box-shadow:0 30px 60px rgba(0,0,0,0.5), inset 0 0 0 4px #faf6ec;
            padding:12px 0 20px;
        }
        .tpl-frame .wrap > .card:first-of-type .card-banner{
            background:transparent; height:240px;
        }
        .tpl-frame .wrap > .card:first-of-type::before{
            content:"◆ ◆ ◆"; position:absolute; top:8px; left:0; right:0;
            text-align:center; color:#c9a56a; font-size:0.7rem; letter-spacing:8px; z-index:2;
        }
        .tpl-frame .script-title{
            font-family:'Playfair Display', serif; font-style:italic; font-weight:400;
            font-size:2.6rem; color:#2a2028;
        }
        .tpl-frame .sub-invite{
            font-family:'Playfair Display', serif; letter-spacing:8px; color:#c9a56a;
            font-weight:400; font-size:0.85rem;
        }

        /* ---------- 5. BANNER — festivo con confeti ---------- */
        body.tpl-banner{
            background:#fff8ee;
            background-image:
              radial-gradient(circle at 10% 15%, var(--p1) 6px, transparent 7px),
              radial-gradient(circle at 85% 22%, var(--p2) 5px, transparent 6px),
              radial-gradient(circle at 20% 60%, var(--p3) 4px, transparent 5px),
              radial-gradient(circle at 90% 75%, var(--p1) 5px, transparent 6px),
              radial-gradient(circle at 45% 90%, var(--p2) 6px, transparent 7px),
              radial-gradient(circle at 60% 8%, var(--p3) 3px, transparent 4px);
            background-attachment:fixed;
        }
        .tpl-banner .wrap{ padding-top:44px; }
        .tpl-banner .wrap > .card:first-of-type{
            background:#fff; border-radius:24px; overflow:visible;
            box-shadow:0 18px 40px rgba(0,0,0,0.1);
        }
        .tpl-banner .wrap > .card:first-of-type::before{
            content:"✦ INVITACIÓN ESPECIAL ✦";
            position:absolute; top:-18px; left:-16px; right:-16px;
            background:var(--p3); color:#fff; padding:10px 0; text-align:center;
            font-family:'Playfair Display', serif; letter-spacing:6px; text-transform:uppercase;
            font-size:0.8rem; font-weight:900; z-index:5;
            box-shadow:0 8px 20px rgba(0,0,0,0.2); transform:rotate(-2.5deg);
            border-radius:4px;
        }
        .tpl-banner .wrap > .card:first-of-type::after{
            content:""; position:absolute; bottom:-8px; left:20%; right:20%; height:16px;
            background:
              radial-gradient(circle at 8px 0, var(--p1) 6px, transparent 7px) 0 0/16px 16px repeat-x;
            z-index:2;
        }

        /* ---------- 6. BUBBLE — chat/mensajería, moderno y juguetón ---------- */
        body.tpl-bubble{
            background: linear-gradient(160deg, var(--p2) 0%, #fff 60%);
            background-attachment:fixed;
        }
        .tpl-bubble .wrap > .card:first-of-type{
            border-radius:36px; border:3px solid var(--p3);
            background:#fff; box-shadow:6px 6px 0 var(--p3);
            position:relative; overflow:visible;
        }
        .tpl-bubble .wrap > .card:first-of-type::after{
            content:""; position:absolute; bottom:-18px; left:36px;
            width:0; height:0;
            border-left:14px solid transparent; border-right:14px solid transparent;
            border-top:18px solid #fff;
            filter:drop-shadow(3px 6px 0 var(--p3));
        }
        .tpl-bubble .wrap > .card:first-of-type .card-banner{ border-radius:32px 32px 0 0; height:80px; }
        .tpl-bubble .script-title{ font-family:'Fredoka', sans-serif; font-weight:700; font-size:2.4rem; }
        .tpl-bubble .sub-invite{ font-family:'Fredoka', sans-serif; letter-spacing:1px; font-weight:400; }

        /* ---------- 7. RIBBON — regalo envuelto ---------- */
        body.tpl-ribbon{
            background:#fdf7fa;
            background-image:
              repeating-linear-gradient(45deg, transparent 0 24px, rgba(0,0,0,0.02) 24px 25px),
              repeating-linear-gradient(-45deg, transparent 0 24px, rgba(0,0,0,0.02) 24px 25px);
        }
        .tpl-ribbon .wrap{ padding-top:40px; }
        .tpl-ribbon .wrap > .card:first-of-type{
            background:#fff; border-radius:12px; position:relative; overflow:visible;
            box-shadow:0 20px 40px rgba(0,0,0,0.08);
        }
        .tpl-ribbon .wrap > .card:first-of-type::before{
            content:""; position:absolute; top:0; left:0; right:0; height:14px;
            background:var(--p1);
            box-shadow:inset 0 -2px 0 rgba(0,0,0,0.1);
            z-index:3;
        }
        .tpl-ribbon .wrap > .card:first-of-type::after{
            content:""; position:absolute; top:-30px; left:50%; transform:translateX(-50%);
            width:64px; height:44px; background:var(--p1); z-index:4;
            border-radius:50% 50% 50% 50% / 60% 60% 40% 40%;
            box-shadow:
              -30px 4px 0 -8px var(--p1),
              30px 4px 0 -8px var(--p1),
              0 26px 0 -18px var(--p3);
        }
        .tpl-ribbon .wrap > .card:first-of-type .card-banner{ margin-top:14px; }

        /* ---------- 8. POSTCARD — postal vintage con sello ---------- */
        body.tpl-postcard{
            background:#e8e0d0;
            background-image:
              repeating-linear-gradient(0deg, rgba(120,90,60,0.05) 0 1px, transparent 1px 40px),
              repeating-linear-gradient(90deg, rgba(120,90,60,0.05) 0 1px, transparent 1px 40px);
        }
        .tpl-postcard .wrap > .card:first-of-type{
            background:#fefaf0; border:1px solid #c9b89a; border-radius:2px;
            box-shadow:0 18px 40px rgba(0,0,0,0.18), inset 0 0 30px rgba(180,140,90,0.06);
            position:relative; overflow:visible;
        }
        .tpl-postcard .wrap > .card:first-of-type::before{
            content:"PAR AVION"; position:absolute; top:12px; left:12px;
            font-family:'Courier New', monospace; letter-spacing:2px; font-size:0.65rem;
            color:#8a4a2a; border:1px solid #8a4a2a; padding:2px 6px; z-index:3;
        }
        .tpl-postcard .wrap > .card:first-of-type::after{
            content:""; position:absolute; top:12px; right:12px; width:56px; height:64px;
            background:
              repeating-linear-gradient(45deg, var(--p1) 0 6px, var(--p2) 6px 12px);
            border:4px solid #fefaf0;
            box-shadow:0 0 0 1px #8a4a2a, 3px 3px 0 rgba(0,0,0,0.15);
            clip-path:polygon(0 4px,4px 0,8px 4px,12px 0,16px 4px,20px 0,24px 4px,28px 0,32px 4px,36px 0,40px 4px,44px 0,48px 4px,52px 0,56px 4px,52px 8px,56px 12px,52px 16px,56px 20px,52px 24px,56px 28px,52px 32px,56px 36px,52px 40px,56px 44px,52px 48px,56px 52px,52px 56px,56px 60px,52px 64px,48px 60px,44px 64px,40px 60px,36px 64px,32px 60px,28px 64px,24px 60px,20px 64px,16px 60px,12px 64px,8px 60px,4px 64px,0 60px,4px 56px,0 52px,4px 48px,0 44px,4px 40px,0 36px,4px 32px,0 28px,4px 24px,0 20px,4px 16px,0 12px,4px 8px);
            z-index:3;
        }
        .tpl-postcard .script-title{ font-family:'Great Vibes', cursive; color:var(--p3); }
        .tpl-postcard .sub-invite{ font-family:'Courier New', monospace; letter-spacing:6px; }

        /* ---------- 9. TAG — etiqueta de equipaje ---------- */
        body.tpl-tag{
            background:#1f2a24;
            background-image:
              radial-gradient(circle at 50% 0, rgba(255,255,255,0.06), transparent 40%);
        }
        .tpl-tag .wrap{ padding-top:50px; }
        .tpl-tag .wrap > .card:first-of-type{
            background:#faf3d9; border:none; border-radius:8px;
            box-shadow:0 20px 40px rgba(0,0,0,0.5);
            position:relative; overflow:visible;
            clip-path:polygon(0 22px, 42% 22px, 50% 0, 58% 22px, 100% 22px, 100% 100%, 0 100%);
            padding-top:36px;
        }
        .tpl-tag .wrap > .card:first-of-type::before{
            content:""; position:absolute; top:6px; left:50%; transform:translateX(-50%);
            width:14px; height:14px; border-radius:50%;
            background:#1f2a24; box-shadow:inset 0 0 0 2px #d4c896; z-index:6;
        }
        .tpl-tag .wrap > .card:first-of-type::after{
            content:""; position:absolute; top:-40px; left:50%; transform:translateX(-50%);
            width:2px; height:44px; background:#8a7550;
        }
        .tpl-tag .script-title{
            font-family:'Playfair Display', serif; font-weight:900; font-style:normal;
            text-transform:uppercase; letter-spacing:2px; font-size:1.8rem;
        }
        .tpl-tag .sub-invite{ font-family:'Courier New', monospace; letter-spacing:4px; }

        /* ---------- Intro Dragon: overlay con esfera del dragón ---------- */
        body.dragon-locked{ overflow:hidden; }
        .dragon-intro{
            position:fixed; inset:0; z-index:9998;
            display:flex; align-items:center; justify-content:center;
            background:
              radial-gradient(circle at 50% 40%, #fff2d9 0%, #fdd9a0 45%, #e88b3a 100%);
            transition: opacity .8s ease, transform .8s ease;
        }
        .dragon-intro.is-opening{
            opacity:0; transform:scale(1.15);
            filter:blur(6px);
        }
        .dragon-intro-content{
            text-align:center; position:relative; z-index:2;
            animation: dragon-intro-enter 1s cubic-bezier(.2,.8,.2,1) both;
        }
        @keyframes dragon-intro-enter{
            from{ opacity:0; transform:translateY(30px); }
            to  { opacity:1; transform:translateY(0); }
        }
        .dragon-intro-hint{
            font-family:'Playfair Display', serif; font-size:0.95rem;
            color:#5a3a10; letter-spacing:2px; margin-bottom:28px;
            animation: dragon-hint-pulse 2.4s ease-in-out infinite;
        }
        @keyframes dragon-hint-pulse{
            0%,100%{ opacity:0.7; }
            50%    { opacity:1; }
        }
        .dragon-intro-sub{
            font-family:'Great Vibes', cursive; font-size:1.8rem;
            color:#8a4a1a; margin-top:32px;
        }

        /* Estrellitas de fondo en el intro */
        .dragon-intro-stars{
            position:absolute; inset:0; z-index:1; pointer-events:none;
            background-image:
              radial-gradient(circle at 12% 18%, #fff 2.5px, transparent 3.5px),
              radial-gradient(circle at 88% 22%, #fff 2px, transparent 3px),
              radial-gradient(circle at 20% 78%, #fff 2.5px, transparent 3.5px),
              radial-gradient(circle at 82% 82%, #fff 2px, transparent 3px),
              radial-gradient(circle at 50% 8%,  #fff 1.8px, transparent 2.8px),
              radial-gradient(circle at 6% 55%,  #fff 1.8px, transparent 2.8px),
              radial-gradient(circle at 94% 55%, #fff 2.2px, transparent 3.2px);
            animation:dragon-twinkle 3s ease-in-out infinite;
            opacity:0.9;
        }

        /* La esfera */
        .dragon-orb{
            position:relative;
            width:200px; height:200px; border-radius:50%;
            border:none; padding:0; cursor:pointer;
            background:
              radial-gradient(circle at 32% 30%, #fff5c0 0%, #ffd769 22%, #f2a83a 55%, #c46a10 90%);
            box-shadow:
              inset -10px -14px 30px rgba(140,60,0,0.35),
              inset 12px 14px 24px rgba(255,255,255,0.55),
              0 20px 60px rgba(232,139,58,0.55),
              0 0 90px rgba(255,215,110,0.6);
            animation: dragon-orb-float 3.2s ease-in-out infinite,
                       dragon-orb-glow 2.4s ease-in-out infinite;
            transition: transform .4s cubic-bezier(.2,.8,.2,1);
        }
        .dragon-orb:hover{ transform:scale(1.06); }
        .dragon-orb:active{ transform:scale(0.96); }
        .dragon-intro.is-opening .dragon-orb{
            animation: dragon-orb-burst 1.2s cubic-bezier(.4,0,.6,1) forwards;
        }
        @keyframes dragon-orb-float{
            0%,100%{ transform:translateY(0); }
            50%    { transform:translateY(-12px); }
        }
        @keyframes dragon-orb-glow{
            0%,100%{ box-shadow: inset -10px -14px 30px rgba(140,60,0,0.35),
                                 inset 12px 14px 24px rgba(255,255,255,0.55),
                                 0 20px 60px rgba(232,139,58,0.55),
                                 0 0 90px rgba(255,215,110,0.6); }
            50%    { box-shadow: inset -10px -14px 30px rgba(140,60,0,0.35),
                                 inset 12px 14px 24px rgba(255,255,255,0.7),
                                 0 20px 80px rgba(232,139,58,0.8),
                                 0 0 140px rgba(255,215,110,0.9); }
        }
        @keyframes dragon-orb-burst{
            0%  { transform:scale(1) rotate(0); filter:brightness(1); }
            30% { transform:scale(1.25) rotate(45deg); filter:brightness(1.4); }
            60% { transform:scale(3) rotate(180deg); filter:brightness(2); opacity:0.9; }
            100%{ transform:scale(8) rotate(360deg); filter:brightness(3); opacity:0; }
        }

        /* Brillo sobre la esfera */
        .dragon-orb-shine{
            position:absolute; top:14%; left:20%;
            width:38%; height:26%; border-radius:50%;
            background:radial-gradient(ellipse at center, rgba(255,255,255,0.85), transparent 70%);
            pointer-events:none;
        }

        /* Las 4 estrellas rojas dentro de la esfera */
        .dragon-orb-stars{
            position:absolute; top:50%; left:50%;
            transform:translate(-50%, -50%);
            width:110px; height:110px;
        }
        .dragon-orb-stars span{
            position:absolute; width:26px; height:26px;
            background:#c8221a;
            clip-path:polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            filter:drop-shadow(0 2px 3px rgba(0,0,0,0.35));
        }
        .dragon-orb-stars span:nth-child(1){ top:6px;  left:50%; transform:translateX(-50%); }
        .dragon-orb-stars span:nth-child(2){ top:50%; left:6px; transform:translateY(-50%); }
        .dragon-orb-stars span:nth-child(3){ top:50%; right:6px; transform:translateY(-50%); }
        .dragon-orb-stars span:nth-child(4){ bottom:6px; left:50%; transform:translateX(-50%); }

        /* Responsive del intro */
        @media (max-width:400px){
            .dragon-orb{ width:160px; height:160px; }
            .dragon-orb-stars{ width:90px; height:90px; }
            .dragon-orb-stars span{ width:22px; height:22px; }
            .dragon-intro-hint{ font-size:0.85rem; padding:0 20px; }
            .dragon-intro-sub{ font-size:1.5rem; }
        }

        /* ---------- DRAGON — estilo Dragon Ball / revelación (refinado) ---------- */
        @keyframes dragon-float{
            0%,100%{ transform:translateY(0) rotate(-2deg); }
            50%    { transform:translateY(-14px) rotate(2deg); }
        }
        @keyframes dragon-float-alt{
            0%,100%{ transform:translateY(-6px) rotate(2deg); }
            50%    { transform:translateY(8px) rotate(-2deg); }
        }
        @keyframes dragon-twinkle{
            0%,100%{ opacity:0.25; transform:scale(0.85); }
            50%    { opacity:1;    transform:scale(1.1); }
        }
        @keyframes dragon-slide-up{
            from{ opacity:0; transform:translateY(30px); }
            to  { opacity:1; transform:translateY(0); }
        }
        @keyframes dragon-glow{
            0%,100%{ text-shadow:0 4px 12px rgba(232,139,58,0.25), 0 0 24px rgba(255,215,110,0.15); }
            50%    { text-shadow:0 4px 22px rgba(232,139,58,0.6),  0 0 40px rgba(255,215,110,0.5); }
        }
        @keyframes dragon-pulse-btn{
            0%,100%{ box-shadow:0 6px 20px rgba(232,139,58,0.35); }
            50%    { box-shadow:0 6px 28px rgba(232,139,58,0.65), 0 0 0 8px rgba(232,139,58,0.15); }
        }
        @keyframes dragon-shine{
            0%  { transform:translateX(-140%) skewX(-20deg); }
            100%{ transform:translateX(240%) skewX(-20deg); }
        }
        @keyframes dragon-cloud-drift{
            0%,100%{ transform:translateX(0); }
            50%    { transform:translateX(10px); }
        }
        @keyframes dragon-orb-spin{
            from{ transform:rotate(0deg); }
            to  { transform:rotate(360deg); }
        }

        body.tpl-dragon{
            background:#fefaf5;
            background-image:
              radial-gradient(circle at 15% 12%, rgba(255,215,110,0.10), transparent 45%),
              radial-gradient(circle at 85% 88%, rgba(255,215,110,0.08), transparent 45%);
            background-attachment:fixed;
            font-family:'Playfair Display', serif;
            position:relative;
        }
        /* Textura papel + estrellitas de fondo */
        body.tpl-dragon::before{
            content:""; position:fixed; inset:0; pointer-events:none; z-index:0;
            background-image:
              radial-gradient(circle at 8% 20%,  #ffd58a 2px, transparent 3px),
              radial-gradient(circle at 92% 30%, #ffd58a 2px, transparent 3px),
              radial-gradient(circle at 20% 70%, #f4a5b8 1.5px, transparent 2.5px),
              radial-gradient(circle at 82% 78%, #ffd58a 2px, transparent 3px),
              radial-gradient(circle at 50% 12%, #ffd58a 1.5px, transparent 2.5px),
              radial-gradient(circle at 30% 45%, #f4a5b8 1.5px, transparent 2.5px),
              radial-gradient(circle at 70% 62%, #ffd58a 1.5px, transparent 2.5px);
            animation:dragon-twinkle 4s ease-in-out infinite;
        }
        /* Grano de papel sutil */
        body.tpl-dragon::after{
            content:""; position:fixed; inset:0; pointer-events:none; z-index:0; opacity:0.4;
            background-image:
              repeating-linear-gradient(0deg,   rgba(170,110,50,0.025) 0 1px, transparent 1px 3px),
              repeating-linear-gradient(90deg,  rgba(170,110,50,0.025) 0 1px, transparent 1px 3px);
        }

        .tpl-dragon .wrap{ padding:28px 16px 44px; position:relative; z-index:2; max-width:500px; }

        .tpl-dragon .wrap > .card:first-of-type{
            background:transparent; border:none; box-shadow:none; border-radius:0;
            overflow:visible; animation:dragon-slide-up .8s cubic-bezier(.2,.8,.2,1) both;
        }
        .tpl-dragon .wrap > .card:first-of-type .card-banner{
            background:transparent !important; height:auto !important; min-height:180px;
            padding:12px 0 4px; position:relative;
        }
        /* Halo dorado suave detrás del personaje */
        .tpl-dragon .wrap > .card:first-of-type .card-banner::before{
            content:""; position:absolute; top:30px; left:50%; transform:translateX(-50%);
            width:260px; height:180px; z-index:0;
            background:radial-gradient(ellipse at center, rgba(255,215,110,0.28) 0%, transparent 65%);
            filter:blur(4px);
        }
        /* Estrellitas decorativas alrededor del personaje */
        .tpl-dragon .wrap > .card:first-of-type .card-banner::after{
            content:""; position:absolute; inset:0; z-index:1; pointer-events:none;
            background-image:
              radial-gradient(circle at 10% 30%, #ffb74d 2.5px, transparent 3.5px),
              radial-gradient(circle at 90% 25%, #ffb74d 2.5px, transparent 3.5px),
              radial-gradient(circle at 15% 78%, #ffb74d 2px, transparent 3px),
              radial-gradient(circle at 85% 80%, #ffb74d 2px, transparent 3px);
            animation:dragon-twinkle 3s ease-in-out infinite;
            opacity:0.7;
        }
        .tpl-dragon .wrap > .card:first-of-type .card-banner svg,
        .tpl-dragon .wrap > .card:first-of-type > svg{ display:none !important; }
        .tpl-dragon .wrap > .card:first-of-type .card-body{ padding:10px 4px 24px; text-align:center; }

        /* Personaje del hero */
        .tpl-dragon .wrap > .card:first-of-type .card-banner img{
            max-width:74% !important; max-height:230px !important;
            position:relative; z-index:2;
            animation:dragon-float 4.5s ease-in-out infinite;
            filter:drop-shadow(0 12px 22px rgba(0,0,0,0.18));
        }

        /* Título con brillo animado */
        .tpl-dragon .script-title{
            font-family:'Great Vibes', cursive; font-weight:400;
            font-size:3.6rem; line-height:1; color:#e88b3a;
            margin:24px 0 10px; letter-spacing:1px;
            background:linear-gradient(180deg, #f5b06d 0%, #e88b3a 55%, #c46a10 100%);
            -webkit-background-clip:text; background-clip:text;
            -webkit-text-fill-color:transparent;
            animation:dragon-glow 3s ease-in-out infinite, dragon-slide-up .9s .1s cubic-bezier(.2,.8,.2,1) both;
        }
        /* Ornamentos laterales del subtítulo */
        .tpl-dragon .sub-invite{
            font-family:'Playfair Display', serif;
            letter-spacing:10px; font-size:0.9rem; font-weight:700;
            color:#3a2b4d; text-transform:uppercase;
            margin:6px 0 8px;
            display:inline-flex; align-items:center; gap:12px;
            animation:dragon-slide-up 1s .2s cubic-bezier(.2,.8,.2,1) both;
        }
        .tpl-dragon .sub-invite::before,
        .tpl-dragon .sub-invite::after{
            content:""; display:inline-block; width:36px; height:1.5px;
            background:linear-gradient(90deg, transparent, #e88b3a);
        }
        .tpl-dragon .sub-invite::after{ background:linear-gradient(90deg, #e88b3a, transparent); }

        /* Divisor de esferas del dragón */
        .tpl-dragon .wrap > .card:first-of-type .card-body::after{
            content:""; display:block; height:16px; margin:14px auto 0;
            width:120px;
            background:
              radial-gradient(circle at 15%  50%, #e88b3a 5px, transparent 6px),
              radial-gradient(circle at 38%  50%, #e88b3a 5px, transparent 6px),
              radial-gradient(circle at 62%  50%, #e88b3a 5px, transparent 6px),
              radial-gradient(circle at 85%  50%, #e88b3a 5px, transparent 6px);
        }

        /* Secciones internas: transparente, espaciadas, con divisores */
        .tpl-dragon .extra-details,
        .tpl-dragon .msgs,
        .tpl-dragon .form,
        .tpl-dragon .count{
            background:transparent !important; border:none !important;
            box-shadow:none !important; padding:16px 4px !important;
            position:relative;
        }
        .tpl-dragon .extra-details{
            border-top:1.5px dashed rgba(232,139,58,0.4) !important;
            border-bottom:1.5px dashed rgba(232,139,58,0.4) !important;
            border-left:none !important; border-right:none !important;
            border-radius:0 !important; padding:18px 14px !important;
            margin:28px 0 !important;
            background:linear-gradient(180deg, rgba(255,244,225,0.5), rgba(255,244,225,0)) !important;
        }
        .tpl-dragon .extra-details-item strong{
            color:#e88b3a !important; font-weight:800;
            letter-spacing:1.5px !important;
        }
        .tpl-dragon .extra-details-item span{
            filter:drop-shadow(0 2px 4px rgba(232,139,58,0.3));
        }

        /* Sub-headers en script dorado */
        .tpl-dragon .form h3,
        .tpl-dragon .msgs h3{
            font-family:'Great Vibes', cursive; font-weight:400;
            font-size:2.8rem; color:#c46a10;
            margin:28px 0 10px;
            text-shadow:0 2px 8px rgba(232,139,58,0.2);
        }

        /* Countdown: separador tipo "esfera" entre bloques */
        .tpl-dragon .count{
            margin:30px 0; display:flex; justify-content:center; align-items:flex-end;
            gap:14px; flex-wrap:nowrap;
        }
        .tpl-dragon .count > div{
            background:transparent !important; color:#e88b3a !important;
            border-radius:0 !important; padding:0 !important;
            min-width:56px !important; box-shadow:none !important;
            border-bottom:2.5px solid #e88b3a; padding-bottom:6px !important;
            font-family:'Playfair Display', serif;
            position:relative;
        }
        .tpl-dragon .count > div + div::before{
            content:""; position:absolute; left:-12px; top:50%;
            transform:translateY(-50%);
            width:6px; height:6px; border-radius:50%;
            background:#e88b3a; box-shadow:0 0 6px rgba(232,139,58,0.6);
        }
        .tpl-dragon .count > div big,
        .tpl-dragon .count > div span:first-child{
            font-family:'Playfair Display', serif !important; font-weight:900;
            font-size:2.6rem !important; color:#e88b3a !important; line-height:1;
        }
        .tpl-dragon .count > div small,
        .tpl-dragon .count > div span:last-child{
            font-family:'Playfair Display', serif !important;
            font-size:0.62rem !important; letter-spacing:2px;
            color:#3a2b4d !important; text-transform:uppercase; font-weight:600;
        }

        /* Botones con brillo sweep */
        .tpl-dragon .btn{
            border-radius:30px !important; padding:12px 34px !important;
            position:relative; overflow:hidden !important;
        }
        .tpl-dragon .btn-main{
            background:linear-gradient(135deg, #f5b06d 0%, #e88b3a 55%, #c46a10 100%) !important;
            color:#fff !important; font-weight:700; letter-spacing:1px;
            animation:dragon-pulse-btn 2.5s ease-in-out infinite;
        }
        .tpl-dragon .btn-main::before{
            content:""; position:absolute; top:0; bottom:0; width:40%;
            background:linear-gradient(90deg, transparent, rgba(255,255,255,0.55), transparent);
            animation:dragon-shine 3.5s ease-in-out infinite;
        }
        .tpl-dragon .btn-maps{
            background:#fff !important; color:#e88b3a !important;
            border:2px solid #e88b3a !important;
        }
        .tpl-dragon .btn-maps:hover{ background:#fff4e6 !important; }

        /* Fotos del usuario */
        .tpl-dragon .user-photos{ margin-top:-16px; gap:6px; }
        .tpl-dragon .user-photos .user-photo{
            border:4px solid #fff; width:104px; height:104px; border-radius:50%;
            box-shadow:
              0 6px 18px rgba(232,139,58,0.25),
              0 0 0 3px rgba(232,139,58,0.2),
              inset 0 0 0 1px rgba(255,255,255,0.4);
        }
        .tpl-dragon .user-photos .user-photo:first-child{ animation:dragon-float 5s ease-in-out infinite; }
        .tpl-dragon .user-photos .user-photo:nth-child(2){ animation:dragon-float-alt 5s ease-in-out infinite; }

        /* Inputs del formulario RSVP */
        .tpl-dragon input, .tpl-dragon textarea, .tpl-dragon select{
            border:1.5px solid rgba(232,139,58,0.3) !important;
            border-radius:24px !important; background:#fff !important;
            padding:12px 16px !important; font-family:'Playfair Display', serif !important;
        }
        .tpl-dragon input:focus, .tpl-dragon textarea:focus, .tpl-dragon select:focus{
            border-color:#e88b3a !important;
            box-shadow:0 0 0 4px rgba(232,139,58,0.15) !important;
        }
        .tpl-dragon .companion-tag{
            border:1.5px solid rgba(232,139,58,0.3) !important;
            border-radius:20px !important;
        }
        .tpl-dragon .companion-tag.active{
            background:#e88b3a !important; color:#fff !important;
            border-color:#e88b3a !important;
            box-shadow:0 4px 12px rgba(232,139,58,0.4) !important;
        }

        /* Mensajes de invitados */
        .tpl-dragon .msg{
            background:linear-gradient(180deg, rgba(255,255,255,0.9), rgba(255,244,225,0.7)) !important;
            border:1px solid rgba(232,139,58,0.25) !important;
            border-radius:14px !important; padding:12px 14px !important;
            margin:8px 0 !important;
            box-shadow:0 3px 8px rgba(232,139,58,0.06);
        }
        .tpl-dragon .msg b{ color:#e88b3a !important; font-weight:700; }


        /* ---------- 10. STICKER — die-cut, personaje protagonista ---------- */
        body.tpl-sticker{
            background:var(--p1);
            background-image:
              radial-gradient(circle at 20% 20%, rgba(255,255,255,0.15), transparent 40%),
              radial-gradient(circle at 80% 80%, var(--p2), transparent 50%);
            background-attachment:fixed;
        }
        .tpl-sticker .wrap > .card:first-of-type{
            background:#fff; border:6px solid #fff; border-radius:0;
            filter:drop-shadow(0 12px 20px rgba(0,0,0,0.25));
            box-shadow:0 0 0 4px var(--p3);
            transform:rotate(-1deg);
        }
        .tpl-sticker .wrap > .card:first-of-type .card-banner{
            background:var(--p2); height:200px;
        }
        .tpl-sticker .script-title{
            font-family:'Fredoka', sans-serif; font-weight:700;
            text-transform:uppercase; font-size:2.4rem; letter-spacing:-1px;
            color:var(--p3); -webkit-text-stroke:1px var(--p3);
        }
        .tpl-sticker .sub-invite{
            font-family:'Fredoka', sans-serif; font-weight:600;
            background:var(--p3); color:#fff; display:inline-block;
            padding:4px 14px; border-radius:20px; letter-spacing:2px;
        }
    </style>
</head>
<body class="tpl-{{ $event->template ?? 'classic' }}">
    @if($isPreview)
        <div style="position: sticky; top: 0; left: 0; right: 0; background: rgba(255, 179, 71, 0.95); backdrop-filter: blur(5px); color: #5a4b00; text-align: center; padding: 10px; font-weight: bold; z-index: 1000; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border-bottom: 2px solid #ffb347; display: flex; align-items: center; justify-content: center; gap: 15px; font-size: 0.9rem;">
            <span>🚧 Vista Previa (Modo Borrador)</span>
            <a href="{{ route('admin') }}" style="background: #3c1e50; color: #fff; text-decoration: none; padding: 4px 12px; border-radius: 6px; font-size: 0.8rem;">Volver al Panel</a>
        </div>
    @endif

    @if(($event->template ?? '') === 'dragon' && ($event->is_published || $isPreview))
        <div id="dragon-intro" class="dragon-intro">
            <div class="dragon-intro-stars"></div>
            <div class="dragon-intro-content">
                <p class="dragon-intro-hint">✨ Toca la esfera para abrir tu invitación ✨</p>
                <button type="button" class="dragon-orb" onclick="openDragonIntro()" aria-label="Abrir invitación">
                    <span class="dragon-orb-shine"></span>
                    <span class="dragon-orb-stars">
                        <span></span><span></span><span></span><span></span>
                    </span>
                </button>
                <p class="dragon-intro-sub">— Revelación de género —</p>
            </div>
        </div>
        <script>
            function openDragonIntro(){
                const intro = document.getElementById('dragon-intro');
                if (!intro) return;
                intro.classList.add('is-opening');
                setTimeout(() => { intro.style.display = 'none'; document.body.classList.remove('dragon-locked'); }, 1600);
            }
            document.body.classList.add('dragon-locked');
        </script>
    @endif


    @if(!$event->is_published && !$isPreview)
        <!-- Pantalla de Borrador / Próximamente -->
        <div class="wrap" style="max-width: 500px; margin-top: 15vh;">
            <div class="card" style="border: 1px solid #e1ebf2; border-radius: 32px; padding: 40px 30px;">
                <h1 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--p1); margin-top: 10px;">Próximamente</h1>
                <p class="subtitle" style="margin-top: 16px; font-size: 1rem; line-height: 1.6; color: #5a4b6e;">
                    Esta invitación está siendo preparada por el organizador.<br>Vuelve a visitarnos más tarde.
                </p>
                <div style="margin-top: 30px;">
                    <a href="{{ route('admin') }}" class="btn btn-main" style="text-decoration: none; font-size: 0.95rem;">
                        Ir al Panel de Control
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="wrap">
            @if(session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif

            <div class="card">
                <!-- 1. Sky blue banner at the top of the card with clothesline and baby items -->
                <div class="card-banner">
                    @if(in_array($event->event_type, ['babyshower', 'bienvenida', 'revelacion']))
                        <!-- Baby Shower / Baby Welcome Clothesline -->
                        <svg viewBox="0 0 600 120" preserveAspectRatio="none" style="width:100%; height:120px; display:block;">
                            <!-- Sky -->
                            <rect width="600" height="120" fill="var(--bg-sky)"/>
                            
                            <!-- Clouds -->
                            <path d="M 50 110 C 80 90, 120 90, 150 110" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="12" stroke-linecap="round" />
                            <path d="M 380 100 C 410 80, 460 80, 490 100" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="15" stroke-linecap="round" />
                            
                            <!-- Clothesline -->
                            <line x1="-10" y1="35" x2="610" y2="35" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" stroke-dasharray="4 4" />
                            
                            <!-- Pins and items -->
                            <!-- Bottle at x=110 -->
                            <rect x="109" y="27" width="2.5" height="10" fill="#d7ccc8" rx="0.5" />
                            <!-- Bottle nipple -->
                            <path d="M106,37 C106,32 114,32 114,37" fill="#ffe082" />
                            <!-- Bottle cap -->
                            <rect x="104" y="37" width="12" height="5" fill="var(--p1)" rx="1" />
                            <!-- Bottle body -->
                            <rect x="102" y="42" width="16" height="28" fill="rgba(255,255,255,0.85)" stroke="var(--p1)" stroke-width="1" rx="2" />
                            <line x1="105" y1="50" x2="115" y2="50" stroke="var(--p1)" stroke-width="1" />
                            <line x1="105" y1="57" x2="113" y2="57" stroke="var(--p1)" stroke-width="1" />

                            <!-- Sock 1 at x=200 -->
                            <rect x="199" y="27" width="2.5" height="10" fill="#d7ccc8" rx="0.5" />
                            <path d="M196,35 L204,35 L204,50 C204,55 192,55 192,50 L196,45 Z" fill="var(--p3)" />

                            <!-- Onesie at x=300 (center) -->
                            <rect x="292" y="27" width="2.5" height="10" fill="#d7ccc8" rx="0.5" />
                            <rect x="305" y="27" width="2.5" height="10" fill="#d7ccc8" rx="0.5" />
                            <!-- Onesie body -->
                            <path d="M285,35 L315,35 L315,60 C315,67 308,67 300,67 C292,67 285,67 285,60 Z" fill="var(--p1)" />
                            <path d="M285,35 L277,41 L281,47 L285,43 Z" fill="var(--p1)" />
                            <path d="M315,35 L323,41 L319,47 L315,43 Z" fill="var(--p1)" />
                            <path d="M300,58 C300,58 296,54 296,51 C296,49 298,47 300,47 C302,47 304,49 304,51 C304,54 300,58 300,58 Z" fill="#fff" />
                            <circle cx="294" cy="62" r="1" fill="#fff" />
                            <circle cx="306" cy="62" r="1" fill="#fff" />

                            <!-- Sock 2 at x=400 -->
                            <rect x="399" y="27" width="2.5" height="10" fill="#d7ccc8" rx="0.5" />
                            <path d="M396,35 L404,35 L404,50 C404,55 392,55 392,50 L396,45 Z" fill="var(--p3)" />

                            <!-- Pacifier at x=490 -->
                            <rect x="489" y="27" width="2.5" height="10" fill="#d7ccc8" rx="0.5" />
                            <circle cx="490" cy="51" r="8" fill="none" stroke="var(--p2)" stroke-width="2" />
                            <rect x="481" y="37" width="18" height="5" fill="var(--p1)" rx="1.5" />
                            <path d="M485,37 C485,33 495,33 495,37 Z" fill="#ffe082" />
                        </svg>
                    @elseif(in_array($event->event_type, ['bautizo', 'comunion']))
                        <!-- Botanical Leaves Vine (Bautizo / Communion) -->
                        <svg viewBox="0 0 600 120" preserveAspectRatio="none" style="width:100%; height:120px; display:block;">
                            <!-- Sky background -->
                            <rect width="600" height="120" fill="var(--bg-sky)"/>
                            
                            <!-- Muted clouds -->
                            <path d="M 30 110 C 60 95, 110 95, 140 110" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="8" stroke-linecap="round" />
                            
                            <!-- Main vine branch string -->
                            <path d="M-10,30 C150,55 300,55 610,30" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="2" />
                            
                            <!-- Elegant leaves hanging along the vine -->
                            <path d="M60,37 C65,45 60,55 55,58 C50,55 45,45 50,37 Z" fill="var(--p1)" opacity="0.85" />
                            <path d="M60,37 C68,40 75,48 72,53 C69,58 60,50 60,37 Z" fill="var(--p3)" opacity="0.85" />
                            
                            <path d="M180,45 C185,53 180,63 175,66 C170,63 165,53 170,45 Z" fill="var(--p3)" opacity="0.85" />
                            <path d="M180,45 C172,48 165,56 168,61 C171,66 180,58 180,45 Z" fill="var(--p2)" opacity="0.85" />
                            
                            <path d="M300,48 C305,56 300,66 295,69 C290,66 285,56 290,48 Z" fill="var(--p1)" opacity="0.85" />
                            <path d="M300,48 C308,51 315,59 312,64 C309,69 300,61 300,48 Z" fill="var(--p3)" opacity="0.85" />
                            
                            <path d="M420,45 C425,53 420,63 415,66 C410,63 405,53 410,45 Z" fill="var(--p2)" opacity="0.85" />
                            <path d="M420,45 C412,48 405,56 408,61 C411,66 420,58 420,45 Z" fill="var(--p1)" opacity="0.85" />
                            
                            <path d="M540,37 C545,45 540,55 535,58 C530,55 525,45 530,37 Z" fill="var(--p3)" opacity="0.85" />
                            <path d="M540,37 C548,40 555,48 552,53 C549,58 540,50 540,37 Z" fill="var(--p2)" opacity="0.85" />
                            
                            <!-- Sparkly thin stars (spiritual vibes) -->
                            <path d="M120,20 L122,25 L127,27 L122,29 L120,34 L118,29 L113,27 L118,25 Z" fill="#fff" opacity="0.9" />
                            <path d="M480,20 L482,25 L487,27 L482,29 L480,34 L478,29 L473,27 L478,25 Z" fill="#fff" opacity="0.9" />
                        </svg>
                    @else
                        <!-- Bunting Flags garland (Cumpleaños / Default) -->
                        <svg viewBox="0 0 600 120" preserveAspectRatio="none" style="width:100%; height:120px; display:block;">
                            <!-- Sky background -->
                            <rect width="600" height="120" fill="var(--bg-sky)"/>
                            
                            <!-- Soft decorative clouds/blobs in background -->
                            <circle cx="90" cy="110" r="40" fill="rgba(255,255,255,0.3)" />
                            <circle cx="480" cy="100" r="30" fill="rgba(255,255,255,0.3)" />
                            
                            <!-- Garland String -->
                            <path d="M-10,25 Q150,75 300,50 T610,25" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" />
                            
                            <!-- Hanging Flags (Buntings) on curve -->
                            <polygon points="40,34 70,39 55,75" fill="var(--p1)" />
                            <polygon points="105,47 135,51 120,87" fill="var(--p3)" />
                            <polygon points="185,54 215,55 200,91" fill="var(--p2)" />
                            <polygon points="265,52 295,51 280,87" fill="var(--p1)" />
                            <polygon points="345,48 375,45 360,82" fill="var(--p3)" />
                            <polygon points="425,41 455,37 440,74" fill="var(--p2)" />
                            <polygon points="505,32 535,28 520,65" fill="var(--p1)" />
                            
                            <!-- Little floating stars or circles (confetti) in background -->
                            <circle cx="150" cy="30" r="3" fill="var(--p2)" opacity="0.7" />
                            <circle cx="310" cy="20" r="2" fill="var(--p3)" opacity="0.6" />
                            <circle cx="450" cy="25" r="3.5" fill="var(--p1)" opacity="0.7" />
                        </svg>
                    @endif
                    <!-- Cloud Divider Bottom -->
                    <svg viewBox="0 0 600 24" preserveAspectRatio="none" style="position:absolute; bottom:-1px; left:0; width:100%; height:24px; display:block; z-index:5;">
                        <path d="M0,24 Q50,0 100,24 T200,24 T300,24 T400,24 T500,24 T600,24 L600,24 L0,24 Z" fill="#ffffff" />
                    </svg>
                </div>

                <!-- 2. White Card Body containing the event details -->
                <div class="card-body">
                    @if($event->photo_1 || $event->photo_2)
                        <div class="user-photos">
                            @if($event->photo_1)
                                <img src="{{ Storage::url($event->photo_1) }}" alt="Foto 1" class="user-photo">
                            @endif
                            @if($event->photo_2)
                                <img src="{{ Storage::url($event->photo_2) }}" alt="Foto 2" class="user-photo">
                            @endif
                        </div>
                    @endif

                    <!-- Event Title (Script cursive font) -->
                    <h2 class="script-title">{{ $event->title }}</h2>

                    <!-- INVITACIÓN label -->
                    <h3 class="sub-invite">Invitación</h3>

                    <!-- Calligraphy Flourish Divider -->
                    <svg height="24" width="140" viewBox="0 0 120 24" class="flourish-divider">
                        <path d="M10,12 C30,2 40,22 60,12 C80,2 90,22 110,12 M60,4 C60,4 58,12 60,20 M35,12 C40,16 45,16 50,12 M70,12 C75,16 80,16 85,12" />
                    </svg>

                    <!-- Central Graphic: Reveal images OR Theme Character OR Fallback -->
                    @if($event->reveal_image_1 || $event->reveal_image_2)
                        <div class="reveal-pair">
                            @if($event->reveal_image_1)
                                <img src="{{ asset($event->reveal_image_1) }}" alt="Revelación 1" class="reveal-img reveal-img-1">
                            @endif
                            @if($event->reveal_image_2)
                                <img src="{{ asset($event->reveal_image_2) }}" alt="Revelación 2" class="reveal-img reveal-img-2">
                            @endif
                        </div>
                    @elseif($event->theme_character && $event->theme_character !== 'none')
                        <div style="margin: 10px auto 16px auto; width: 110px; height: 110px; overflow: hidden; border-radius: 50%; border: 3px solid var(--p1); background: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 18px rgba(0,0,0,0.05); animation: wiggle 2.5s ease-in-out infinite;">
                            <img src="{{ asset('images/themes/' . $event->theme_character . '.png') }}" alt="Theme character" style="max-width: 90%; max-height: 90%; object-fit: contain;">
                        </div>
                    @else
                        @if($event->event_type === 'cumple')
                            <!-- Elegant Cake SVG -->
                            <svg viewBox="0 0 100 90" style="width:70px; height:70px; margin: 10px auto 16px auto; display:block; color: var(--p1); opacity: 0.85;">
                                <path d="M50 20 L50 35 M50 15 C52 15, 53 18, 50 20 C47 18, 48 15, 50 15" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <rect x="35" y="35" width="30" height="20" rx="3" fill="none" stroke="currentColor" stroke-width="2.5" />
                                <rect x="25" y="55" width="50" height="22" rx="4" fill="none" stroke="currentColor" stroke-width="2.5" />
                                <line x1="20" y1="77" x2="80" y2="77" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                                <circle cx="42" cy="45" r="2" fill="currentColor" />
                                <circle cx="50" cy="45" r="2" fill="currentColor" />
                                <circle cx="58" cy="45" r="2" fill="currentColor" />
                                <circle cx="35" cy="66" r="2" fill="currentColor" />
                                <circle cx="50" cy="66" r="2" fill="currentColor" />
                                <circle cx="65" cy="66" r="2" fill="currentColor" />
                            </svg>
                        @elseif(in_array($event->event_type, ['bautizo', 'comunion']))
                            <!-- Elegant Dove SVG -->
                            <svg viewBox="0 0 100 90" style="width:80px; height:75px; margin: 10px auto 16px auto; display:block; color: var(--p1); opacity: 0.85;">
                                <path d="M25,50 C30,45 42,40 50,45 C58,35 68,25 78,35 C70,40 65,48 68,52 C73,50 82,45 88,48 C80,55 70,62 60,60 C55,62 48,68 40,70 C43,62 38,58 35,56 C30,58 20,60 15,55 Z" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M88,48 C91,44 94,45 96,43 C93,46 91,48 88,48" stroke="currentColor" stroke-width="1.8" fill="none" />
                            </svg>
                        @else
                            <!-- Footprints SVG -->
                            <svg viewBox="0 0 100 80" style="width:90px; height:70px; margin: 10px auto 16px auto; display:block; color: var(--p1); opacity: 0.85;">
                                <g transform="rotate(-10 35 45)">
                                    <path d="M30 42 C23 38, 20 48, 22 58 C24 69, 34 75, 38 69 C42 63, 39 55, 36 49 C33 43, 33 45, 30 42 Z" fill="currentColor"/>
                                    <circle cx="23.5" cy="30" r="5" fill="currentColor"/>
                                    <circle cx="31.5" cy="28.5" r="3.8" fill="currentColor"/>
                                    <circle cx="38" cy="31" r="3.3" fill="currentColor"/>
                                    <circle cx="43" cy="35" r="2.8" fill="currentColor"/>
                                    <circle cx="46" cy="41" r="2.5" fill="currentColor"/>
                                </g>
                                <g transform="rotate(10 65 45)">
                                    <path d="M70 42 C77 38, 80 48, 78 58 C76 69, 66 75, 62 69 C58 63, 61 55, 64 49 C67 43, 67 45, 70 42 Z" fill="currentColor"/>
                                    <circle cx="76.5" cy="30" r="5" fill="currentColor"/>
                                    <circle cx="68.5" cy="28.5" r="3.8" fill="currentColor"/>
                                    <circle cx="62" cy="31" r="3.3" fill="currentColor"/>
                                    <circle cx="57" cy="35" r="2.8" fill="currentColor"/>
                                    <circle cx="54" cy="41" r="2.5" fill="currentColor"/>
                                </g>
                            </svg>
                        @endif
                    @endif

                    <!-- Date & Time (Playfair serif elegant text) -->
                    @if($event->date)
                        <h4 class="date-title">{{ $event->date->translatedFormat('l d \d\e F · g:i A') }}</h4>
                    @endif

                    <!-- Location / Address -->
                    <p class="place-title">
                        {{ $event->place }}
                        @if($event->show_map)
                            <br>
                            <button type="button" class="btn-map-toggle" onclick="toggleMapPreview()">Ver mapa</button>
                        @endif
                    </p>

                    <!-- Interactive Map inside collapsible wrapper -->
                    @if($event->show_map)
                        <div id="map-wrapper" class="map-wrapper">
                            <div id="map" style="height:200px;border-radius:20px;
                                box-shadow:0 6px 16px rgba(0,0,0,.08);z-index:1;border:1px solid #e2d8f5;"></div>
                        </div>
                    @endif

                    <!-- Countdown -->
                    @if($event->show_countdown)
                        <div class="count" id="count">
                            <div><b id="d">0</b><small>días</small></div>
                            <div><b id="h">0</b><small>horas</small></div>
                            <div><b id="m">0</b><small>min</small></div>
                            <div><b id="s">0</b><small>seg</small></div>
                        </div>
                    @endif

                    <!-- Extra details (dress code, gift, etc) -->
                    @if($event->dress_code || $event->gift_info || $event->extra_info)
                        <div class="extra-details">
                            @if($event->dress_code)
                                <div class="extra-details-item">
                                    <div>
                                        <strong>Código de Vestimenta</strong>
                                        <p>{{ $event->dress_code }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($event->gift_info)
                                <div class="extra-details-item">
                                    <div>
                                        <strong>Sugerencia de Regalo</strong>
                                        <p>{{ $event->gift_info }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($event->extra_info)
                                <div class="extra-details-item">
                                    <div>
                                        <strong>Detalles Adicionales</strong>
                                        <p style="white-space: pre-line;">{{ $event->extra_info }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Confirmed guests count -->
                    @if($event->show_confirmed_count)
                        <div class="badge">{{ $confirmed }} personas confirmadas</div>
                        <br>
                    @endif

                    <!-- Action buttons -->
                    <div class="actions-container">
                        <button class="btn btn-main" onclick="openModal()">{{ $event->rsvp_button_text ?: 'Confirmar asistencia' }}</button>
                        @php
                            $shareText = $event->share_message ?: ($event->title . ' - ' . $event->place);
                        @endphp
                        <a class="btn btn-wa" target="_blank"
                           href="https://wa.me/?text={{ urlencode($shareText) }}">
                           Compartir
                        </a>
                        <a class="btn btn-maps" target="_blank"
                           href="https://www.google.com/maps/search/?api=1&query={{ $event->lat }},{{ $event->lng }}">Cómo llegar</a>
                    </div>
                </div>
            </div>

            @if($event->show_messages && $messages->count())
                <div class="msgs">
                    <h3>Mensajes de los invitados</h3>
                    @foreach($messages as $m)
                        <div class="msg"><b>{{ $m->name }}:</b> {{ $m->message }}</div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- Botón flotante al administrador -->
    <a href="{{ route('admin') }}" class="btn-admin-float" title="Administrar evento">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block;"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
    </a>

    <!-- Chatbot flotante para invitados -->
    @if($event->is_published || $isPreview)
        <div class="chat-bubble" onclick="toggleChat()" title="Asistente de IA">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <div class="chat-window" id="chat-window">
            <div class="chat-header">
                <span>Asistente del Evento</span>
                <span class="chat-close" onclick="toggleChat()">✕</span>
            </div>
            <div class="chat-messages" id="chat-messages">
                <div class="chat-msg bot">¡Hola! Soy el asistente virtual del evento. Puedes consultarme sobre la dirección, el horario, sugerencias de regalo o código de vestimenta.</div>
            </div>
            <div class="chat-input-area">
                <input type="text" id="chat-input" class="chat-input" placeholder="Pregunta algo aquí..." onkeydown="if(event.key==='Enter') sendChatMessage()">
                <button class="chat-send" onclick="sendChatMessage()">Enviar</button>
            </div>
        </div>
    @endif

    <!-- Modal RSVP -->
    <div class="modal {{ $errors->any() ? 'open' : '' }}" id="modal">
        <div class="form">
            <span class="close" onclick="closeModal()">✕</span>
            <h3>Confirma tu asistencia</h3>
            <form method="POST" action="{{ route('rsvp') }}">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <label>Tu nombre</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej: Frank" required>
                @error('name')<div class="err">{{ $message }}</div>@enderror

                <input type="hidden" name="attending" value="1">

                <label>Acompañantes</label>
                <input type="hidden" name="companions" id="companions-value" value="{{ old('companions', 0) }}">
                <div class="companions-tags">
                    <button type="button" class="companion-tag" data-val="0" onclick="selectCompanionTag(0)">0</button>
                    <button type="button" class="companion-tag" data-val="1" onclick="selectCompanionTag(1)">1</button>
                    <button type="button" class="companion-tag" data-val="2" onclick="selectCompanionTag(2)">2</button>
                    <button type="button" class="companion-tag" data-val="3" onclick="selectCompanionTag(3)">3</button>
                    <button type="button" class="companion-tag" data-val="4" onclick="selectCompanionTag(4)">4</button>
                    <button type="button" class="companion-tag" data-val="5" onclick="selectCompanionTag(5)">5+</button>
                </div>

                <label style="margin-top:14px;">Mensaje (opcional)</label>
                <textarea name="message" rows="2" placeholder="Deja un saludo...">{{ old('message') }}</textarea>

                <br><br>
                <button class="btn btn-main" style="width:100%">Enviar</button>
            </form>
        </div>
    </div>

    <script>
        function selectCompanionTag(val) {
            document.getElementById('companions-value').value = val;
            const tags = document.querySelectorAll('.companion-tag');
            tags.forEach(tag => {
                if (parseInt(tag.getAttribute('data-val')) === val) {
                    tag.classList.add('active');
                } else {
                    tag.classList.remove('active');
                }
            });
        }

        window.addEventListener('DOMContentLoaded', () => {
            const currentVal = parseInt(document.getElementById('companions-value').value) || 0;
            selectCompanionTag(currentVal);
        });

        function openModal(){document.getElementById('modal').classList.add('open')}
        function closeModal(){document.getElementById('modal').classList.remove('open')}

        // Lógica de Toggle Chatbot
        function toggleChat() {
            const win = document.getElementById('chat-window');
            if (win.style.display === 'none' || !win.style.display) {
                win.style.display = 'flex';
                document.getElementById('chat-input').focus();
            } else {
                win.style.display = 'none';
            }
        }

        // Enviar Mensaje al Chatbot
        async function sendChatMessage() {
            const input = document.getElementById('chat-input');
            const text = input.value.trim();
            if (!text) return;
            
            input.value = '';
            
            const container = document.getElementById('chat-messages');
            const uMsg = document.createElement('div');
            uMsg.className = 'chat-msg user';
            uMsg.textContent = text;
            container.appendChild(uMsg);
            container.scrollTop = container.scrollHeight;
            
            const typingMsg = document.createElement('div');
            typingMsg.className = 'chat-msg bot';
            typingMsg.textContent = 'Escribiendo...';
            container.appendChild(typingMsg);
            container.scrollTop = container.scrollHeight;
            
            try {
                const response = await fetch('{{ route("ai.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: text, event_id: {{ $event->id }} })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    typingMsg.textContent = data.reply || 'No obtuve respuesta.';
                } else {
                    typingMsg.textContent = 'Lo siento, ocurrió un error al procesar tu pregunta.';
                }
            } catch (e) {
                typingMsg.textContent = 'No pude conectarme al servidor.';
            }
            container.scrollTop = container.scrollHeight;
        }

        // Mapa de ubicación
        let map;
        @if($event->show_map)
            function initMap() {
                if (map) return;
                const lat = parseFloat("{{ $event->lat ?? -13.516799 }}".replace(',', '.'));
                const lng = parseFloat("{{ $event->lng ?? -71.978817 }}".replace(',', '.'));
                map = L.map('map',{scrollWheelZoom:false}).setView([lat,lng],15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {attribution:'© OpenStreetMap'}).addTo(map);
                L.marker([lat,lng]).addTo(map).bindPopup("{{ addslashes($event->place) }}").openPopup();
            }

            function toggleMapPreview() {
                const wrapper = document.getElementById('map-wrapper');
                const btn = document.querySelector('.btn-map-toggle');
                wrapper.classList.toggle('open');
                
                if (wrapper.classList.contains('open')) {
                    btn.innerHTML = '🗺️ Ocultar mapa';
                    initMap();
                    setTimeout(() => {
                        if (map) map.invalidateSize(true);
                    }, 400);
                } else {
                    btn.innerHTML = '🗺️ Ver mapa';
                }
            }
        @endif

        // Cuenta regresiva
        @if($event->show_countdown && $event->date)
            const target = new Date("{{ $event->date->toIso8601String() }}").getTime();
            function tick(){
                const now = Date.now(), diff = target - now;
                const countEl = document.getElementById('count');
                if (!countEl) return;
                
                if(diff<0){
                    countEl.innerHTML='<div style="min-width:auto;padding:14px 24px;width:100%;border-radius:16px;">🎉 ¡Hoy es el gran día! 🎉</div>';
                    return;
                }
                const d=Math.floor(diff/86400000),h=Math.floor(diff%86400000/3600000),
                      m=Math.floor(diff%3600000/60000),s=Math.floor(diff%60000/1000);
                
                const dEl = document.getElementById('d');
                const hEl = document.getElementById('h');
                const mEl = document.getElementById('m');
                const sEl = document.getElementById('s');
                
                if (dEl) dEl.textContent=d;
                if (hEl) hEl.textContent=h;
                if (mEl) mEl.textContent=m;
                if (sEl) sEl.textContent=s;
            }
            tick(); setInterval(tick,1000);
        @endif

        // Confeti al cargar / confirmar
        function confetti(){
            const cs=['#d4a3b3','#e5c1cd','#6e5a63','#9fb8c7','#cbdacb','#a8bda8'];
            for(let i=0;i<80;i++){
                const c=document.createElement('div');
                c.style.cssText=`position:fixed;top:-10px;left:${Math.random()*100}%;
                    width:10px;height:10px;background:${cs[i%6]};z-index:99;
                    border-radius:${Math.random()>.5?'50%':'0'};pointer-events:none;
                    transform:rotate(${Math.random()*360}deg)`;
                document.body.appendChild(c);
                const dur=2000+Math.random()*2000;
                c.animate([{transform:`translateY(0) rotate(0)`,opacity:1},
                    {transform:`translateY(105vh) rotate(720deg)`,opacity:.8}],
                    {duration:dur,easing:'ease-in'});
                setTimeout(()=>c.remove(),dur);
            }
        }
        
        // Ejecutar confeti solo si cargó la invitación o si se envió rsvp
        @if($event->is_published || $isPreview)
            confetti();
        @endif
        @if(session('success')) confetti(); @endif
    </script>
</body>
</html>
